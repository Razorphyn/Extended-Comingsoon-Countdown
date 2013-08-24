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
		$plain=convert_html_to_text(str_replace('&','&amp;',str_replace('&nbsp;',' ',$file[1])));
		
		if(isset($info[7]) && isset($info[8]) && $info[7]!='none' && $info[8]!='none' && $info[7]!=null && $info[8]!=null) $mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin($info[7], $info[8]));
		$count=count($mailist);
		if(rtrim($info[4])=='yes'){
			for($i=0;$i< $count;$i++){
				$id=($mailist[$i][0]+1)*8;
				$unlink="<p>Click <a href='http://".$info[5].$info[6]."/admin/unsubscribe.php?mail=".$mailist[$i][0]."&id=".$id."'>here</a> if you want to unsubscribe</p></div></body></html>";
				
				$plain=convert_html_to_text(str_replace('&','&amp;',str_replace('&nbsp;',' ',$manip.$unlink)));
				$message->setBody($plain,'text/plain');						
				$message->addPart($manip.$unlink,'text/html');
				$message->setTo($mailist[$i][0]);
				$mailer->send($message);
			}
		}else{
			$manip=$manip."</div></body></html>";
			$plain=convert_html_to_text(str_replace('&','&amp;',str_replace('&nbsp;',' ',$manip)));
			$message->setBody($plain,'text/plain');						
			$message->addPart($manip,'text/html');
			for($i=0;$i< $count;$i++){
				$message->setTo($mailist[$i][0]);
				$mailer->send($message);
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