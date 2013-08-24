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
	session_start();
	
	require_once ('../config/pass.php');
	$fileconfig='../config/config.txt';
	$socialfile='../config/social.txt';
	$filefnmail='../config/fnmail.txt';
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
			$adminpassword=hash('whirlpool',$_POST['pwd']);
			if(isset($acc)) unset($acc);
		}
		else if (hash('whirlpool',$_POST['pwd'])==$adminpassword){
			$_SESSION['views']=1946;
			if(isset($acc)) unset($acc);
			header('Location: '.$_SERVER['REQUEST_URI']);
		}
	}
	/*end login*/

if(isset($_SESSION['views']) && $_SESSION['views']==1946){
	unset($_POST['loginb']);
	unset($_POST['pwd']);
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
	
	$fnmail= file($filefnmail, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
	*/
	
	if(isset($_POST['uploadlogo'])){
		$target_path = "../css/images/".basename( $_FILES['uploadedfile']['name']);
		if($_FILES['uploadedfile']['type']=='image/png' || $_FILES['uploadedfile']['type']=='image/jpeg' || $_FILES['uploadedfile']['type']=='image/pjpeg' || $_FILES['uploadedfile']['type']=='image/gif'){
			if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			
				$fs=fopen($filelogo,"w+");
				fwrite($fs,basename( $_FILES['uploadedfile']['name']));
				fclose($fs);
			} else
				echo "There was an error uploading the file, please try again!";
		}
		header('Location: index.php');
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
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bootstrap-responsive.css" />
		<link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
		<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
		
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		
		<script type="text/javascript"  src="../js/jquery-1.10.2.js"></script>
		<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
		<script  type="text/javascript" src="../ckeditor/ckeditor.js"></script>
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
								<li class="dropdown active" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Setup",false); ?><b class="caret"></b></a>
									<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
										<li role="presentation" class='active'><a href="mail.php" tabindex="-1" role="menuitem"><?php $translate->__("Site",false); ?></a></li>
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
		<div class='main'>
					<div class='formcor' style='text-align:center' ><button onclick='javascript:location.href="../index.php"' value='<?php $translate->__("See Frontend",false); ?>' class='btn btn-info'><?php $translate->__("See Frontend",false); ?></button></div>
					<form name="ckform" id="ckform"  method="post"  class='formcor form-inline'>
					<h2 class='titlesec'><?php $translate->__("Database Files Checking",false); ?></h2>
						<input type="submit" name="fcheck" id="fcheck" value="<?php $translate->__("Check Database Files",false); ?>" class="btn"/>
					</form>
					
					<form  name="logoform" id="logoform" enctype="multipart/form-data"  method="POST" class='formcor'>
						<h2 class='titlesec'>Logo</h2>
						<input type="hidden" name="MAX_FILE_SIZE" value="50000" />
						<label><?php $translate->__("Current logo:",false); ?></label><img src='../css/images/<?php if(isset($logo) && rtrim($logo)!='') echo $logo;else echo "logo.png"; ?>' alt='Logo'/><br/><br/>
						<label><?php $translate->__("Upload a New Logo(png, jpeg, jpg, gif; max=5 MB):",false); ?></label><input id="uploadedfile" name="uploadedfile" type="file" />
						<br/><br/>
						<input type="submit" name="uploadlogo" id="uploadlogo" value="<?php $translate->__("Upload New Logo",false); ?>" class="btn btn-success"/>
					</form>
						
					<form name="formdata" id="formdata"  method="post"  class='formcor'>
						<h2 class='titlesec'><?php $translate->__("Meta Information",false); ?></h2>
						<div class='row-fluid'>
							<div class='span2'><?php $translate->__("Meta Description:",false); ?></div><div class='span10'><textarea class='metacont' type="text" id="metadesc" name="metadesc" ><?php if(isset($frontph[2]) && $frontph[2]!='**@****nullo**@****') echo $frontph[2]; ?> </textarea></div>
						</div><br/>
						<div class='row-fluid'>
						<div class='span2'><?php $translate->__("Meta Keywords:",false); ?></div><div class='span10'><input class='metacont' type="text" id="metakey" name="metakey" <?php if(isset($frontph[3]) && $frontph[3]!='**@****nullo**@****') echo 'value="'.$frontph[3].'"'; ?> /></div>
						</div>
						<br/><br/>
						<h2 class='titlesec'><?php $translate->__("Frontend",false); ?></h2>
						
							<label><?php $translate->__("Site Title:",false); ?></label><input type="text" id="pgtit" name="pgtit" <?php if(isset($var[5]) && $var[5]!='**@****nullo**@****') echo 'value="'.$var[5].'"'; ?> required />
						
						
							<label><?php $translate->__("Finished Site Url:",false); ?></label><input type="text" id="urls" name="urls" <?php if(isset($var[4]) && $var[4]!='**@****nullo**@****') echo 'value="'.$var[4].'"'; ?> required />
						
							<div class='row-fluid'>
								<div class='span2'><label><?php $translate->__("Use",false); ?> <a href='http://fittextjs.com/' target='_blank'>FitText</a>?</label></div>
								<div class='span1'><input type="radio" name="enfitetx" value="yes" <?php if(isset($var[18]) && $var[18]=='yes') echo "checked";else if(!isset($var[18])) echo "checked";  ?>/><?php $translate->__("Yes",false); ?> </div>
								<div class='span1'><input type="radio" name="enfitetx" value="no" <?php if(isset($var[18]) && $var[18]=='no') echo "checked"; ?>/><?php $translate->__("No",false); ?></div>
							</div>
							<br/><br/>
							<label><?php $translate->__("Site phrase:",false); ?></label><textarea type="text" id="phrase" name="phrase"><?php if(isset($phrase) && $phrase!='**@****nullo**@****') echo stripslashes($phrase); ?></textarea>
							<br/><br/>
							<label><?php $translate->__("Time Zone:",false); ?></label><input type="text" id="tz" name="tz" <?php if(isset($var[10]) && $var[10]!='**@****nullo**@****') echo 'value="'.$var[10].'"'; ?> required />
							<div class='row-fluid'>
								<div class='span3'><label for='datai'><?php $translate->__("Starting data:",false); ?></label><input type="text" id="datai" name="datai" <?php if(isset($var[0]) && $var[0]!='**@****nullo**@****') echo 'value="'.$var[0].'"'; ?> required /></div>
				
								<div class='span3'><label for='horai'><?php $translate->__("Starting hour(hh):",false); ?></label><input type="text" name='horai' id='horai' value='<?php if(count($var)>1)echo $var[1][0];else echo '00'; ?>'/></div>
								<div class='span3'><label for='morai'><?php $translate->__("Starting minute (mm):",false); ?></label><input type="text" name='morai' id='morai' value='<?php if(count($var)>1)echo $var[1][1];else echo '00'; ?>'/></div>
								<div class='span3'><label for='sorai'><?php $translate->__("Starting second(ss):",false); ?></label><input type="text" name='sorai' id='sorai' value='<?php if(count($var)>1)echo $var[1][2];else echo '00'; ?>'/></div>
							</div>
							<div class='row-fluid'>
								<div class='span3'><label for='dataf'><?php $translate->__("Relase date:",false); ?></label><input type="text" id="dataf" name="dataf" <?php if(isset($var[2]) && $var[2]!='**@****nullo**@****') echo 'value="'.$var[2].'"'; ?> required /></div>
								<div class='span3'><label for='horaf'><?php $translate->__("Relase hour (hh):",false); ?></label><input type="text" name='horaf' id='horaf' value='<?php if(count($var)>1)echo $var[3][0];else echo '00'; ?>'/></div>
								<div class='span3'><label for='moraf'><?php $translate->__("Relase minute (mm):",false); ?></label><input type="text" name='moraf' id='moraf' value='<?php if(count($var)>1)echo $var[3][1];else echo '00'; ?>'/></div>
								<div class='span3'><label for='soraf'><?php $translate->__("Relase second (ss):",false); ?></label><input type="text" name='soraf' id='soraf' value='<?php if(count($var)>1)echo $var[3][2];else echo '00'; ?>'/></div>
							</div>
							<div class='row-fluid'>
								<div class='span3'><?php $translate->__("Complete Percent:",false); ?><input type="text" id="perc" name="perc" <?php if(isset($var[6]) && $var[6]!='**@****nullo**@****') echo 'value="'.$var[6].'"'; ?>/></div>
								<div class='span3'><?php $translate->__("Admin email:",false); ?><input type="text" id="emailad" name="emailad" <?php if(isset($var[7]) && $var[7]!='**@****nullo**@****') echo 'value="'.$var[7].'"'; ?> /></div>
							</div>
							<br/><br/>
							<div class='row-fluid'>
								<div class='span3'><label><?php $translate->__("Checking Word:",false); ?></label></div>
								<div class='span3'><input type="text" id="psphrase" name="psphrase" <?php if(isset($var[19]) && $var[19]!='**@****nullo**@****') echo 'value="'.$var[19].'"'; ?> required/></div>
							</div>

							<br/>
							<div class='row-fluid'>
								<div class='span3'><label><?php $translate->__("Show Frontend Contact Form?",false); ?></label><div class='radioform'><input type="radio" name="shcf" value="yes" <?php if(isset($var[8]) && $var[8]=='yes') echo "checked";else if(!isset($var[8])) echo "checked";  ?>/> <?php $translate->__("Yes",false); ?> <input type="radio" name="shcf" value="no" <?php if(isset($var[8]) && $var[8]=='no') echo "checked"; ?>/> <?php $translate->__("No",false); ?></div></div>
								<div class='span3'><label><?php $translate->__("Show Frontend Subscribe Form?",false); ?></label><div class='radioform'><input type="radio" name="shsf" value="yes" <?php if(isset($var[9]) && $var[9]=='yes') echo "checked";else if(!isset($var[9])) echo "checked";  ?>/> <?php $translate->__("Yes",false); ?> <input type="radio" name="shsf" value="no" <?php if(isset($var[9]) && $var[9]=='no') echo "checked"; ?>/> <?php $translate->__("No",false); ?></div></div>
								<div class='span3'><label><?php $translate->__("Show Unsubscribe Link Inside Email Footer?",false); ?></label><div class='radioform'><input type="radio" name="shunl" value="yes" <?php if(isset($var[11]) && $var[11]=='yes') echo "checked";else if(!isset($var[11])) echo "checked";  ?>/> <?php $translate->__("Yes",false); ?> <input type="radio" name="shunl" value="no" <?php if(isset($var[11]) && $var[11]=='no') echo "checked"; ?>/> <?php $translate->__("No",false); ?></div></div>
							</div><br/>
							<label><?php $translate->__("Server Email Restriction",false); ?></label><br/>
							<div class='row-fluid'>
								<div class='span2'><?php $translate->__("Number of email",false); ?></div><div class='span4'><input type="text" id="mailimit" name="mailimit" <?php if(isset($var[14]) && $var[14]!='none') echo 'value="'.$var[14].'"'; ?> /></div><div class='span2'><?php $translate->__("per (in seconds)",false); ?></div><div class='span4'><input type="text" id="pertime" name="pertime" <?php if(isset($var[15]) && $var[15]!='none') echo 'value="'.$var[15].'"'; ?> /></div>
							</div>
							<br/><br/>
							<div class='row-fluid'>
								<div class='span4'><label><?php $translate->__("Show Frontend Clock?",false); ?></label><div class='radioform'><div class='span2'><input type="radio" name="dispclock" value="yes" <?php if(isset($var[16]) && $var[16]=='yes') echo "checked";else if(!isset($var[16])) echo "checked";  ?>/><?php $translate->__("Yes",false); ?></div><div class='span2'> <input type="radio" name="dispclock" value="no" <?php if(isset($var[16]) && $var[16]=='no') echo "checked"; ?>/><?php $translate->__("No",false); ?></div></div></div>
								<div class='span4'><label><?php $translate->__("Show Frontend Progressbar?",false); ?></label><div class='radioform'><div class='span2'><input type="radio" name="dispprog" value="yes" <?php if(isset($var[17]) && $var[17]=='yes') echo "checked";else if(!isset($var[17])) echo "checked";  ?>/><?php $translate->__("Yes",false); ?></div><div class='span2'> <input type="radio" name="dispprog" value="no" <?php if(isset($var[17]) && $var[17]=='no') echo "checked"; ?>/><?php $translate->__("No",false); ?></div></div></div>
							</div><br/><br/>
						<label><?php $translate->__("Progressbar Phrase:",false); ?></label><textarea type="text" id="progph" name="progph" ><?php if(isset($frontph[1]) && $frontph[1]!='**@****nullo**@****') echo stripslashes($frontph[1]); ?></textarea>
						<br/><br/>
						<label><?php $translate->__("Footer Phrase:",false); ?></label><textarea type="text" id="footerph" name="footerph"><?php if(isset($frontph[0]) && $frontph[0]!='**@****nullo**@****') echo stripslashes($frontph[0]); ?></textarea>
						<br/><br/>
						<input onclick='javascript:return false;' type="submit" name="datacom" id="datacom" value="<?php $translate->__("Set",false); ?>" class="btn btn-success"/>
					</form>
					<?php if(isset($var[20])) {?>
						<div class='formcor'><h2 class='titlesec'><?php $translate->__("Cronjob String",false); ?></h2>
							<p><?php $translate->__("If you can't automatically update the Cronjob trough the php function you can try set it by your own, this is the string with the information:",false); ?></p>
							<br/>
							<p id='cronstring'><?php echo $var[20]; ?></p>
						</div>
					<?php } ?>
					<form name="passwordform" id="passwordform"  method="post"  class='formcor'>
					<h2 class='titlesec'><?php $translate->__("Password Change",false); ?></h2>
					
					<div class='row-fluid'>
						<div class='span4'><label><?php $translate->__("Old Password:",false); ?></label><input type="password" id="oldpwd" name="oldpwd" /></div>
						<div class='span4'><label><?php $translate->__("New Password:",false); ?></label><input type="password" id="newpwd" name="newpwd"/></div>
						<div class='span4'><label><?php $translate->__("Repeat new Password:",false); ?></label><input type="password" name='cnewpwd' id='cnewpwd'/></div>
					</div>
					<br/><br/>
						<input onclick='javascript:return false;' type="submit" name="updatepwd" id="updatepwd" value="<?php $translate->__("Update Password",false); ?>" class="btn btn-success"/>
					</form>
					
					<form name="socialform" id="socialform"  method="post"  class='formcor form-horizontal'>
					<h2 class='titlesec'><?php $translate->__("Social Network Link",false); ?></h2>
						<div class="row-fluid">
							<div class='span3'><label>Blogger</label><input type="text" id="blog" name="blog" <?php if(isset($social[0]) && $social[0]!='**@****nullo**@****') echo 'value="'.$social[0].'"'; ?> placeholder='Blogger Link'/></div>
							<div class='span3'><label>DeviantArt</label><input type="text" id='devian' name='devian' <?php if(isset($social[1]) && $social[1]!='**@****nullo**@****') echo 'value="'.$social[1].'"'; ?> placeholder='DeviantArt Link'/></div>
							<div class='span3'><label>Facebook</label><input 	type="text" id="fb" name="fb" <?php if(isset($social[2]) && $social[2]!='**@****nullo**@****') echo 'value="'.$social[2].'"'; ?> placeholder='Facebook Link'/></div>
							<div class='span3'><label>Flickr</label><input type="text" id="fl" name="fl" <?php if(isset($social[3]) && $social[3]!='**@****nullo**@****') echo 'value="'.$social[3].'"'; ?> placeholder='Flickr Link'/></div>
						</div>
						<br/>
						<div class="row-fluid">
							<div class='span3'><label>Linkedin</label><input type="text" id="linkedin" name="linkedin" <?php if(isset($social[4]) && $social[4]!='**@****nullo**@****') echo 'value="'.$social[4].'"'; ?> placeholder='Linkedin Link'/></div>
							<div class='span3'><label>Twitter</label><input type="text" id="tw" name="tw" <?php if(isset($social[5]) && $social[5]!='**@****nullo**@****') echo 'value="'.$social[5].'"';?> placeholder='Twitter Link'/></div>
							<div class='span3'><label>Wordpress</label><input type="text" id="word" name="word" <?php if(isset($social[6]) && $social[6]!='**@****nullo**@****') echo 'value="'.$social[6].'"'; ?> placeholder='Wordpress Link'/></div>
							<div class='span3'><label>Youtube</label><input type="text" id='yb' name='yb' <?php if(isset($social[7]) && $social[7]!='**@****nullo**@****') echo 'value="'.$social[7].'"'; ?> placeholder='Youtube Link'/></div>
						</div>
						<br/><br/>
						<input onclick='javascript:return false;' type="submit" name="updatesocial" id="updatesocial" value="<?php $translate->__("Update Information",false); ?>" class="btn btn-success"/>
					</form>
					
					<form name="logoutfor" id="logoutfor" method="post"  class='formcor'>
						<input type="submit" name="logout" id="logout" value="Logout" class="btn btn-danger"/>
					</form>
				<!--</div>
			</div>-->
		</div>
		<?php } else { ?>
		<div class='row-fluid-fluid main'>
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
		<script type="text/javascript"  src="../js/timezoneautocomplete.js"></script>
		<script type="text/javascript"  src="../js/noty/jquery.noty.js"></script>
		<script type="text/javascript"  src="../js/noty/layouts/top.js"></script>
		<script type="text/javascript"  src="../js/noty/themes/default.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			
			CKEDITOR.replace('phrase');
			CKEDITOR.replace('progph');
			CKEDITOR.replace('footerph');
			
			$('#datai').datepicker({ dateFormat: 'yy-mm-dd' });
			<?php if(isset($var[0]) && $var[0]!='' ){ ?>$("#datai").datepicker("setDate", "<?php echo $var[0] ?>");<?php } ?>
			var dateArray = new String("<?php echo date("Y-m-d");?>").split('-');
			var dateObject = new Date(dateArray[0], dateArray[1]-1, dateArray[2]);
			$("#datai" ).datepicker("option", "maxDate", dateObject);
			
			$('#dataf').datepicker({ dateFormat: 'yy-mm-dd' });
			<?php if(isset($var[2]) && $var[2]!='' ){ ?>$("#dataf" ).datepicker("setDate", "<?php echo $var[2] ?>");<?php } ?>
			var dateArray = new String("<?php if(isset($var[0]))echo $var[0];else echo date("Y-m-d");?>").split('-');
			var dateObject = new Date(dateArray[0], dateArray[1]-1, dateArray[2]);
			$("#dataf" ).datepicker("option", "minDate", dateObject);
			
			$('#datacom').click(function(){
				var metadesc=$('#metadesc').val().replace(/\s+/g,' ');
				var metakey=$('#metakey').val().replace(/\s+/g,' ');
				var pgtit=$('#pgtit').val().replace(/\s+/g,' ');
				var urls=$('#urls').val().replace(/\s+/g,' ');
				var enfitetx=$('input[type=radio][name="enfitetx"]:checked').val();
				var phrase=CKEDITOR.instances.phrase.getData().replace(/\s+/g,' ');
				var tz=$('#tz').val().replace(/\s+/g,'');
				var datai=$('#datai').val().replace(/\s+/g,'');
				var horai=$('#horai').val().replace(/\s+/g,'');
				var morai=$('#morai').val().replace(/\s+/g,'');
				var sorai=$('#sorai').val().replace(/\s+/g,'');
				var dataf=$('#dataf').val().replace(/\s+/g,'');
				var horaf=$('#horaf').val().replace(/\s+/g,'');
				var moraf=$('#moraf').val().replace(/\s+/g,'');
				var soraf=$('#soraf').val().replace(/\s+/g,'');
				var perc=$('#perc').val().replace(/\s+/g,'');
				var emailad=$('#emailad').val().replace(/\s+/g,'');
				var psphrase=$('#psphrase').val().replace(/\s+/g,'');
				var shcf=$('input[type=radio][name="shcf"]:checked').val();
				var shsf=$('input[type=radio][name="shsf"]:checked').val();
				var shunl=$('input[type=radio][name="shunl"]:checked').val();
				var dispclock=$('input[type=radio][name="dispclock"]:checked').val();
				var dispprog=$('input[type=radio][name="dispprog"]:checked').val();
				var mailimit=$('#mailimit').val().replace(/\s+/g,'');
				var pertime=$('#pertime').val().replace(/\s+/g,'');
				var progph=CKEDITOR.instances.progph.getData().replace(/\s+/g,' ');
				var footerph=CKEDITOR.instances.footerph.getData().replace(/\s+/g,' ');
				if($("#psphrase").val().split(/\s+/).length==1){
					if(pgtit.replace(/\s+/g,'')!='' && urls.replace(/\s+/g,'')!='' && tz.replace(/\s+/g,'')!='' && datai.replace(/\s+/g,'')!='' && dataf.replace(/\s+/g,'')!='' && psphrase.replace(/\s+/g,'')!=''){
						var request= $.ajax({
							type: 'POST',
							url: 'function.php',
							data: {act:'save_options',metadesc:metadesc,metakey:metakey,pgtit:pgtit,urls:urls,enfitetx:enfitetx,phrase:phrase,tz:tz,datai:datai,horai:horai,morai:morai,sorai:sorai,dataf:dataf,horaf:horaf,moraf:moraf,soraf:soraf,perc:perc,emailad:emailad,psphrase:psphrase,shcf:shcf,shsf:shsf,shunl:shunl,dispclock:dispclock,dispprog:dispprog,mailimit:mailimit,pertime:pertime,progph:progph,footerph:footerph},
							dataType : 'json',
							success : function (data) {
								if(data[0]=='Saved'){
									if(data.length>1){
										var n = noty({text: '<?php echo $translate->__("The settings have been saved",true); ?>',type:'success',timeout:9000});
										if($('#cronstring').length)
											$('#cronstring').html(data[1]);
										else
											$('#formdata').after('<div class="formcor"><h2 class="titlesec"><?php echo $translate->__("Cronjob String",true); ?></h2><p><?php echo $translate->__("If you can't automatically update the Cronjob trough the php function you can try set it by your own, this is the string with the information:",true); ?></p><br/><p id="cronstring">'+data[1]+'</p></div>');
									}
									else{
										var n = noty({text: '<?php echo $translate->__("This is the first time,the page will be reloaded",true); ?>',type:'success',timeout:9000});
										window.location.reload();
									}
								}
								else if(data[0]=='Empty')
									var n = noty({text: '<?php echo $translate->__("Please Complete all the fields",true); ?>',type:'error',timeout:9000});
								else
									var n = noty({text: '<?php echo $translate->__("A problem has occured,please try again",true); ?>',type:'error',timeout:9000});
							}
						});
						request.fail(function(jqXHR, textStatus){var n = noty({text: textStatus,type:'error',timeout:9000});});
					}
					else
						var n = noty({text: '<?php echo $translate->__("Please Complete all the fields",true); ?>',type:'error',timeout:9000});
				}
				else
					var n = noty({text: '<?php echo $translate->__("The Checking Word must be only one word.",true); ?>',type:'error',timeout:9000});
				return false;
			});
			
			$('#updatepwd').click(function(){
				var oldpwd=$('#oldpwd').val();
				var newpwd=$('#newpwd').val();
				var cnewpwd=$('#cnewpwd').val();
				if(oldpwd.replace(/\s+/g,'')!='' && newpwd.replace(/\s+/g,'')!='' && cnewpwd.replace(/\s+/g,'')!='' && cnewpwd===newpwd){
					var request= $.ajax({
						type: 'POST',
						url: 'function.php',
						data: {act:'update_password',oldpwd:oldpwd,newpwd:newpwd,cnewpwd:cnewpwd},
						dataType : 'json',
						success : function (data) {
							if(data[0]=='Updated'){
								$('#oldpwd').val('');
								$('#newpwd').val('');
								$('#cnewpwd').val('');
								var n = noty({text: '<?php echo $translate->__("Password Updated",true); ?>',type:'success',timeout:9000});
							}
							else if(data[0]=='Error')
								var n = noty({text: '<?php echo $translate->__("A problem has occured,please try again",true); ?>',type:'error',timeout:9000});
							else if(data[0]=='Empty')
								var n = noty({text: '<?php echo $translate->__("Please Complete all the fields",true); ?>',type:'error',timeout:9000});
							else if(data[0]=='Wrong')
								var n = noty({text: '<?php echo $translate->__("Wrong Password",true); ?>',type:'error',timeout:9000});
						}
					});
					request.fail(function(jqXHR, textStatus){var n = noty({text: textStatus,type:'error',timeout:9000});});
				}
				else
					var n = noty({text: '<?php echo $translate->__("The new passwords don't correspond",true); ?>',type:'error',timeout:9000});
				return false;
			});
			
			$('#updatesocial').click(function(){
				var blog=$('#blog').val();
				var devian=$('#devian').val();
				var fb=$('#fb').val();
				var fl=$('#fl').val();
				var linkedin=$('#linkedin').val();
				var tw=$('#tw').val();
				var word=$('#word').val();
				var yb=$('#yb').val();
				
				var request= $.ajax({
					type: 'POST',
					url: 'function.php',
					data: {act:'update_social',blog:blog,devian:devian,fb:fb,fl:fl,linkedin:linkedin,tw:tw,word:word,yb:yb},
					dataType : 'json',
					success : function (data) {
						if(data[0]=='Saved')
							var n = noty({text: '<?php echo $translate->__("Social Network Links Saved",true); ?>',type:'success',timeout:9000});
						else if(data[0]=='Error')
							var n = noty({text: '<?php echo $translate->__("A problem has occured,please try again",true); ?>',type:'error',timeout:9000});
						else if(data[0]=='Empty')
							var n = noty({text: '<?php echo $translate->__("Please Complete all the fields",true); ?>',type:'error',timeout:9000});
					}
				});
				request.fail(function(jqXHR, textStatus){var n = noty({text: textStatus,type:'error',timeout:9000});});
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
				messages:{datai: "Select data",dataf: "Select data ",horaf: "Two digits( ex: 09 12), 00-23",moraf: "Two digits( ex: 09 12), 00-59",soraf: "Two digits( ex: 09 12), 00-59",horai: "Two digits( ex: 09 12), 00-23",morai: "Two digits( ex: 09 12), 00-59",sorai: "Two digits( ex: 09 12), 00-59",urls: "Invalid url ex: http://site.com",folurl: "Invalid url ex: http://site.com/comingsoon/",shcf: "Please choose",shsf: "Please choose",tz: "Please select."}
			});
			
			$("#passwordform").validate(
			{
				rules:{pwd:"required",oldpwd: "required",newpwd: "required",cnewpwd:{required: true,equalTo: "#newpwd"}},
				messages:{cnewpwd: "Password don't match"}
			});
			
			$("#socialform").validate(
			{
				rules:{blog:{url:true},devian:{url:true},fb:{url:true},fl:{url:true},linkedin:{url:true},tw:{url:true},word:{url:true},yb:{url:true}},
				messages:{blog: "Invalid url ex: http://site.com",devian: "Invalid url ex: http://site.com",fb: "Invalid url ex: http://site.com",fl: "Invalid url ex: http://site.com",linkedin: "Invalid url ex: http://site.com",tw: "Invalid url ex: http://site.com",word: "Invalid url ex: http://site.com",yb: "Invalid url ex: http://site.com"}
			});
		});
		</script>
	<?php } ?>
	</body>
</html>