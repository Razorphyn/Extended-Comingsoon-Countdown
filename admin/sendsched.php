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
	if(php_sapi_name()=='cli' || php_sapi_name()=='cgi' || php_sapi_name()=='cgi-fcgi' && isset($argv[1])){
		require_once '../lib/Swift/lib/swift_required.php';
		$config='../config/config.txt';
		$readfile='../config/scheduled/'.$argv[1].'.txt';
		$var=file($config, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (isset($var[10]))date_default_timezone_set($var[10]);
		if($argv[2]==date('Y')){
			$info=file($readfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			unlink($readfile);
			
			$output = shell_exec('crontab -l');
			$output= implode(PHP_EOL,array_values(array_filter(explode(PHP_EOL,$output), 'strlen')));
		
			$output=str_replace($info[12],'',$output);
			file_put_contents('../config/crontab.txt', $output.PHP_EOL);
			echo exec('crontab ../config/crontab.txt');
			unlink('../config/crontab.txt');

			if (isset($var[10]))date_default_timezone_set($var[10]);
			
			$manip="<html><body>".$info[0].'<div id="footer" style="display:block;clear:both;width:100%;position:relative;margin:10px 0;border-top:1px solid #bbb">'.$info[1];
			if($info[11]=='no')
				$mailist=json_decode($info[2],true);
			else{
				$scan=array_values(array_diff(scandir('../config/mails'), array('..', '.','.htaccess')));
				$mailist=array();
				$count=count($scan);
				if(isset($scan[0])){
					for($i=0;$i<$count;$i++){
						$mailist[$i]=file('../config/mails/'.$scan[$i],FILE_IGNORE_NEW_LINES);
						$mailist[$i]=array($mailist[$i][2],$mailist[$i][7]);
					}
				}
			}
			$message = Swift_Message::newInstance();
			$message->setFrom($info[3]);
			$message->setSubject($info[4]);
			$message->setContentType('text/html; charset=UTF-8');
			$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -t');
			$mailer = Swift_Mailer::newInstance($transport);
			
			if(isset($info[8]) && isset($info[9]) && $info[8]!='none' && $info[9]!='none' && $info[8]!=null && $info[9]!=null) $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($info[8], $info[9]));
			$count=count($mailist);
			if(rtrim($info[5])=='yes'){
				for($i=0;$i< $count;$i++){
					$id=($mailist[$i][0]+1)*8;
					$unlink="<p>Click <a href='http://".$info[6].$info[7]."/admin/unsubscribe.php?mail=".$mailist[$i][0]."&id=".$id."'>here</a> if you want to unsubscribe</p></div></body></html>";
					$message->setBody($manip.$unlink);
					$message->setTo($mailist[$i][0]);
					$mailer->send($message);
				}
			}else{
				$manip=$manip."</div></body></html>";
				$message->setBody($manip);
				for($i=0;$i< $count;$i++){
					$message->setTo($mailist[$i][0]);
					$mailer->send($message);
				}
			}
		}
	}
?>