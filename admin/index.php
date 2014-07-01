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
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('../translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);

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
	if(is_file('../config/monintoring.php')){
		include_once('../config/monintoring.php');
		if(file_put_contents('../config/monintoring.txt',$monitoringcode))
			unlink('../config/monintoring.php');
	}
	$filemonitor='../config/monintoring.txt';
	$fileconfig='../config/config.txt';
	$socialfile='../config/social.txt';
	$filefnmessage= '../config/fnmessage.txt';
	$filefnfooter= '../config/footermail.txt';
	$filelogo= '../config/logo.txt';
	$filefrontmess= '../config/frontmess.txt';
	$frontotinfo= '../config/indexfooter.txt';

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
	
	$var = file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$social=file($socialfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	if(is_file($filemonitor)){$monitoringcode=file($filemonitor, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);$monitoringcode=implode("\n",$monitoringcode);}
	
	$messagefn= file_get_contents($filefnmessage);
	$footermail= file_get_contents($filefnfooter);
	$logo= file_get_contents($filelogo);
	$phrase=file_get_contents($filefrontmess);
	$frontph=file($frontotinfo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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
	14	Number Email Limit
	15	Time email limit
	16	Display Time Countdown
	17	Display Progresbar
	18	fitetxt
	19	pass
	20	cron
	21	instant parameter
	22	exec parameter
	23	en redirect
	24	enable captcha
	*/
	
	if(isset($_POST['uploadlogo'])){
		$target_path = "../css/images/".basename( $_FILES['uploadedfile']['name']);
		if(!empty($_FILES['uploadedfile']['tmp_name'])){
			$a=getimagesize($_FILES['uploadedfile']['tmp_name']);
			
			if( in_array($a[2] , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)) && ($_FILES['uploadedfile']['type']=='image/png' || $_FILES['uploadedfile']['type']=='image/jpeg' || $_FILES['uploadedfile']['type']=='image/pjpeg' || $_FILES['uploadedfile']['type']=='image/gif')){
				if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
				
					$fs=fopen($filelogo,"w+");
					fwrite($fs,basename( $_FILES['uploadedfile']['name']));
					fclose($fs);
				} else
					echo "There was an error uploading the file, please try again!";
				header('Location: index.php');
			}
			else{
				$uperror= 'Invalid Image';
			}
		}
		else{
			$uperror= 'Empty or Invalid Image';
		}
	}
	
	if(isset($_POST['fcheck'])){
		header('Location: datacheck.php');
	}
	
}
	if (isset($var[10]))date_default_timezone_set($var[10]);
