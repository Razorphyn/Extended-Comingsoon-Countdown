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

require_once '../lib/Swift/lib/swift_required.php';
require_once '../config/stmp.php';

$fileconfig ='../config/config.txt';
$var = file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if($argv[1]==$var[19] || $argv[1]==$var[20]){
	$dir='../config/mails';
	$filemail ='../config/mail.txt';
	$filefnmail ='../config/fnmail.txt';
	$filefnmessage = '../config/fnmessage.txt';
	$filefnfooter = '../config/footermail.txt';

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
	list($anno, $mese, $giorno)=explode('-',trim($var[2]));

	if(date("Y")== $anno && trim($misc[2])=='yes'){
		file_put_contents('if2','');
		$message=Swift_Message::newInstance();
		$message->setFrom($misc[0]);
		$message->setSubject($misc[1]);
		$message->setContentType('text/html; charset=UTF-8');	
		
		if($smailservice==0)
			$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -t');
		else if($smailservice==1){
			if($smailssl==0)
				$transport = Swift_SmtpTransport::newInstance($settingmail,$smailport);
			else if($smailssl==1)
				$transport = Swift_SmtpTransport::newInstance($settingmail,$smailport,'ssl');
			else if($smailssl==2)
				$transport = Swift_SmtpTransport::newInstance($settingmail,$smailport,'tls');
			else
				exit();
			if($smailauth==1){
				$transport->setUsername($smailuser);
				include_once ('endecrypt.php');
				$smailpassword=base64_decode($smailpassword);
				$e = new Encryption(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
				$smailpassword = $e->decrypt($smailpassword, $skey);

				$transport->setPassword($smailpassword);
			}
			
		}
		else
			exit();

		$mailer = Swift_Mailer::newInstance($transport);
		
		if(isset($var[14]) && isset($var[15]) && $var[14]!='none' && $var[15]!='none') $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($var[14], $var[15]));
		
		$main="<html><body>".$bod.'<div id="footer" style="display:block;clear:both;width:100%;position:relative;margin:10px 0;border-top:1px solid #000">'.$footer;
		$count=count($mailist);
		if(trim($var[11])=='yes'){
			for($i=0;$i< $count;$i++){
				$unlink="<p>Click <a href='http://".$var[12].$var[13]."/admin/unsubscribe.php?mail=".$mailist[$i][0]."&id=".(($mailist[$i][1]+1)*8)."'>here</a> if you want to unsubscribe</p></div></body></html>";
				$plain=str_replace('&nbsp;',' ',str_replace('&','&amp;',$main.$unlink));
				$plain=convert_html_to_text($plain);
				$message->setBody($plain,'text/plain');						
				$message->addPart($main.$unlink,'text/html');
				$message->setTo($mailist[$i][0]);
				$mailer->send($message);
			}
		}
		else{
			$manip=$main."</div></body></html>";
			$plain=str_replace('&nbsp;',' ',str_replace('&','&amp;',$manip));
			$plain=convert_html_to_text($plain);
			$message->setBody($plain,'text/plain');						
			$message->addPart($manip,'text/html');
			for($i=0;$i< $count;$i++){
				$message->setTo($mailist[$i][0]);
				$mailer->send($message);
			}
		}
	}
}
/******************************************************************************
 * Copyright (c) 2010 Jevon Wright and others.
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *    Jevon Wright - initial API and implementation
 ****************************************************************************/

function convert_html_to_text($html) {
	//libxml_use_internal_errors(true);
	$html = fix_newlines($html);

	$doc = new DOMDocument();
	if (!$doc->loadHTML($html))
		throw new Html2TextException("Could not load HTML - badly formed?", $html);

	$doc->loadHTML($html);
	$output = iterate_over_node($doc);

	// remove leading and trailing spaces on each line
	$output = preg_replace("/[ \t]*\n[ \t]*/im", "\n", $output);

	// remove leading and trailing whitespace
	$output = trim($output);

	return $output;
}
function fix_newlines($text) {
	// replace \r\n to \n
	$text = str_replace("\r\n", "\n", $text);
	// remove \rs
	$text = str_replace("\r", "\n", $text);

	return $text;
}
function next_child_name($node) {
	// get the next child
	$nextNode = $node->nextSibling;
	while ($nextNode != null) {
		if ($nextNode instanceof DOMElement)
			break;
		$nextNode = $nextNode->nextSibling;
	}
	$nextName = null;
	if ($nextNode instanceof DOMElement && $nextNode != null)
		$nextName = strtolower($nextNode->nodeName);

	return $nextName;
}
function prev_child_name($node) {
	// get the previous child
	$nextNode = $node->previousSibling;
	while ($nextNode != null) {
		if ($nextNode instanceof DOMElement) {break;}
		$nextNode = $nextNode->previousSibling;
	}
	$nextName = null;
	if ($nextNode instanceof DOMElement && $nextNode != null) {$nextName = strtolower($nextNode->nodeName);}
	return $nextName;
}
function iterate_over_node($node) {
	if ($node instanceof DOMText) {return preg_replace("/\\s+/im", " ", $node->wholeText);}
	if ($node instanceof DOMDocumentType) {return "";}
	$nextName = next_child_name($node);
	$prevName = prev_child_name($node);
	$gmane = strtolower($node->nodeName);

	// start whitespace
	switch ($gmane) {
		case "hr":
			return "------\n";
		case "style":case "head":case "title":case "meta":case "script":
			return "";
		case "h1":case "h2":case "h3":case "h4":case "h5":case "h6":
			$output = "\n";
			break;
		case "p":case "div":
			$output = "\n";
			break;
		default:
			$output = "";
			break;
	}

	// debug
	//$output .= "[$gmane,$nextName]";

	for ($i = 0; $i < $node->childNodes->length; $i++) {$n = $node->childNodes->item($i);$text = iterate_over_node($n);$output .= $text;}

	// end whitespace
	switch ($gmane) {
		case "style":case "head":case "title":case "meta":case "script":
			return "";

		case "h1":case "h2":case "h3":case "h4":case "h5":case "h6":
			$output .= "\n";
			break;

		case "p":case "br":
			if ($nextName != "div")
				$output .= "\n";
			break;

		case "div":
			// add one line only if the next child isn't a div
			if ($nextName != "div" && $nextName != null)
				$output .= "\n";
			break;

		case "a":
			// links are returned in [text](link) format
			$href = $node->getAttribute("href");
			if ($href == null) {
				// it doesn't link anywhere
				if ($node->getAttribute("name") != null) {
					$output = "[$output]";
				}
			} else {
				if ($href == $output) {
					// link to the same address: just use link
					$output;
				} else {
					// replace it
					$output = "[$output]($href)";
				}
			}
			// does the next node require additional whitespace?
			switch ($nextName) {
				case "h1": case "h2": case "h3": case "h4": case "h5": case "h6":
					$output .= "\n";
					break;
			}
		default:
			break;
	}
	return $output;
}
class Html2TextException extends Exception {
	var $more_info;
	public function __construct($message = "", $more_info = "") {parent::__construct($message);$this->more_info = $more_info;}
}
?>