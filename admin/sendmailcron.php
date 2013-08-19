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
require_once ($argv[2].'/lib/Swift/lib/swift_required.php');
$fileconfig =$argv[2].'/config/config.txt';
$var = file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if($argv[1]==$var[19] || $argv[1]==$var[20]){
	$dir=$argv[2].'/config/mails';
	$filemail =$argv[2].'/config/mail.txt';
	$filefnmail =$argv[2].'/config/fnmail.txt';
	$filefnmessage = $argv[2].'/config/fnmessage.txt';
	$filefnfooter = $argv[2].'/config/footermail.txt';

	
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
	$misc= file($filefnmail, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$bod= file_get_contents($filefnmessage);
	$footer= file_get_contents($filefnfooter);
	if (isset($var[10]))date_default_timezone_set($var[10]);
	list($anno, $mese, $giorno)=explode('-',rtrim($var[2]));

	if(date("Y")== $anno && rtrim($misc[2])=='yes'){
		$message=Swift_Message::newInstance();
		$message->setFrom($misc[0]);
		$message->setSubject($misc[1]);
		$message->setContentType('text/html; charset=UTF-8');	
		
		$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -t');
		$mailer = Swift_Mailer::newInstance($transport);
		
		if(isset($var[14]) && isset($var[15]) && $var[14]!='none' && $var[15]!='none') $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($var[14], $var[15]));
		
		$main="<html><body>".$bod.'<div id="footer" style="display:block;clear:both;width:100%;position:relative;margin:10px 0;border-top:1px solid #000">'.$footer;
		$count=count($mailist);
		if(rtrim($var[11])=='yes'){
			for($i=0;$i< $count;$i++){
				$unlink="<p>Click <a href='http://".$var[12].$var[13]."/admin/unsubscribe.php?mail=".$mailist[$i][0]."&id=".(($mailist[$i][1]+1)*8)."'>here</a> if you want to unsubscribe</p></div></body></html>";
				$message->setBody($main.$unlink);
				$message->setTo($mailist[$i][0]);
				$mailer->send($message);
			}
		}
		else{
			$manip=$main."</div></body></html>";
			$message->setBody($manip);
			for($i=0;$i< $count;$i++){
				$message->setTo($mailist[$i][0]);
				$mailer->send($message);
			}
		}
	}
}
?>