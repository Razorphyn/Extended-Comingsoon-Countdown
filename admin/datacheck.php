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
	//REMEBER THE FIRST 0
	$folderperm=0755; //Folders Permissions
	$fileperm=0644; //Files Permissions


	if(!is_dir("session")) {mkdir("session",$folderperm);file_put_contents("session/.htaccess","Deny from All \n IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'");}
	
	ini_set("session.auto_start", "0");
	ini_set("session.hash_function", "sha512");
	ini_set("session.entropy_file", "/dev/urandom");
	ini_set("session.entropy_length", "512");
	ini_set("session.save_path", "session");
	ini_set("session.gc_probability", "1");
	ini_set("session.cookie_httponly", "1");
	ini_set("session.use_only_cookies", "1");
	ini_set("session.use_trans_sid", "0");
	session_name("RazorphynExtendedComingsoon");
	if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		ini_set('session.cookie_secure', '1');
	}
	session_start();
	
	if(isset($_SESSION["created"]) && $_SESSION["created"]==true) {unset($_SESSION["created"]);header("Location: index.php");}
	else{
		$dir="../config";
		$fileconfig=$dir."/config.txt";
		$passfile=$dir."/pass.php";
		$socialfile=$dir."/social.txt";
		$dirmail= $dir."/mails/";
		$filefnmail= $dir."/fnmail.txt";
		$filefnmessage= $dir."/fnmessage.txt";
		$filefnfooter= $dir."/footermail.txt";
		$filelogo= $dir."/logo.txt";
		$filefrontmess= $dir."/frontmess.txt";
		$filenews= $dir."/news.txt";
		$fileindexfoot= $dir."/indexfooter.txt";
		$access= $dir."/.htaccess";

		if(!is_dir($dir)){
			if(mkdir($dir,0700)){
				mkdir("../config/scheduled",$folderperm);
				file_put_contents("../config/scheduled/.htaccess","Deny from All"."\n"."IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'");

				if(!is_file($fileconfig))file_put_contents($fileconfig,"");
				if(!is_file($socialfile))file_put_contents($socialfile,"");
				if(!is_dir($dirmail)){ mkdir($dirmail,$folderperm);file_put_contents($dirmail.".htaccess","Deny from All"."\n"."IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'"); };
				if(!is_file($filefnmail))file_put_contents($filefnmail,"");
				if(!is_file($filefnmessage))file_put_contents($filefnmessage,"");
				if(!is_file($filefnfooter))file_put_contents($filefnfooter,"");
				if(!is_file($filelogo))file_put_contents($filelogo,"");
				if(!is_file($filefrontmess))file_put_contents($filefrontmess,"");
				if(!is_file($filenews))file_put_contents($filenews,"");
				if(!is_file($fileindexfoot))file_put_contents($fileindexfoot,"");
				if(!is_file($passfile))file_put_contents($passfile,'<?php $adminpassword=\''.(hash("whirlpool","admin")).'\'; ?>');
				if(!is_file($access))file_put_contents($access,"Deny from All"."\n"."IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'");
				if(!is_file(".htaccess"))file_put_contents(".htaccess","IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'");
				chmod($fileconfig, $fileperm);
				chmod($socialfile, $fileperm);
				chmod($dirmail, $folderperm);
				chmod($filefnmail, $fileperm);
				chmod($filefnmessage, $fileperm);
				chmod($filefnfooter, $fileperm);
				chmod($filelogo, $fileperm);
				chmod($filefrontmess, $fileperm);
				chmod($filenews, $fileperm);
				chmod($passfile, $fileperm);
				$_SESSION["created"]=true;
			}
		}
		else{
			if(!is_dir("../config/scheduled")){
				if(mkdir("../config/scheduled",$folderperm))
					file_put_contents("../config/scheduled/.htaccess","Deny from All"."\n"."IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'");
			}
			if(!is_file($fileconfig))file_put_contents($fileconfig,"");
			if(!is_file($fileindexfoot))file_put_contents($fileindexfoot,"");
			if(!is_file($socialfile))file_put_contents($socialfile,"");
			if(!is_dir($dirmail)){mkdir($dirmail,folderperm);file_put_contents($dirmail.".htaccess","Deny from All"."\n"."IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'"); };
			if(!is_file($filefnmail))file_put_contents($filefnmail,"");
			if(!is_file($filefnmessage))file_put_contents($filefnmessage,"");
			if(!is_file($filefnfooter))file_put_contents($filefnfooter,"");
			if(!is_file($filelogo))file_put_contents($filelogo,"");
			if(!is_file($filefrontmess))file_put_contents($filefrontmess,"");
			if(!is_file($filenews))file_put_contents($filenews,"");
			if(!is_file($passfile))file_put_contents($passfile,'<?php $adminpassword=\''.(hash("whirlpool","admin")).'\'; ?>');
			if(!is_file($access))file_put_contents($access,"Deny from All"."\n"."IndexIgnore *");
			if(!is_file(".htaccess"))file_put_contents(".htaccess","IndexIgnore * \n Header set X-Frame-Options SAMEORIGIN \n Header set X-XSS-Protection '1; mode=block' \n Header set X-Content-Type-Options 'nosniff'");
			chmod($fileconfig, $fileperm);
			chmod($socialfile, $fileperm);
			chmod($dirmail, $folderperm);
			chmod($filefnmail, $fileperm);
			chmod($filefnmessage, $fileperm);
			chmod($filefnfooter, $fileperm);
			chmod($filelogo, $fileperm);
			chmod($filefrontmess, $fileperm);
			chmod($filenews, $fileperm);
			chmod($passfile, $fileperm);
			$_SESSION["created"]=true;
		}
		echo "<html><head></head><body><button onclick='javascript:ref();' style='position:relative;display:block;margin:0 auto;top:45%'>Return Back</button><script>function ref(){location.reload(true);}</script></body></html>";
	}
?>
