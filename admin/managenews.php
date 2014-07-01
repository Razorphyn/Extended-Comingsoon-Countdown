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
	$filenews= '../config/news.txt';
	
	if(!isset($adminpassword) || !is_dir('../config') && !isset($_SESSION['created']) && $_SESSION['created']==true){header('Location: datacheck.php');exit();}
	
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
	if (isset($var[10]))date_default_timezone_set($var[10]);
	$var = file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	if(count($var)>1){
		$var[1]=explode(':',$var[1]);
		$var[3]=explode(':',$var[3]);
	}
	
	$news = file($filenews, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$news=array_reverse($news);

	if(isset($_GET['act']) && isset($_GET['id']) && $_GET['act']=='del'){
		$id=$_GET['id'];
						
		unset($news[$id]);
		unset($news[$id+1]);
		unset($news[$id+2]);
		$news=array_reverse($news);
		$fs=fopen($filenews,"w+");
		for($i=0;$i< count($news);$i++)
			($i< count($news)-1 && $news[$i]!='')? fwrite($fs,$news[$i]."\n"):fwrite($fs,$news[$i]);	
		fclose($fs);
		unset($_GET['act']);
		header('Location: managenews.php');
	}
	
	/*Edit News*/
	if(isset($_POST['bnews'])){
		$id=$_POST['id'];
		
		$news[$id]=normalize_str(rtrim(preg_replace('/\s+/',' ',$_POST['nnews'])));
		$news[$id+2]=normalize_str(rtrim(preg_replace('/\s+/',' ',$_POST['tnews'])));
		$news=array_reverse($news);
		
		$fs=fopen($filenews,"w+");
		for($i=0;$i<count($news);$i++)
			($i==count($news)-1)? fwrite($fs,$news[$i]):fwrite($fs,$news[$i]."\n");
		fclose($fs);
		unset($_GET['act']);
		header('Location: managenews.php');
	}
	/*end edit news*/
	
	
	if(isset($_POST['fcheck'])){
		header('Location: datacheck.php');
	}
	
}

	function normalize_str($str){
		$invalid = array('Š'=>'&#352;', 'š'=>'&#353;', 'Ð'=>'&ETH;', 'Ž'=>'&#381;', 'ž'=>'&#382;',
		'À'=>'&Agrave;', 'Á'=>'&Aacute;', 'Â'=>'&Acirc;', 'Ã'=>'&Atilde;',
		'Ä'=>'&Auml;', 'Å'=>'&Aring;', 'Æ'=>'&AElig;', 'Ç'=>'&Ccedil;', 'È'=>'&Egrave;', 'É'=>'&Eacute;', 'Ê'=>'&Ecirc; ', 'Ë'=>'&Euml;',
		'Ì'=>'&Igrave;', 'Í'=>'&Iacute;', 'Î'=>'&Icirc;', 'Ï'=>'&Iuml;', 'Ñ'=>'&Ntilde;', 'Ò'=>'&Ograve;', 'Ó'=>'&Oacute;', 'Ô'=>'&Ocirc;',
		'Õ'=>'&Otilde;', 'Ö'=>'&Ouml;', 'Ø'=>'&Oslash;', 'Ù'=>'&Ugrave;', 'Ú'=>'&Uacute;', 'Û'=>'&Ucirc;', 'Ü'=>'&Uuml;', 'Ý'=>'&Yacute;',
		'Þ'=>'&THORN;', 'ß'=>'&szlig;', 'à'=>' &agrave;', 'á'=>'&aacute;', 'â'=>'&acirc;', 'ã'=>'&atilde;', 'ä'=>'&auml;', 'å'=>'&aring;',
		'æ'=>'&aelig;', 'ç'=>'&ccedil;', 'è'=>'&egrave;', 'é'=>'&eacute;', 'ê'=>'&ecirc; ',  'ë'=>'&euml;', 'ì'=>'&igrave;', 'í'=>'&iacute;',
		'î'=>'&icirc;', 'ï'=>'&iuml;', 'ð'=>'&eth;', 'ñ'=>'&ntilde;', 'ò'=>'&ograve;', 'ó'=>'&oacute;', 'ô'=>'&ocirc;', 'õ'=>'&otilde;',
		'ö'=>'&ouml;', 'ø'=>'&oslash;', 'ù'=>'&ugrave;', 'ú'=>'&uacute;', 'û'=>'&ucirc;','ü'=>'&uuml;', 'ý'=>'&yacute;', 'þ'=>'&thorn;');
			 
		$str = str_replace(array_keys($invalid), array_values($invalid), $str);
		return $str;
	}
	
