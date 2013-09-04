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
	if(is_file('../config/pass.txt')){
		$pass= file('../config/pass.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		file_put_contents('../config/pass.php','<?php $adminpassword=\''.$pass[0].'\'; ?>');
		unlink('../config/pass.txt');
	}
	require_once '../translator/class.translation.php';
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('../translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);	ini_set('session.auto_start', '0');
	ini_set('session.hash_function', 'sha512');
	ini_set('session.entropy_file', '/dev/urandom');
	ini_set('session.entropy_length', '512');
	ini_set('session.save_path', 'session');
	ini_set('session.gc_probability', '1');
	ini_set('session.cookie_httponly', '1');
	
	ini_set('session.use_only_cookies', '1');
	ini_set('session.use_trans_sid', '0');
	session_name("RazorphynExtendedComingsoon");
	if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		ini_set('session.cookie_secure', '1');
	}
	if(isset($_COOKIE['RazorphynExtendedComingsoon']) && !is_string($_COOKIE['RazorphynExtendedComingsoon']) || !preg_match('/^[a-z0-9]{26,40}$/',$_COOKIE['RazorphynExtendedComingsoon']))
		setcookie(session_name(),'invalid',time()-3600);
	session_start(); 

	//Session Check
	if(isset($_SESSION['time']) && time()-$_SESSION['time']<=1800)
		$_SESSION['time']=time();
	else if(isset($_SESSION['id']) && !isset($_SESSION['time']) || isset($_SESSION['time']) && time()-$_SESSION['time']>1800){
		session_unset();
		session_destroy();
	}
	else if(isset($_SESSION['ip']) && $_SESSION['ip']!=retrive_ip()){
		session_unset();
		session_destroy();
	}
	
	if(!is_file('../config/pass.php') || !is_dir('../config') && !isset($_SESSION['created']) && $_SESSION['created']==true){header('Location: datacheck.php');exit();}

	require_once ('../config/pass.php');
	$fileconfig='../config/config.txt';
	$socialfile='../config/social.txt';
	$filemail= '../config/mail.txt';
	$filefnmail='../config/fnmail.txt';
	$filefnmessage= '../config/fnmessage.txt';
	$filefnfooter= '../config/footermail.txt';
	$dir='../config/mails';
	
	if(!isset($adminpassword) || !is_dir('../config') && !isset($_SESSION['created']) && $_SESSION['created']==true){header('Location: datacheck.php');exit();}
	
	if (isset($var[10]))date_default_timezone_set($var[10]);
	
	/*login*/
	if (isset($_POST['loginb'])){ 
		if(md5($_POST['pwd'])!=$adminpassword && hash('whirlpool',$_POST['pwd'])!=$adminpassword){
			$acc=false;
		}
		else if(md5($_POST['pwd'])==$adminpassword){
			$adminpassword=hash('whirlpool',$_POST['pwd']);
			$fs=fopen('../config/pass.php',"w+");
			fwrite($fs,'<?php $adminpassword=\''.$adminpassword.'\'; ?>');
			fclose($fs);
			$_SESSION['views']=1946;
			$_SESSION['time']=time();
			$_SESSION['ip']=retrive_ip();
			$adminpassword=hash('whirlpool',$_POST['pwd']);
			if(isset($acc)) unset($acc);
		}
		else if (hash('whirlpool',$_POST['pwd'])==$adminpassword){
			$_SESSION['views']=1946;
			$_SESSION['time']=time();
			$_SESSION['ip']=retrive_ip();
			if(isset($acc)) unset($acc);
			header('Location: '.$_SERVER['REQUEST_URI']);
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
	
	$fnmail= file($filefnmail, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$footermail= file_get_contents($filefnfooter);

	/*Update Database*/
	if(!is_dir($dir)){ if(mkdir($dir,0755))file_put_contents($dir.'/.htaccess','Deny from All'."\n".'IndexIgnore *'); };
	if(is_file($filemail)){
		$mailfile=file($filemail,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$count=count($mailfile);
		if(isset($mailfile[0])){
			for($i=0;$i<$count;$i++){
				$mailfile[$i]=explode('###next<!>nf###',$mailfile[$i]);
				if(!isset($mailfile[0][7])){
					for($j=0;$j<$count;$j++){
						if(!isset($mailfile[$j][1])) $mailfile[$j][1]='';if(!isset($mailfile[$j][2])) $mailfile[$j][2]='';if(!isset($mailfile[$j][3])) $mailfile[$j][3]='';if(!isset($mailfile[$j][4])) $mailfile[$j][4]='';if(!isset($mailfile[$j][5])) $mailfile[$j][5]='';if(!isset($mailfile[$j][6])) $mailfile[$j][6]='';
						if(!isset($mailfile[$j][7]))
							$mailfile[$j][7]=$j;
					}
				}
				file_put_contents($dir.'/'.$mailfile[$i][2],implode("\n",$mailfile[$i]));
			}
		}
		unlink($filemail);
	}
	
	/*Read Files*/
	$scan=array_values(array_diff(scandir($dir), array('..', '.','.htaccess')));
	$mailist=array();
	$count=count($scan);
	if(isset($scan[0])){
		for($i=0;$i<$count;$i++){
			$mailist[$i]=file($dir.'/'.$scan[$i],FILE_IGNORE_NEW_LINES);
			$mailist[$i]=array($mailist[$i][2],$mailist[$i][7]);
		}
	}

	if(isset($_POST['fcheck'])){
		unset($_POST['fcheck']);
		header('Location: datacheck.php');
	}
}
?>
<!DOCTYPE html>
<html  lang="<?php echo $lang; ?>">
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
    <title><?php $translate->__("Send Mail",false); ?></title>
	
	<!--[if lt IE 9]><script src="../js/html5shiv-printshiv.js"></script><![endif]-->
	<link rel="stylesheet" href="../css/bootstrap.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.css" />
    <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
	<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
	
	<link rel="stylesheet" href="../lib/DataTables/css/jquery.dataTables.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	
	<script type="text/javascript"  src="../js/jquery-1.10.2.js"></script>
	<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
	
	</head>
	<body>
	<div class="container">
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
								<li class="dropdown" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Setup",false); ?><b class="caret"></b></a>
									<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
										<li role="presentation"><a href="index.php" tabindex="-1" role="menuitem"><?php $translate->__("Site",false); ?></a></li>
										<li role="presentation"><a href="mail_setting.php" tabindex="-1" role="menuitem"><?php $translate->__("Mail",false); ?></a></li>
									</ul>
								</li>
								<li class="dropdown" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Mail",false); ?><b class="caret"></b></a>
									<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
										<li role="presentation"><a href="mail.php" tabindex="-1" role="menuitem"><?php $translate->__("Send Mail",false); ?></a></li>
										<li role="presentation"><a href="managesched.php" tabindex="-1" role="menuitem"><?php $translate->__("Manage Scheduled Mail",false); ?></a></li>
									</ul>
								</li>
								<li><a href='managesub.php'><?php $translate->__("Manage Subscriptions",false); ?></a></li>
								<li><a href='postnews.php'><?php $translate->__("Post News",false); ?></a></li>
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
			<form name="sendmailform" id="sendmailform" method="post"  class='formcor '>
				<h2 class='titlesec'><?php $translate->__("Contact users",false); ?></h2>
				<div class='row'>
					<div class='span3'><label><?php $translate->__("Send Later?",false); ?></label></div>
					<div class='span1'><label class="radio"><input type="radio" name="sched" value="yes"  onclick='javascript:$(".scheduled").css("display","block");$("#sendate").attr("required","required");$("#sentime").attr("required","required");' /><?php $translate->__("Yes",false); ?></label></div>
					<div class='span1'><label class="radio"><input type="radio" name="sched" value="no"  onclick='javascript:$(".scheduled").css("display","none");$("#sendate").removeAttr("required");$("#sentime").removeAttr("required");' checked /><?php $translate->__("No",false); ?></label></div>
				</div>
				<div class='row scheduled' style='display:none'>
					<div class='span1'><label><?php $translate->__("Date:",false); ?></label></div><div class='span3'><input type="text" id="sendate" name="sendate" /></div>
					<div class='span2'><label><?php $translate->__("Time:",false); ?> (hh:mm)</label></div><div class='span3'><input type="time" id="sentime" name="sentime" pattern='([01]?[0-9]|[2]?[0-3]):[0-5][0-9]' /></div>
				</div>
				<br/><br/>
				<div class='row-fluid'>
					<div class='span3'><label><?php $translate->__("Do you want to contact all the users?",false); ?></label></div>
					<div class='span1'><label class="radio"><input type="radio" name="shtb" value="yes" onclick='javascript:$("#conttab").css("display","none");' checked><?php $translate->__("Yes",false); ?></label></div>
					<div class='span1'><label class="radio"><input type="radio" name="shtb" value="no" onclick='javascript:$("#conttab").css("display","block");'><?php $translate->__("No",false); ?></label></div>
				</div>
				<div id='conttab'>
					<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mails" width="100%">
						<thead>
						<tr>
							<th><?php $translate->__("Position",false); ?></th>
							<th><?php $translate->__("Send Mail",false); ?></th>
							<th><?php $translate->__("Mail",false); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php $count=count($mailist);if($count>0){for($i=0;$i<$count;$i++)echo '<tr><td><input type="checkbox" class="mal" name="semail[]" value="'.$mailist[$i][1].'"/></td><td>'.($i+1).'</td><td>'.$mailist[$i][0].'</td></tr>';} ?>
						</tbody>
					</table>
				</div>
				<div class='row'>
					<div class='span4'><label><?php $translate->__("Sender:",false); ?></label><input type="text" id="sender" name="sender" <?php if(isset($var[7]) && $var[7]!='**@****nullo**@****') echo 'value="'.$var[7].'"'; ?> required/></div>
					<div class='span4'><label><?php $translate->__("Object:",false); ?></label><input	type="text" id="object" name="object" required/></div>
				</div>
					<label><?php $translate->__("Message:",false); ?></label><textarea id='message' name='message' rows='10' cols='100' required></textarea>
					<br/><br/>
					<label><?php $translate->__("Use Different Footer:",false); ?></label><textarea id='footerfn' name='footerfn' class='footerfn' rows='10' cols='100'><?php if(isset($footermail) && $footermail!='**@****nullo**@****') echo $footermail; ?></textarea>
				<br/><br/>
				<input onclick='javascript:return false;' type="submit" name="sendmail" id="sendmail" value="<?php $translate->__("Send Mail",false); ?>" class="btn btn-success"/>
			</form>
			
			<form name="logoutfor" id="logoutfor"  method="post"  class='formcor'>
				<input type="submit" name="logout" id="logout" value="Logout" class="btn btn-danger"/>
			</form>
		</div>
		<?php } else { ?>
		<div class='row-fluid main'>
			<form name="formdata" id="formdata" method="post"  class='formcor form-inline'>
				<h2 class='titlesec'>Login</h2>
					<!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" /><![endif]-->
					<?php if(isset($acc) && $acc==false){ ?>
					<div class='row-fluid'><div class='span12'><p><?php $translate->__("Wrong Password",false); ?><p></div></div>
					<?php } ?>
				<div class='row-fluid'>
					<div class='span2'><label>Password</label></div>
					<div class='span4'><input type="password" id="pwd" name="pwd" placeholder="Password"></div>
				</div>
				<br/><br/>
				<input type="submit" name="loginb" id="loginb" value="Login" class="btn btn-success"/>
			</form>
		</div>
		<?php } 
		?>
	</div>
	<?php if(isset($_SESSION['views']) && $_SESSION['views']==1946 ){ ?>
		<script type="text/javascript"  src="../js/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript"  src="../js/jquery.validate.min.js"></script>
		<script type="text/javascript"  src="../lib/DataTables/js/jquery.dataTables.js"></script>
		<script type="text/javascript"	src="../ckeditor/ckeditor.js"></script>
		<script type="text/javascript"  src="../js/noty/jquery.noty.js"></script>
		<script type="text/javascript"  src="../js/noty/layouts/top.js"></script>
		<script type="text/javascript"  src="../js/noty/themes/default.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			CKEDITOR.replace('message');
			CKEDITOR.replace('footerfn');
			$('#mails').dataTable({
				"sDom": "<<'span6'l><'span6'f>r>t<<'span6'i><'span6'p>>",
				"aoColumns": [
					{ "sTitle": "Send Mail","bSortable": false,"bSearchable":false,'sWidth':'150px' },
					{ "sTitle": "Position",'sWidth':'60px' },
					{ "sTitle": "Mail" }
				]
			});

			$('#sendate').datepicker({ dateFormat: 'yy-mm-dd' });
			var dateArray = new String("<?php echo date("Y-m-d");?>").split('-');
			var dateObject = new Date(dateArray[0], dateArray[1]-1, dateArray[2]);
			$("#sendate" ).datepicker("option", "minDate", dateObject);

			$('#sendmail').click(function(){
				var sender=$('#sender').val().replace(/\s+/g,' ');
				var object=$('#object').val().replace(/\s+/g,' ');
				var message=CKEDITOR.instances.message.getData().replace(/\s+/g,' ');
				var footerfn=CKEDITOR.instances.footerfn.getData().replace(/\s+/g,' ');
				var shtb=$('input[type=radio][name="shtb"]:checked').val();
				var scheduled=$('input[type=radio][name="sched"]:checked').val();
				var sdate=$('#sendate').val().replace(/\s+/g,'');
				var stime=$('#sentime').val().replace(/\s+/g,'');
				var semail = new Array();
				$('input[name="semail[]"]:checked').each(function() {
					semail.push($(this).val());
				});
				if(sender.replace(/\s+/g,'')!='' && object.replace(/\s+/g,'')!='' && shtb.replace(/\s+/g,'')!=''){
					var request= $.ajax({
						type: 'POST',
						url: 'function.php',
						data: {act:'send_mail_bk',sender:sender,object:object,message:message,footerfn:footerfn,semail:semail,shtb:shtb,sched:scheduled,sdate:sdate,stime:stime},
						dataType : 'json',
						success : function (data) {
							if(data[0]=='Sent')
								var n = noty({text: "<?php echo $translate->__("The Server is Processing Your Request",true); ?>",type:'success',timeout:9000});
							else if(data[0]=='Empty')
								var n = noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
							else
								var n = noty({text: "<?php echo $translate->__("A problem has occured,please try again",true); ?>",type:'error',timeout:9000});
						}
					});
					request.fail(function(jqXHR, textStatus){var n = noty({text: textStatus,type:'error',timeout:9000});});
				}
				else
					var n = noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
				return false;
			});
			$("#sendmailform").validate({
				rules:{sender: "required",message: "required",object:"required"},
				messages:{sender: "Complete field",message: "Complete field",object: "Complete field"}
			});
		});
		</script>
	<?php } ?>
	</body>
</html>
<?php function retrive_ip(){if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];}elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip=$_SERVER['REMOTE_ADDR'];}return $ip;} ?>