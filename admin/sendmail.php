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
	if(file_exists('../config/tmpinfo.txt')){
		umask(002);
		require_once '../lib/Swift/lib/swift_required.php';
		$readfile='../config/tmpinfo.txt';
		$info=file($readfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		unlink($readfile);
		$manip=$info[0];
		$mailist=json_decode($info[1],true);
		$message = Swift_Message::newInstance();
		$message->setFrom($info[2]);
		$message->setSubject($info[3]);
		$message->setContentType('text/html; charset=UTF-8');
		$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -t');
		$mailer = Swift_Mailer::newInstance($transport);
		
		if(isset($info[7]) && isset($info[8]) && $info[7]!='none' && $info[8]!='none' && $info[7]!=null && $info[8]!=null) $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($info[7], $info[8]));
		$count=count($mailist);
		if(rtrim($info[4])=='yes'){
			for($i=0;$i< $count;$i++){
				$id=($mailist[$i][0]+1)*8;
				$unlink="<p>Click <a href='http://".$info[5].$info[6]."/admin/unsubscribe.php?mail=".$mailist[$i][0]."&id=".$id."'>here</a> if you want to unsubscribe</p></div></body></html>";
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
?>