?>
<!DOCTYPE html>
<html  lang="<?php echo $lang; ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="viewport" content="width=device-width">
		<title><?php $translate->__("Setup",false); ?></title>
		
		
		<!--[if lt IE 9]><script src="../js/html5shiv-printshiv.js"></script><![endif]-->
		<link rel="stylesheet" href="../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
		<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
		
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		
		<script type="text/javascript"  src="../js/jquery.js"></script>
		<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
		<script  type="text/javascript" src="../ckeditor/ckeditor.js"></script>
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
								<li class="dropdown active" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Setup",false); ?> <b class="caret"></b></a>
									<ul class="dropdown-menu" role="menu">
										<li role="presentation" class='active'><a href="index.php" tabindex="-1" role="menuitem"><?php $translate->__("Site",false); ?></a></li>
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
				</nav>
			</div>
		</header>
		<div class='main container'>
					<div class='formcor' style='text-align:center' ><button onclick='javascript:location.href="../index.php"' value='<?php $translate->__("See Frontend",false); ?>' class='btn btn-info'><?php $translate->__("See Frontend",false); ?></button></div>
					<form name="ckform" id="ckform"  method="post"  class='formcor form-inline'>
					<h2 class='titlesec'><?php $translate->__("Database Files Checking",false); ?></h2>
						<input type="submit" name="fcheck" id="fcheck" value="<?php $translate->__("Check Database Files",false); ?>" class="btn"/>
					</form>
					
					<form  name="logoform" id="logoform" enctype="multipart/form-data"  method="POST" class='formcor'>
						<h2 class='titlesec'>Logo</h2>
						<?php if(isset($uperror))echo '<p>'.$uperror.'</p>' ?>
						<input type="hidden" name="MAX_FILE_SIZE" value="50000" />
						<div class='form-group'>
							<div class='row'>
								<div class='col-xs-12 col-sm-4 col-md-2'><label><?php $translate->__("Current logo:",false); ?></label></div>
								<div class='col-xs-12 col-sm-8 col-md-10'><img src='../css/images/<?php if(isset($logo) && rtrim($logo)!='') echo $logo;else echo "logo.png"; ?>' alt='Logo'/></div>
							</div>
						</div>
						
						<div class='form-group'>
							<div class='row'>
								<div class='col-xs-12'><label><?php $translate->__("Upload a New Logo(png, jpeg, jpg, gif; max=5 MB):",false); ?></label></div>
								<div class='col-xs-12'><input id="uploadedfile" name="uploadedfile" type="file" /></div>
							</div>
						</div>
						<input type="submit" name="uploadlogo" id="uploadlogo" value="<?php $translate->__("Upload New Logo",false); ?>" class="btn btn-success"/>
					</form>
						
					<form name="formdata" id="formdata"  method="post"  class='formcor'>
						<h2 class='titlesec'><?php $translate->__("Meta Information",false); ?></h2>
						<div class='row'>
							<div class='col-xs-12 col-sm-4 col-md-2'><?php $translate->__("Meta Description:",false); ?></div>
							<div class='col-xs-12 col-sm-8 col-md-10'><textarea class='form-control metacont' type="text" id="metadesc" name="metadesc" ><?php if(isset($frontph[2]) && $frontph[2]!='**@****nullo**@****') echo $frontph[2]; ?> </textarea></div>
						</div>
						<div class='row'>
						<div class='col-xs-12 col-sm-4 col-md-2'><?php $translate->__("Meta Keywords:",false); ?></div>
						<div class='col-xs-12 col-sm-8 col-md-10'><input class='form-control metacont' type="text" id="metakey" name="metakey" <?php if(isset($frontph[3]) && $frontph[3]!='**@****nullo**@****') echo 'value="'.$frontph[3].'"'; ?> /></div>
						</div>
						
						<h2 class='titlesec'><?php $translate->__("Frontend",false); ?></h2>
							
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-4 col-md-2'>
										<label><?php $translate->__("Site Title:",false); ?>*</label>
									</div>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<input class='form-control' type="text" id="pgtit" name="pgtit" <?php if(isset($var[5]) && $var[5]!='**@****nullo**@****') echo 'value="'.$var[5].'"'; ?> required />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class='row form-group'>
									<div class='col-xs-12 col-sm-4 col-md-2'>
										<label><?php $translate->__("Enable Redirect?",false); ?></label>
									</div>
									<div class='col-xs-12 col-sm-2'>
										<select class='form-control' name='enredirect' id='enredirect'>
											<option value='yes'>Yes</option>
											<option value='no'>No</option>
										</select>
									</div>
									<div class='col-xs-12 col-sm-4 col-md-2'>
										<label><?php $translate->__("Finished Site Url:",false); ?>*</label>
									</div>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<input class='form-control' type="text" id="urls" name="urls" <?php if(isset($var[4]) && $var[4]!='**@****nullo**@****') echo 'value="'.$var[4].'"'; ?> />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-4 col-md-2'><label><?php $translate->__("Use",false); ?> <a href='http://fittextjs.com/' target='_blank'>FitText</a>?</label></div>
									<div class='col-xs-12 col-sm-2'>
										<select class='form-control' name='enfitetx' id='enfitetx'>
											<option value='yes'>Yes</option>
											<option value='no'>No</option>
										</select>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12'><label><?php $translate->__("Site phrase:",false); ?></label></div>
									<div class='col-xs-12'><textarea class="form-control" type="text" id="phrase" name="phrase"><?php if(isset($phrase) && $phrase!='**@****nullo**@****') echo stripslashes($phrase); ?></textarea></div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-3'><label><?php $translate->__("Time Zone:",false); ?>*</label></div>
									<div class='col-xs-12 col-sm-6 col-md-3'><input class='form-control' type="text" id="tz" name="tz" <?php if(isset($var[10]) && $var[10]!='**@****nullo**@****') echo 'value="'.$var[10].'"'; ?> required /></div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='datai'><?php $translate->__("Starting data:",false); ?>*</label>
										<input class='form-control' type="text" id="datai" name="datai" <?php if(isset($var[0]) && $var[0]!='**@****nullo**@****') echo 'value="'.$var[0].'"'; ?> required />
									</div>
					
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='horai'><?php $translate->__("Starting hour(hh):",false); ?></label>
										<input class='form-control' type="text" name='horai' id='horai' value='<?php if(count($var)>1)echo $var[1][0];else echo '00'; ?>'/>
									</div>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='morai'><?php $translate->__("Starting minute (mm):",false); ?></label>
										<input class='form-control' type="text" name='morai' id='morai' value='<?php if(count($var)>1)echo $var[1][1];else echo '00'; ?>'/>
									</div>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='sorai'><?php $translate->__("Starting second(ss):",false); ?></label>
										<input class='form-control' type="text" name='sorai' id='sorai' value='<?php if(count($var)>1)echo $var[1][2];else echo '00'; ?>'/>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='dataf'><?php $translate->__("Relase date:",false); ?>*</label>
										<input class='form-control' type="text" id="dataf" name="dataf" <?php if(isset($var[2]) && $var[2]!='**@****nullo**@****') echo 'value="'.$var[2].'"'; ?> required />
									</div>
									
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='horaf'><?php $translate->__("Relase hour (hh):",false); ?></label>
										<input class='form-control' type="text" name='horaf' id='horaf' value='<?php if(count($var)>1)echo $var[3][0];else echo '00'; ?>'/>
									</div>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='moraf'><?php $translate->__("Relase minute (mm):",false); ?></label>
										<input class='form-control' type="text" name='moraf' id='moraf' value='<?php if(count($var)>1)echo $var[3][1];else echo '00'; ?>'/>
									</div>
									<div class='col-xs-12 col-sm-6 col-md-3'>
										<label for='soraf'><?php $translate->__("Relase second (ss):",false); ?></label>
										<input class='form-control' type="text" name='soraf' id='soraf' value='<?php if(count($var)>1)echo $var[3][2];else echo '00'; ?>'/>
									</div>
								</div>
							</div>
							<br/><br/>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'><?php $translate->__("Complete Percent:",false); ?><input class='form-control' type="text" id="perc" name="perc" <?php if(isset($var[6]) && $var[6]!='**@****nullo**@****') echo 'value="'.$var[6].'"'; ?>/></div>
									<div class='col-xs-12 col-sm-6 col-md-3'><?php $translate->__("Admin email:",false); ?><input class='form-control' type="text" id="emailad" name="emailad" <?php if(isset($var[7]) && $var[7]!='**@****nullo**@****') echo 'value="'.$var[7].'"'; ?> /></div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Show Frontend Clock?",false); ?></label></div>
									<div class='col-xs-12 col-sm-2'>
										<select class='form-control' name='dispclock' id='dispclock'>
											<option value='yes'>Yes</option>
											<option value='no'>No</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Show Frontend Progressbar?",false); ?></label></div>
									<div class='col-xs-12 col-sm-2'>
										<select class='form-control' name='dispprog' id='dispprog'>
											<option value='yes'>Yes</option>
											<option value='no'>No</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Show Frontend Contact Form?",false); ?></label></div>
									<div class='col-xs-12 col-sm-2'>
										<select class='form-control' name='shcf' id='shcf'>
											<option value='yes'>Yes</option>
											<option value='no'>No</option>
										</select>
									</div>
									<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Enable Captcha?",false); ?></label></div>
									<div class='col-xs-12 col-sm-2'>
										<select class='form-control' name='encaptcha' id='encaptcha'>
											<option value='yes'>Yes</option>
											<option value='no'>No</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Show Frontend Subscribe Form?",false); ?></label></div>
									<div class='col-xs-12 col-sm-2'>
										<select class='form-control' name='shsf' id='shsf'>
											<option value='yes'>Yes</option>
											<option value='no'>No</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class='col-xs-12'><label><?php $translate->__("Progressbar Phrase:",false); ?></label></div>
									<div class='col-xs-12'><textarea class="form-control" type="text" id="progph" name="progph" ><?php if(isset($frontph[1]) && $frontph[1]!='**@****nullo**@****') echo stripslashes($frontph[1]); ?></textarea></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class='col-xs-12'><label><?php $translate->__("Footer Phrase:",false); ?></label></div>
									<div class='col-xs-12'><textarea class="form-control" type="text" id="footerph" name="footerph"><?php if(isset($frontph[0]) && $frontph[0]!='**@****nullo**@****') echo stripslashes($frontph[0]); ?></textarea></div>
								</div>
							</div>
							
							<h3>Email Setting</h3>
							<h5><label><?php $translate->__("Server Email Restriction",false); ?></label></h5>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-4 col-md-2'><?php $translate->__("Number of email",false); ?></div>
									<div class='col-xs-12 col-sm-6 col-md-2'><input class='form-control' type="text" id="mailimit" name="mailimit" <?php if(isset($var[14]) && $var[14]!='none') echo 'value="'.$var[14].'"'; ?> /></div>
									<div class='col-xs-12 col-sm-4 col-md-2'><?php $translate->__("per (in seconds)",false); ?></div>
									<div class='col-xs-12 col-sm-6 col-md-2'><input class='form-control' type="text" id="pertime" name="pertime" <?php if(isset($var[15]) && $var[15]!='none') echo 'value="'.$var[15].'"'; ?> /></div>
								</div>
							</div>
							<div class="form-group">
							<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Show Unsubscribe Link Inside Email Footer?",false); ?></label></div>
								<div class='col-xs-12 col-sm-2'>
									<select class='form-control' name='shunl' id='shunl'>
										<option value='yes'>Yes</option>
										<option value='no'>No</option>
									</select>
								</div>
							</div>
							</div>
							<p><?php $translate->__("Once you have saved these settings you can complete the email configuration under <a href='mail_setting.php'>Setup->Mail</a>",false); ?></p>
							

							<h3>Cronjob Setting</h3>
							<div class="form-group">
							<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Checking Word:",false); ?>*</label></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><input class='form-control' type="text" id="psphrase" name="psphrase" <?php if(isset($var[19]) && $var[19]!='**@****nullo**@****') echo 'value="'.$var[19].'"'; ?> required/></div>
							</div>
							</div>
							<div class="form-group">
							<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-3'><label for='execpara' ><?php $translate->__("PHP Command Parameter(instant sending):",false); ?>*</label></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><input class='form-control' type="text" id="execpara" name="execpara" <?php if(isset($var[21]) && $var[21]!='**@****nullo**@****') echo 'value="'.$var[21].'"'; ?> required /></div>
							</div>
							</div>
							<div class="form-group">
								<div class='row'>
									<div class='col-xs-12 col-sm-6 col-md-3'><label for='cronpara' ><?php $translate->__("PHP Command Parameter(cronjob):",false); ?>*</label></div>
									<div class='col-xs-12 col-sm-6 col-md-3'><input class='form-control' type="text" id="cronpara" name="cronpara" <?php if(isset($var[22]) && $var[22]!='**@****nullo**@****') echo 'value="'.$var[22].'"'; ?> required /></div>
								</div>
							</div>
						<input onclick='javascript:return false;' type="submit" name="datacom" id="datacom" value="<?php $translate->__("Set",false); ?>" class="btn btn-success"/>
					</form>

					<?php if(isset($var[20])) {?>
						<div class='formcor'><h2 class='titlesec'><?php $translate->__("Cronjob String",false); ?></h2>
							<p><?php $translate->__("If you can't automatically update the Cronjob trough the php function you can try set it by your own, this is the string with the information:",false); ?></p>
							
							<p id='cronstring'><?php echo $var[20]; ?></p>
						</div>
					<?php } ?>
					
					<form class='formcor'>
						<h2 class='titlesec'><?php $translate->__("Monitoring Code",false); ?></h2>
						<div class="form-group">
							<div class='row'>
								<div class='span12'><label><?php $translate->__('Write here your monitoring code(no script tags)',false); ?></label></div>
							</div>
						</div>
						<div class="form-group">
							<div class='row'>
								<div class='span12'><textarea class="form-control" id='analisyscode'><?php if(isset($monitoringcode)) echo stripslashes($monitoringcode); ?></textarea></div>
							</div>
						</div>
						
						<input onclick='javascript:return false;' type="submit" name="setmonit" id="setmonit" value="<?php $translate->__("Save Code",false); ?>" class="btn btn-success"/>
					</form>
					
					<form name="passwordform" id="passwordform"  method="post"  class='formcor'>
						<h2 class='titlesec'><?php $translate->__("Password Change",false); ?></h2>
						<div class="form-group">
							<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Old Password:",false); ?></label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input class='form-control' type="password" id="oldpwd" name="oldpwd" /></div>
							</div>
						</div>
						<div class="form-group">
							<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("New Password:",false); ?></label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input class='form-control' type="password" id="newpwd" name="newpwd"/></div>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Repeat new Password:",false); ?></label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input class='form-control' type="password" name='cnewpwd' id='cnewpwd'/></div>
							</div>
						</div>
					
						<input onclick='javascript:return false;' type="submit" name="updatepwd" id="updatepwd" value="<?php $translate->__("Update Password",false); ?>" class="btn btn-success"/>
					</form>
					
					<form name="socialform" id="socialform"  method="post"  class='formcor form-horizontal'>
					<h2 class='titlesec'><?php $translate->__("Social Network Link",false); ?></h2>
						<div class="form-group">
							<div class="row">
								<div class='col-xs-12 col-sm-6 col-md-3'><label>Blogger</label><input class='form-control' type="text" id="blog" name="blog" <?php if(isset($social[0]) && $social[0]!='**@****nullo**@****') echo 'value="'.$social[0].'"'; ?> placeholder='Blogger Link'/></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><label>DeviantArt</label><input class='form-control' type="text" id='devian' name='devian' <?php if(isset($social[1]) && $social[1]!='**@****nullo**@****') echo 'value="'.$social[1].'"'; ?> placeholder='DeviantArt Link'/></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><label>Facebook</label><input class='form-control' 	type="text" id="fb" name="fb" <?php if(isset($social[2]) && $social[2]!='**@****nullo**@****') echo 'value="'.$social[2].'"'; ?> placeholder='Facebook Link'/></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><label>Flickr</label><input class='form-control' type="text" id="fl" name="fl" <?php if(isset($social[3]) && $social[3]!='**@****nullo**@****') echo 'value="'.$social[3].'"'; ?> placeholder='Flickr Link'/></div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<div class='col-xs-12 col-sm-6 col-md-3'><label>Linkedin</label><input class='form-control' type="text" id="linkedin" name="linkedin" <?php if(isset($social[4]) && $social[4]!='**@****nullo**@****') echo 'value="'.$social[4].'"'; ?> placeholder='Linkedin Link'/></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><label>Twitter</label><input class='form-control' type="text" id="tw" name="tw" <?php if(isset($social[5]) && $social[5]!='**@****nullo**@****') echo 'value="'.$social[5].'"';?> placeholder='Twitter Link'/></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><label>Wordpress</label><input class='form-control' type="text" id="word" name="word" <?php if(isset($social[6]) && $social[6]!='**@****nullo**@****') echo 'value="'.$social[6].'"'; ?> placeholder='Wordpress Link'/></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><label>Youtube</label><input class='form-control' type="text" id='yb' name='yb' <?php if(isset($social[7]) && $social[7]!='**@****nullo**@****') echo 'value="'.$social[7].'"'; ?> placeholder='Youtube Link'/></div>
							</div>
						</div>
						
						<input onclick='javascript:return false;' type="submit" name="updatesocial" id="updatesocial" value="<?php $translate->__("Update Information",false); ?>" class="btn btn-success"/>
					</form>
					
					<form name="logoutfor" id="logoutfor" method="post"  class='formcor'>
						<input type="submit" name="logout" id="logout" value="Logout" class="btn btn-danger"/>
					</form>
		</div>
		<?php } else { ?>
		<div class='container main'>
			<form name="formdata" id="formdata" method="post"  class='formcor form-inline'>
				<h2 class='titlesec'>Login</h2>
					<!--[if IE]><input class='form-control' type="text" style="display: none;" disabled="disabled" size="1" /><![endif]-->
					<?php if(isset($acc) && $acc==false){ ?>
					<div class='row'><div class='span12'><p><?php $translate->__("Wrong Password",false); ?><p></div></div>
					<?php } ?>
				<div class='row'>
					<div class='col-xs-12 col-sm-4 col-md-2'><label>Password</label></div>
					<div class='col-xs-12 col-sm-6 col-md-4'><input class='form-control' type="password" id="pwd" name="pwd" placeholder="Password"></div>
				</div>
				
				<input class='form-control' type="submit" name="loginb" id="loginb" value="Login" class="btn btn-success"/>
			</form>
		</div>
		<?php } 
		?>
	<?php if(isset($_SESSION['views']) && $_SESSION['views']==1946 ){ ?>
		<script type="text/javascript"  src="../js/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript"  src="../js/jquery.validate.min.js"></script>
		<script type="text/javascript"  src="../js/timezoneautocomplete.js"></script>
		<script type="text/javascript"  src="../js/noty/jquery.noty.js"></script>
		<script type="text/javascript"  src="../js/noty/layouts/top.js"></script>
		<script type="text/javascript"  src="../js/noty/themes/default.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			<?php if(isset($var[8])){ ?>
				$('select[name="shcf"] option[value="<?php echo $var[8]; ?>"]').attr("selected", "selected");
				<?php if(isset($var[8])=='yes'){ ?>
					$('#emailad').attr('required','required');
			<?php }}if(isset($var[9])){ ?>
				$('select[name="shsf"] option[value="<?php echo $var[9]; ?>"]').attr("selected", "selected");
			<?php }if(isset($var[11])){ ?>
				$('select[name="shunl"] option[value="<?php echo $var[11]; ?>"]').attr("selected", "selected");
			<?php }if(isset($var[23])){ ?>
				$('select[name="enredirect"] option[value="<?php echo $var[23]; ?>"]').attr("selected", "selected");
				<?php if(isset($var[23])=='yes'){ ?>
					$('#urls').attr('required','required');
			<?php }}if(isset($var[18])){ ?>
				$('select[name="enfitetx"] option[value="<?php echo $var[18]; ?>"]').attr("selected", "selected");
			<?php }if(isset($var[16])){ ?>
				$('select[name="dispclock"] option[value="<?php echo $var[16]; ?>"]').attr("selected", "selected");
			<?php }if(isset($var[17])){ ?>
				$('select[name="dispprog"] option[value="<?php echo $var[17]; ?>"]').attr("selected", "selected");
			<?php }if(isset($var[24])){ ?>
				$('select[name="encaptcha"] option[value="<?php echo $var[24]; ?>"]').attr("selected", "selected");
			
			<?php } ?>
			CKEDITOR.replace('phrase');
			CKEDITOR.replace('progph');
			CKEDITOR.replace('footerph');

			$('#datai').datepicker({ dateFormat: 'yy-mm-dd' });
			<?php if(isset($var[0]) && $var[0]!='' ){ ?>$("#datai").datepicker("setDate", "<?php echo $var[0] ?>");<?php } ?>
			var dateArray = new String("<?php echo date("Y-m-d");?>").split('-'),
				dateObject = new Date(dateArray[0], dateArray[1]-1, dateArray[2]);
			$("#datai" ).datepicker("option", "maxDate", dateObject);

			$('#dataf').datepicker({ dateFormat: 'yy-mm-dd' });
			<?php if(isset($var[2]) && $var[2]!='' ){ ?>
				$("#dataf" ).datepicker("setDate", "<?php echo $var[2] ?>");
			<?php } ?>
			var dateArray = new String("<?php if(isset($var[0]))echo $var[0];else echo date("Y-m-d");?>").split('-'),
				dateObject = new Date(dateArray[0], dateArray[1]-1, dateArray[2]);
			$("#dataf" ).datepicker("option", "minDate", dateObject);
			
			$('#shcf').change(function(){
				if($(this).val()=='yes')
					$('#emailad').attr('required','required')
				else
					$('#emailad').removeAttr('required')
			});
			
			$('#enredirect').change(function(){
				if($(this).val()=='yes')
					$('#urls').attr('required','required')
				else
					$('#urls').removeAttr('required')
			});

			$('#datacom').click(function(){
				var metadesc=$('#metadesc').val().replace(/\s+/g,' '),
					metakey=$('#metakey').val().replace(/\s+/g,' '),
					pgtit=$('#pgtit').val().replace(/\s+/g,' '),
					enredirect=$('#enredirect').val(),
					urls=$('#urls').val().replace(/\s+/g,' '),
					enfitetx=$('#enfitetx').val(),
					phrase=CKEDITOR.instances.phrase.getData().replace(/\s+/g,' '),
					tz=$('#tz').val().replace(/\s+/g,''),
					datai=$('#datai').val().replace(/\s+/g,''),
					horai=$('#horai').val().replace(/\s+/g,''),
					morai=$('#morai').val().replace(/\s+/g,''),
					sorai=$('#sorai').val().replace(/\s+/g,''),
					dataf=$('#dataf').val().replace(/\s+/g,''),
					horaf=$('#horaf').val().replace(/\s+/g,''),
					moraf=$('#moraf').val().replace(/\s+/g,''),
					soraf=$('#soraf').val().replace(/\s+/g,''),
					perc=$('#perc').val().replace(/\s+/g,''),
					emailad=$('#emailad').val().replace(/\s+/g,''),
					psphrase=$('#psphrase').val().replace(/\s+/g,''),
					phpexec=$('#execpara').val().replace(/\s+/g,' '),
					cronpara=$('#cronpara').val().replace(/\s+/g,' '),
					shcf=$('#shcf').val(),
					shsf=$('#shsf').val(),
					shunl=$('#shunl').val(),
					dispclock=$('#dispclock').val(),
					dispprog=$('#dispprog').val(),
					mailimit=$('#mailimit').val().replace(/\s+/g,''),
					pertime=$('#pertime').val().replace(/\s+/g,''),
					progph=CKEDITOR.instances.progph.getData().replace(/\s+/g,' '),
					footerph=CKEDITOR.instances.footerph.getData().replace(/\s+/g,' '),
					encaptcha=$('#encaptcha').val();

				if($("#psphrase").val().split(/\s+/).length==1){
					if(pgtit.replace(/\s+/g,'')!='' && urls.replace(/\s+/g,'')!='' && tz.replace(/\s+/g,'')!='' && datai.replace(/\s+/g,'')!='' && dataf.replace(/\s+/g,'')!='' && psphrase.replace(/\s+/g,'')!=''){
						$.ajax({
							type: 'POST',
							url: 'function.php',
							data: {act:'save_options',metadesc:metadesc,metakey:metakey,pgtit:pgtit,enredirect:enredirect,urls:urls,enfitetx:enfitetx,phrase:phrase,tz:tz,datai:datai,horai:horai,morai:morai,sorai:sorai,dataf:dataf,horaf:horaf,moraf:moraf,soraf:soraf,perc:perc,emailad:emailad,psphrase:psphrase,shcf:shcf,shsf:shsf,shunl:shunl,dispclock:dispclock,dispprog:dispprog,mailimit:mailimit,pertime:pertime,progph:progph,footerph:footerph,eparam:phpexec,cronpara:cronpara,encaptcha:encaptcha},
							dataType : 'json',
							success : function (data) {
								if(data[0]=='Saved'){
									if(data.length>1){
										noty({text: "<?php echo $translate->__("The settings have been saved",true); ?>",type:'success',timeout:9000});
										if($('#cronstring').length)
											$('#cronstring').html(data[1]);
										else
											$('#formdata').after("<div class='formcor'><h2 class='titlesec'><?php echo $translate->__("Cronjob String",true); ?></h2><p><?php echo $translate->__("If you can't automatically update the Cronjob trough the php function you can try set it by your own, this is the string with the information:",true); ?></p><p id='cronstring'>"+data[1]+"</p></div>");
									}
									else{
										noty({text: "<?php echo $translate->__("This is the first time,the page will be reloaded",true); ?>",type:'success',timeout:9000});
										window.location.reload();
									}
								}
								else if(data[0]=='Empty')
									noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
								else
									noty({text: "<?php echo $translate->__("A problem has occured,please try again",true); ?>",type:'error',timeout:9000});
							}
						}).fail(function(jqXHR, textStatus){noty({text: textStatus,type:'error',timeout:9000});});
					}
					else
						noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
				}
				else
					noty({text: "<?php echo $translate->__("The Checking Word must be only one word.",true); ?>",type:'error',timeout:9000});
				return false;
			});
			
			$('#updatepwd').click(function(){
				var oldpwd=$('#oldpwd').val();
				var newpwd=$('#newpwd').val();
				var cnewpwd=$('#cnewpwd').val();
				if(oldpwd.replace(/\s+/g,'')!='' && newpwd.replace(/\s+/g,'')!='' && cnewpwd.replace(/\s+/g,'')!='' && cnewpwd===newpwd){
					$.ajax({
						type: 'POST',
						url: 'function.php',
						data: {act:'update_password',oldpwd:oldpwd,newpwd:newpwd,cnewpwd:cnewpwd},
						dataType : 'json',
						success : function (data) {
							if(data[0]=='Updated'){
								$('#oldpwd').val('');
								$('#newpwd').val('');
								$('#cnewpwd').val('');
								noty({text: "<?php echo $translate->__("Password Updated",true); ?>",type:'success',timeout:9000});
							}
							else if(data[0]=='Error')
								noty({text: "<?php echo $translate->__("A problem has occured,please try again",true); ?>",type:'error',timeout:9000});
							else if(data[0]=='Empty')
								noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
							else if(data[0]=='Wrong')
								noty({text: "<?php echo $translate->__("Wrong Password",true); ?>",type:'error',timeout:9000});
						}
					}).fail(function(jqXHR, textStatus){noty({text: textStatus,type:'error',timeout:9000});});
				}
				else
					noty({text: "<?php echo $translate->__("The new passwords don't correspond",true); ?>",type:'error',timeout:9000});
				return false;
			});
			
			$('#updatesocial').click(function(){
				var blog=$('#blog').val(),
					devian=$('#devian').val(),
					fb=$('#fb').val(),
					fl=$('#fl').val(),
					linkedin=$('#linkedin').val(),
					tw=$('#tw').val(),
					word=$('#word').val(),
					yb=$('#yb').val();
				
				$.ajax({
					type: 'POST',
					url: 'function.php',
					data: {act:'update_social',blog:blog,devian:devian,fb:fb,fl:fl,linkedin:linkedin,tw:tw,word:word,yb:yb},
					dataType : 'json',
					success : function (data) {
						if(data[0]=='Saved')
							noty({text: "<?php echo $translate->__("Social Network Links Saved",true); ?>",type:'success',timeout:9000});
						else if(data[0]=='Error')
							noty({text: "<?php echo $translate->__("A problem has occured,please try again",true); ?>",type:'error',timeout:9000});
						else if(data[0]=='Empty')
							noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
					}
				}).fail(function(jqXHR, textStatus){noty({text: textStatus,type:'error',timeout:9000});});
				return false;
			});
			
			$('#setmonit').click(function(){
				var c=$('#analisyscode').val();

				$.ajax({
					type: 'POST',
					url: 'function.php',
					data: {act:'monitoring_code',code:c},
					dataType : 'json',
					success : function (data) {
						if(data[0]=='Saved')
							noty({text: "<?php echo $translate->__("Monitoring Code Saved",true); ?>",type:'success',timeout:9000});
						else if(data[0]=='Error')
							noty({text: "<?php echo $translate->__("A problem has occured,please try again",true); ?>",type:'error',timeout:9000});
						else if(data[0]=='Empty')
							noty({text: "<?php echo $translate->__("Please add the code",true); ?>",type:'error',timeout:9000});
					}
				}).fail(function(jqXHR, textStatus){noty({text: textStatus,type:'error',timeout:9000});});
				return false;
			});
			
			$("#logoform").validate(
			{
				rules:{uploadedfile:{required:true,accept:"image/gif,image/pjpeg,image/png,image/jpeg"}},
				messages:{uploadedfile:"image/gif,image/pjpeg,image/png,image/jpeg"}
			});
			
			$("#formdata").validate(
			{
				rules:{folurl:{required:true,url:true},urls:{required:true,url:true},perc:{max:100,min:0},datai: "required",dataf: "required",shcf: "required",shsf: "required",tz:'required',pgtit: 'required',horai:{digits:true,minlength:2,maxlength :2,min:00,max:23},morai:{digits:true,minlength:2,maxlength :2,min:00,max:59},sorai:{digits:true,minlength:2,maxlength :2,min:00,max:59},horaf:{digits:true,minlength:2,maxlength :2,min:00,max:23},moraf:{digits:true,minlength:2,maxlength :2,min:00,max:59},soraf:{digits:true,minlength:2,maxlength :2,min:00,max:59},mailimit:{digits:true},pertime:{digits:true}},
				messages:{datai: "<?php echo $translate->__("Select data",true); ?>",dataf: "<?php echo $translate->__("Select data",true); ?>",horaf: "<?php echo $translate->__("Two digits: 00-23",true); ?>",moraf: "<?php echo $translate->__("Two digits: 00-59",true); ?>",soraf: "<?php echo $translate->__("Two digits: 00-59",true); ?>",horai: "<?php echo $translate->__("Two digits: 00-23",true); ?>",morai: "<?php echo $translate->__("Two digits: 00-59",true); ?>",sorai: "<?php echo $translate->__("Two digits: 00-59",true); ?>",urls: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",folurl: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",shcf: "<?php echo $translate->__("Please choose",true); ?>",shsf: "<?php echo $translate->__("Please choose",true); ?>",tz: "<?php echo $translate->__("Please Complete",true); ?>"}
			});
			
			$("#passwordform").validate(
			{
				rules:{pwd:"required",oldpwd: "required",newpwd: "required",cnewpwd:{required: true,equalTo: "#newpwd"}},
				messages:{cnewpwd: "<?php echo $translate->__("The new passwords don't correspond",true); ?>"}
			});
			
			$("#socialform").validate(
			{
				rules:{blog:{url:true},devian:{url:true},fb:{url:true},fl:{url:true},linkedin:{url:true},tw:{url:true},word:{url:true},yb:{url:true}},
				messages:{blog: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",devian: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",fb: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",fl: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",linkedin: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",tw: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",word: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>",yb: "<?php echo $translate->__("Invalid url ex: http://site.com",true); ?>"}
			});
		});
		</script>
	<?php } ?>
	</body>
</html>
<?php function retrive_ip(){if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];}elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip=$_SERVER['REMOTE_ADDR'];}return $ip;} ?>