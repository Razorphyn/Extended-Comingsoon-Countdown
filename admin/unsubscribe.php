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

require_once '../translator/class.translation.php';
if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('../translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);	ini_set('session.auto_start', '0');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo $lang; ?>">
<head>
<title><?php $translate->__("Unsubscription",false); ?></title>
</head>
<body>
<div style='display:block;position:relative;margin:0 auto;top:40%;width:100%;text-align:center;height:100px;background:#f2f2f2'>
<?php
if(isset($_GET['mail']) && isset($_GET['id']) && is_numeric($_GET['id'])){
	$id=(is_numeric($_GET['id']))?(int)$_GET['id']/8-1:exit();
	$mail=preg_replace('/\s+/','',$_GET['mail']);
	
	$filemail='../config/mail.txt';
	$dir='../config/mails';
	umask(002);
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
	if(is_file($dir.'/'.$mail)){
		$mailist=file($dir.'/'.$mail,FILE_IGNORE_NEW_LINES);
		if($mailist[7]==$id){
			if(unlink($dir.'/'.$mail))
				$translate->__("Your email has been removed from our mailing list.",false);
			else
				$translate->__("There was a problem and the operation couldn't be completed,please contact us",false);
		}
		else
			$translate->__("The information don't correspond,please contact us",false);
	}
	else
		$translate->__("Your email doesn't exist in our datatabase",false);
}
else
	echo "<script type='text/javascript'>location.href = '../index.php';</script>"
?>
</div>
</body>
</html>