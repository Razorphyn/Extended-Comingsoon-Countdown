<?php 
/**
 * Razorphyn
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future.
 *
 * @copyright  Copyright (c) 2013 Razorphyn
 *
 * Extended Coming Soon Countdown
 *
 * @author     	Razorphyn
 * @Site		http://razorphyn.com/
 */

	umask(002);
	if(!is_dir('session')) mkdir('session',0767);
	require_once '../translator/class.translation.php';
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('../translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);	ini_set('session.auto_start', '0');
	ini_set('session.auto_start', '0');
	ini_set('session.hash_function', 'sha512');
	ini_set('session.entropy_file', '/dev/urandom');
	ini_set('session.entropy_length', '512');
	ini_set('session.save_path', 'session');
	ini_set('session.gc_probability', '1');
	ini_set('session.cookie_httponly', '1');
	
	ini_set('session.use_only_cookies', '1');
	ini_set('session.use_trans_sid', '0');
	session_name("RazorphynExtendedComingsoon");
	session_start();
	
	$fileconfig='../config/config.txt';
	$passfile='../config/pass.txt';
	$filenews= '../config/news.txt';
	
	if(!is_file($passfile) || !is_dir('../config'))
		header('Location: datacheck.php');
	
	$pass= file($passfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	/*login*/
	if (isset($_POST['loginb'])){ 
		if(md5($_POST['pwd'])!=$pass[0] && hash('whirlpool',$_POST['pwd'])!=$pass[0]){
			$acc=false;
		}
		else if(md5($_POST['pwd'])==$pass[0]){
			$fs=fopen($passfile,"w+");
			fwrite($fs,hash('whirlpool',$_POST['pwd']));
			fclose($fs);
			$_SESSION['views']=1946;
			$pass[0]=hash('whirlpool',$_POST['pwd']);
			if(isset($acc)) unset($acc);
		}
		else if (hash('whirlpool',$_POST['pwd'])==$pass[0]){
			$_SESSION['views']=1946;
			if(isset($acc)) unset($acc);
		}
	}
	/*end login*/

if(isset($_SESSION['views']) && $_SESSION['views']==1946){
	if(isset($_POST['logout'])){
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_destroy();
		header('Location: '.$_SERVER['REQUEST_URI']);
	}
	
	$var = file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if (isset($var[10]))date_default_timezone_set(rtrim($var[10]));
	if(count($var)>1){
		$var[1]=explode(':',$var[1]);
		$var[3]=explode(':',$var[3]);
	}
	
	/*
	0	datai
	1	orai
	2	dataf
	3	oraf
	4	siteurl
	5	site title
	6	perc
	7	email
	8	show contact
	9	show subscribe
	10	TimeZone
	11	Unsubscribe
	12	Domain
	13	Folder
	*/
	
	
	if(isset($_POST['fcheck'])){
		header('Location: datacheck.php');
	}
}
	
?>
<!DOCTYPE html>
<html  lang="<?php echo $lang; ?>">
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
    <title><?php $translate->__("Post News",false); ?></title>
	
	
	<!--[if lt IE 9]><script src="../js/html5shiv-printshiv.js"></script><![endif]-->
	<link rel="stylesheet" href="../css/bootstrap.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.css" />
    <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
	<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
	
	<script type="text/javascript"  src="../js/jquery-1.10.2.js"></script>
	<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
	
	</head>
	<body>
	<div class='container'>
		<?php if(isset($_SESSION['views']) && $_SESSION['views']==1946 ){ ?>
		<div class="masthead">
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar hidden-desktop" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
						<a class="brand" href='index.php'><?php $translate->__("Administration",false); ?></a>
						<div class="nav-collapse navbar-responsive-collapse collapse">
							<ul class="nav">
								<li ><a href='index.php'><?php $translate->__("Setup",false); ?></a></li>
								<li class="dropdown">
									<a id="drop1" class="dropdown-toggle" data-toggle="dropdown" href="#">
										<?php $translate->__("Mail",false); ?>
										<b class="caret"></b>
									</a>
									<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
										<li role="presentation">
											<a href="mail.php" tabindex="-1" role="menuitem"><?php $translate->__("Send Mail",false); ?></a>
										</li>
										<li role="presentation">
											<a href="managesched.php" tabindex="-1" role="menuitem"><?php $translate->__("Manage Scheduled Mail",false); ?></a>
										</li>
									</ul>
								</li>
								<li><a href='managesub.php'><?php $translate->__("Manage Subscriptions",false); ?></a></li>
								<li class='active'><a href='postnews.php'><?php $translate->__("Post News",false); ?></a></li>
								<li><a href='managenews.php'><?php $translate->__("Manage News",false); ?></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='row-fluid main'>
			<form name="ckform" id="ckform"  method="post"  class='formcor form-inline'>
			<h2 class='titlesec'><?php $translate->__("Database Files Checking",false); ?></h2>
				<input type="submit" name="fcheck" id="fcheck" value="<?php $translate->__("Check Database Files",false); ?>" class="btn"/>
			</form>
			
			<form name="formnews" id="formnews" class='formcor'>
			<h2 class='titlesec'><?php $translate->__("Post a News",false); ?></h2>
				<label><?php $translate->__("News Title:",false); ?></label><input type="text" id="tnews" name="tnews"/>
				<label><?php $translate->__("News:",false); ?></label><textarea type="text" id="nnews" name="nnews"></textarea>
				<br/><br/>
				<input onclick='javascript:return false;' type="submit" name="bnews" id="bnews" value="Post News" class="btn btn-success"/>
			</form>
		</div>
		<?php } else { ?>
		<div class='row-fluid main'>
			<form name="formdata" id="formdata" method="post"  class='formcor form-inline'>
				<h2 class='titlesec'>Login</h2>
					<!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" /><![endif]-->
				<div class='row'>
					<div class='span1'><label>Password</label></div>
					<div class='span3'><input type="password" id="pwd" name="pwd" placeholder="Password"></div>
				</div>
				<br/><br/>
				<input type="submit" name="loginb" id="loginb" value="Login" class="btn btn-success"/>
			</form>
		</div>
		<?php } 
		?>
	</div>
	
	<?php if(isset($_SESSION['views']) && $_SESSION['views']==1946 ){ ?>
		<script type="text/javascript"  src="../ckeditor/ckeditor.js"></script>
		<script type="text/javascript"  src="../js/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript"  src="../js/jquery.validate.min.js"></script>
		<script type="text/javascript"  src="../js/timezoneautocomplete.js"></script>
		<script type="text/javascript"  src="../js/noty/jquery.noty.js"></script>
		<script type="text/javascript"  src="../js/noty/layouts/top.js"></script>
		<script type="text/javascript"  src="../js/noty/themes/default.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			CKEDITOR.replace('nnews');
			$("#formnews").validate(
			{
				rules:{tnews:"required",nnews: "required"},
				messages:{tnews: "Please complete this field",nnews: "Please complete this field"}
			});
			
			$('#bnews').click(function(){
				var tit=$('#tnews').val().replace(/\s+/g,' ');
				var message=CKEDITOR.instances.nnews.getData().replace(/\s+/g,' ');
				if(tit.replace(/\s+/g,'')!='' && message.replace(/\s+/g,'')!=''){
					var request= $.ajax({
						type: 'POST',
						url: 'function.php',
						data: {act:'post_news',tnews:tit,nnews:message},
						dataType : 'json',
						success : function (data) {
							if(data[0]=='Added')
								var n = noty({text: '<?php echo $translate->__("The New has been created",true); ?>',type:'success',timeout:9000});
							else if(data[0]=='Error')
								var n = noty({text: '<?php echo $translate->__("A problem has occured,please try again",true); ?>',type:'error',timeout:9000});
							else if(data[0]=='Empty')
								var n = noty({text: '<?php echo $translate->__("Please Complete all the fields",true); ?>',type:'error',timeout:9000});
						}
					});
					request.fail(function(jqXHR, textStatus){var n = noty({text: textStatus,type:'error',timeout:9000});});
				}
				else
					var n = noty({text: '<?php echo $translate->__("Please Complete all the fields",true); ?>',type:'error',timeout:9000});
				return false;
			});
		});
		</script>
	<?php } ?>
	</body>
</html>