?>
<!DOCTYPE html>
<html  lang="<?php echo $lang; ?>">
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
    <title><?php $translate->__("Manage News",false); ?></title>
	
	
	<!--[if lt IE 9]><script src="../js/html5shiv-printshiv.js"></script><![endif]-->
	<link rel="stylesheet" href="../css/bootstrap.min.css" />
   
	<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
	
	<link rel="stylesheet" href="../lib/DataTables/css/jquery.dataTables.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	
	<script type="text/javascript"  src="../js/jquery.js"></script>
	<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
	
	</head>
	<body>
		<?php if(isset($_SESSION['views']) && $_SESSION['views']==1946 ){ ?>
		<header>
			<div class="container">
				<nav class="navbar navbar-default" role="navigation">
					<div class="container-fluid">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href='index.php'><?php $translate->__("Administration",false); ?></a>
						</div>
									
						<div class="nav-collapse" id='navbar-collapse'>
							<ul class="nav navbar-nav">
								<li class="dropdown" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Setup",false); ?> <b class="caret"></b></a>
									<ul class="dropdown-menu" role="menu">
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
								<li><a href='managenews.php' class='active'><?php $translate->__("Manage News",false); ?></a></li>
							</ul>
						</div>
					</div>
				</nav>
			</div>
		</header>
		<div class='main container'>
			<div  class='formcor'>
				<?php if(isset($_GET['act'])&& isset($_GET['id']) && $_GET['act']=='edit'){ $id=$_GET['id']; ?>
					<form name="formnews" id="formnews"  method="post" value='Data' >
						<h2 class='titlesec'><?php $translate->__("Edit News",false); ?></h2>
							<input  type="hidden" id='id' name='id' value='<?php echo $id; ?>'/>
							<div class='form-group'>
								<div class='row'>
									<div class='col-xs-12'><label><?php $translate->__("News Title:",false); ?></label></div>
									<div class='col-xs-12'><input class='form-control' type="text" id="tnews" name="tnews" value='<?php echo $news[$id+2]; ?>' /></div>
								</div>
							</div>
							<div class='form-group'>
								<div class='row'>
									<div class='col-xs-12'><label><?php $translate->__("News:",false); ?></label></div>
									<div class='col-xs-12'><textarea class='form-control' type="text" id="nnews" name="nnews" class='ckeditor'><?php echo $news[$id]; ?></textarea></div>
								</div>
							</div>
						<input type="submit" name="bnews" id="bnews" value="<?php $translate->__("Save Changes",false); ?>" class="btn btn-success"/>
					</form>
				<?php } else { ?>
				<table cellpadding="0" cellspacing="0" border="0" class="table-striped table-bordered" id="news" >
					<tbody>
					<?php
					$count=count($news);
					if($count>0){
						for($i=0,$j=$count/3;$i<$count;$i+=3,$j--)
							echo '<tr><td>'.$j.'</td><td>'.$news[$i+2].'</td><td>'.$news[$i+1].'</td><td><div class="btn-group"><button class="btn btn-danger" title="'.$translate->__("Delete News",true).'" onclick="javascript:contrch(\'managenews.php?act=del&id='.$i.'\');" ><span class="glyphicon glyphicon-remove"></span></button><button class="btn btn-info" onclick="javascript:location.href=\'managenews.php?act=edit&id='.$i.'\';" title="'.$translate->__("Edit News",true).'"><span class="glyphicon glyphicon-edit"></button></div></td></tr>';
					}
					?>
					</tbody>
				</table>
				<?php } ?>
			</div>
			<form name="logoutfor" id="logoutfor"  method="post"  class='formcor'>
				<input type="submit" name="logout" id="logout" value="Logout" class="btn btn-danger"/>
			</form>
		</div>
		<?php } else { ?>
		<div class='container main'>
			<form name="formdata" id="formdata" method="post"  class='formcor form-inline'>
				<h2 class='titlesec'>Login</h2>
					<!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" /><![endif]-->
					<?php if(isset($acc) && $acc==false){ ?>
					<div class='row'><div class='col-xs-12 col-sm-3 col-md-12'><p><?php $translate->__("Wrong Password",false); ?><p></div></div>
					<?php } ?>
				<div class='row'>
					<div class='col-xs-12 col-sm-6 col-md-2'><label>Password</label></div>
					<div class='col-xs-12 col-sm-6 col-md-4'><input type="password" id="pwd" name="pwd" placeholder="Password"></div>
				</div>
				<br/><br/>
				<input type="submit" name="loginb" id="loginb" value="Login" class="btn btn-success"/>
			</form>
		</div>
		<?php } 
		?>
	<?php if(isset($_SESSION['views']) && $_SESSION['views']==1946 ){ ?>
		<script type="text/javascript"  src="../lib/DataTables/js/jquery.dataTables.js"></script>
		<script type="text/javascript"	src="../ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				if($('#nnews').length>0) CKEDITOR.replace('nnews');
				$('#news').dataTable({
					"sDom": "<<'col-xs-12 col-md-6'l><'col-xs-12 col-md-6'f>r>t<<'col-xs-12 col-md-6'i><'col-xs-12 col-md-6'p>>",
					"aoColumns": [
							{ "sTitle": "<?php $translate->__("Number",false); ?>",'sWidth':'60px' },
							{ "sTitle": "<?php $translate->__("Title",false); ?>" },
							{ "sTitle": "<?php $translate->__("Posted Date",false); ?>",'sWidth':'150px' },
							{ "sTitle": "<?php $translate->__("Toggle",false); ?>","bSortable": false,"bSearchable":false,'sWidth':'95px' }
						]
				});
				$.extend( $.fn.dataTableExt.oStdClasses, {
					"sWrapper": "dataTables_wrapper form-inline"
				} );
			} );
			function contrch(link){ 
				   if(window.confirm("Do you want to delete this news?"))
					 window.location.href = link;
				}
		</script>
	<?php } ?>
	</body>
</html>

<?php function retrive_ip(){if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];}elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip=$_SERVER['REMOTE_ADDR'];}return $ip;} ?>