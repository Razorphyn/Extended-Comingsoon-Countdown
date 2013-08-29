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
	
	$fileconfig='../config/config.txt';
	$passfile='../config/pass.php';
	$socialfile='../config/social.txt';
	$filefnmail='../config/fnmail.txt';
	$filefnmessage= '../config/fnmessage.txt';
	$filefnfooter= '../config/footermail.txt';
	$filelogo= '../config/logo.txt';
	$filefrontmess= '../config/frontmess.txt';
	$frontotinfo= '../config/indexfooter.txt';
	$filemail='../config/mail.txt';
	$dir='../config/mails';

	if(isset($_POST['expcsvmail'])){
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
				array_unshift($mailist[$i], $mailist[$i][7]);
				unset($mailist[$i][8]);
			}
		}
		$fp = fopen('../config/exported_mails.csv', 'w+');
		foreach($mailist as $df)
			fputcsv($fp, $df);
		fclose($fp);
		header("Content-Type: text/csv");
		header("Cache-Control: no-store, no-cache");
		header("Content-Description: List of Emails");
		header("Content-Disposition: attachment;filename=exported_mails.csv");
		header("Content-Transfer-Encoding: binary");
		readfile('../config/exported_mails.csv');
		unlink('../config/exported_mails.csv');
	}

	else if(isset($_POST['act']) && $_POST['act']=='send_mail'){
		
		$_POST['senmail']=trim(preg_replace('/\s+/','',$_POST['senmail']));
		$_POST['senname']=trim(preg_replace('/\s+/',' ',$_POST['senname']));
		$_POST['subject']=trim(preg_replace('/\s+/',' ',$_POST['subject']));
		
		if($_POST['senmail']!='' && $_POST['senname']!='' && $_POST['subject']!='' && trim(preg_replace('/\s+/','',$_POST['message']))!='' && filter_var($_POST['senmail'], FILTER_VALIDATE_EMAIL)){
			require_once '../translator/class.translation.php';
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(is_file('../translator/lang/'.$lang.'.csv'))$translate = new Translator($lang);else $translate = new Translator('en');}else $translate = new Translator('en');

			$var=file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$headers = "From:".$_POST['senmail']."\r\n CC:".$_POST['senmail']."\r\n MIME-Version: 1.0\r\n Content-Type: text/plain; charset=UTF-8\r\n";
			$body=$_POST['message'];
			$message="------".$translate->__("Information",true)."------\n".$translate->__("Name",true).": ".$_POST['senname']."\n ".$translate->__("Mail",true).": ".$_POST['senmail']."\n ".$translate->__("Telephone",true).": ".$_POST['senphone']."\n------------\n".$body;
			if(mail($var[7], $_POST['subject'], $message ,$headers))
				echo json_encode(array(0=>'Sent'));
			else
				echo json_encode(array(0=>'Error'));
		}
		else{
			echo json_encode(array(0=>'Error'));
			exit();
		}
	}
	
	else if(isset($_POST['act']) && $_POST['act']=='subscribe'){
		if(isset($_POST['nameinput']) && trim(preg_replace('/\s+/','',$_POST['nameinput']))!='' && isset($_POST['mailinput']) && trim(preg_replace('/\s+/','',$_POST['mailinput']))!='' && filter_var($_POST['mailinput'], FILTER_VALIDATE_EMAIL)){
			$_POST['nameinput']=trim(preg_replace('/\s+/',' ',$_POST['nameinput']));
			$_POST['mailinput']=trim(preg_replace('/\s+/','',$_POST['mailinput']));
			if(is_file($fileconfig)) $var=file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			if(isset($var[10])) date_default_timezone_set($var[10]);
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

			$name=trim(preg_replace('/\s+/',' ',$_POST['nameinput']));
			$lname=trim(preg_replace('/\s+/',' ',$_POST['lnameinput']));
			$mail=trim(preg_replace('/\s+/','',$_POST['mailinput']));
			if(is_file($dir.'/'.$mail))
				echo json_encode(array(0=>'Already'));
			else{
				$scan=array_values(array_diff(scandir($dir), array('..', '.','.htaccess')));
				$mailist=array();
				$count=count($scan);
				$id=0;
				if(isset($scan[0])){
					for($i=0;$i<$count;$i++){
						$mailist[$i]=file($dir.'/'.$scan[$i],FILE_IGNORE_NEW_LINES);
						if($id<$mailist[$i][7])
							$id=$mailist[$i][7];
					}
					$id++;
				}
				$fs=fopen($filemail,"a+");
				$ip=retrive_ip();
				$info=$name."\n".$lname."\n".$mail."\n".date('H:i:s  d/m/Y')."\n\n".$ip."\n".$_SERVER['HTTP_USER_AGENT']."\n".$id;
				file_put_contents($dir.'/'.$mail,$info);
				echo json_encode(array(0=>'Added'));
			}
		}
		else
			echo json_encode(array(0=>'Empty'));
	}

	else if(isset($_SESSION['views']) && isset($_POST['act']) && $_POST['act']=='post_news'){
		if(isset($_POST['tnews']) && preg_replace('/\s+/','',$_POST['tnews'])!='' && isset($_POST['nnews']) && preg_replace('/\s+/','',$_POST['nnews'])!=''){
			$file='../config/config.txt';
			$filenews= '../config/news.txt';
			if(is_file($file)) $var=file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			if(!is_file($filenews)) file_put_contets($filenews,'');
			date_default_timezone_set($var[10]);
			$fs=fopen($filenews,"a+");
			if(rtrim(file_get_contents($filenews))=='')
				fwrite($fs,rtrim(preg_replace('/\s+/',' ',$_POST['tnews']))."\n");
			else
				fwrite($fs,"\n".rtrim($_POST['tnews'])."\n");
			fwrite($fs,date("d-m-Y H:i:s")."\n".rtrim(preg_replace('/\s+/',' ',$_POST['nnews'])));
			fclose($fs);
			echo json_encode(array(0=>'Added'));
		}
		else
			echo json_encode(array(0=>'Empty'));
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act']) && $_POST['act']=='send_mail_bk'){
		if(isset($_POST['shtb']) && isset($_POST['sender']) && isset($_POST['object']) ){
			$var=file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			
			$dir='../config/mails';
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
			$_POST['shtb']=($_POST['shtb']=='yes')? $_POST['shtb']:'no';
			if($_POST['shtb']=='no'){
				$c=count($_POST['semail']);
				for($i=0,$j=0;$i<$count;$i++){
					if(in_array($mailist[$i][1],$_POST['semail'])){
						$mailist[$j]=array($mailist[$i][0],$mailist[$i][1]);
						$j++;
					}
					if($j==$c)
						break;
				}
				for($i=$c;$i<$count;$i++)
					unset($mailist[$i]);
			}
			$bod=preg_replace('/\s+/',' ',$_POST['message']);
			$footer=preg_replace('/\s+/',' ',$_POST['footerfn']);
			
			if($_POST['sched']=='no'){
				$manip="<html><head></head><body>".$bod.'<div id="footer" style="display:block;clear:both;width:100%;position:relative;margin:10px 0;border-top:1px solid #000">'.$footer;
				file_put_contents('../config/tmpinfo.txt',$manip."\n".json_encode($mailist)."\n".$_POST['sender']."\n".$_POST['object']."\n".$var[11]."\n".$var[12]."\n".$var[13]."\n".$var[14]."\n".$var[15]);
				$ex=$var[21].' '.rtrim(dirname(__FILE__))."/sendmail.php";
				if(substr(php_uname(), 0, 7) == "Windows")
					pclose(popen("start /B ".$ex,"r"));
				else
					shell_exec($ex." > /dev/null 2> /dev/null &");
			}
			else{
				if(!is_dir('../config/scheduled/'))
					mkdir('../config/scheduled');
				$iden=MD5($_POST['object'].$_POST['sdate'].$_POST['stime']);
				if(!file_exists('../config/scheduled/'.$iden)){
					if($_POST['shtb']=='no')
						$mailist=json_encode($mailist);
					else
						$mailist='';
					file_put_contents('../config/scheduled/'.$iden,$bod."\n".$footer."\n".$mailist."\n".$_POST['sender']."\n".$_POST['object']."\n".$var[11]."\n".$var[12]."\n".$var[13]."\n".$var[14]."\n".$var[15]."\n".$_POST['sdate'].' '.$_POST['stime']."\n".$_POST['shtb']);
					list($anno,$mese,$giorno)=explode('-',$_POST['sdate']);
					list($ora,$minuto)=explode(':',$_POST['stime']);
					$diff=(int)get_timezone_offset($var[10]);
					if($diff!=0)
						list($ora, $giorno ,$mese)=explode('-',serverinsdata($diff,$ora,$giorno,$mese,$anno));
					$add="$minuto $ora $giorno $mese * ".$var[21].' '.realpath(dirname(__FILE__))."/sendsched.php ".$iden." ".$anno;
					$output = shell_exec('crontab -l');
					file_put_contents('../config/crontab.txt', $output.$add.PHP_EOL);
					echo exec('crontab ../config/crontab.txt');
					unlink('../config/crontab.txt');
					file_put_contents('../config/scheduled/'.$iden,$bod."\n".$footer."\n".json_encode($mailist)."\n".$_POST['sender']."\n".$_POST['object']."\n".$var[11]."\n".$var[12]."\n".$var[13]."\n".$var[14]."\n".$var[15]."\n".$_POST['sdate'].' '.$_POST['stime']."\n".$_POST['shtb']."\n".$add);

				}
				else
					echo json_encode(array(0=>'This Mail is Already Scheduled'));
			}
			echo json_encode(array(0=>'Sent'));
		}
		else
			echo json_encode(array(0=>'Empty'));
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act']) && $_POST['act']=='update_social'){
		$_POST['blog']=(preg_replace('/\s+/','',$_POST['blog'])=='')? '**@****nullo**@****':$_POST['blog'];
		$_POST['devian']=(preg_replace('/\s+/','',$_POST['devian'])=='')? '**@****nullo**@****':$_POST['devian'];
		$_POST['fb']=(preg_replace('/\s+/','',$_POST['fb'])=='')? '**@****nullo**@****':$_POST['fb'];
		$_POST['fl']=(preg_replace('/\s+/','',$_POST['fl'])=='')? '**@****nullo**@****':$_POST['fl'];
		$_POST['linkedin']=(preg_replace('/\s+/','',$_POST['linkedin'])=='')? '**@****nullo**@****':$_POST['linkedin'];
		$_POST['tw']=(preg_replace('/\s+/','',$_POST['tw'])=='')? '**@****nullo**@****':$_POST['tw'];
		$_POST['word']=(preg_replace('/\s+/','',$_POST['word'])=='')? '**@****nullo**@****':$_POST['word'];
		$_POST['yb']=(preg_replace('/\s+/','',$_POST['yb'])=='')? '**@****nullo**@****':$_POST['yb'];
		
		$fs=fopen($socialfile,"w+");
			fwrite($fs,$_POST['blog']."\n".$_POST['devian']."\n".$_POST['fb']."\n".$_POST['fl']."\n".$_POST['linkedin']."\n".$_POST['tw']."\n".$_POST['word']."\n".$_POST['yb']);
		fclose($fs);
		echo json_encode(array(0=>'Saved'));
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act']) && $_POST['act']=='complete_site_mail'){
		$fs=fopen($filefnmail,"w+");
			fwrite($fs,$_POST['senderfn']."\n".$_POST['objectfn']."\n".$_POST['warnus']);
		fclose($fs);
		$fs=fopen($filefnmessage,"w+");
			fwrite($fs,rtrim(preg_replace('/\s+/',' ',$_POST['messagefn'])));
		fclose($fs);
		echo json_encode(array(0=>'Saved'));
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act']) && $_POST['act']=='update_password'){
		include_once $passfile;
		if($adminpassword==hash('whirlpool',$_POST['oldpwd'])){
			if($_POST['newpwd']===$_POST['cnewpwd'] && preg_replace('/\s+/',' ',$_POST['cnewpwd'])!=''){
				$fs=fopen($passfile,"w+");
					fwrite($fs,'<?php $adminpassword=\''.hash('whirlpool',$_POST['newpwd']).'\'; ?>');
				fclose($fs);
				echo json_encode(array(0=>'Updated'));
			}
			else
				echo json_encode(array(0=>'Empty'));
		}
		else
			echo json_encode(array(0=>'Wrong'));
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act']) && $_POST['act']=='save_common_mail'){
		$fs=fopen($filefnfooter,"w+");
		fwrite($fs,preg_replace('/\s+/',' ',$_POST['footerfn']));
		fclose($fs);
		echo json_encode(array(0=>'Sent'));
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act'])  && $_POST['act']=='save_stmp'){
		$serv=(is_numeric($_POST['serv'])) ? $_POST['serv']:exit();
		$mustang=(string)$_POST['name'];
		$viper=(string)$_POST['mail'];
		$host=(string)$_POST['host'];
		$port=(is_numeric($_POST['port'])) ? $_POST['port']:exit();
		$ssl=(is_numeric($_POST['ssl'])) ? $_POST['ssl']:exit();
		$auth=(is_numeric($_POST['auth'])) ? $_POST['auth']:exit();
		
		$usr=(string)$_POST['usr'];
		$pass=(string)$_POST['pass'];
		if(preg_replace('/\s+/','',$_POST['pass'])!=''){
			$crypttable=array('a'=>'X','b'=>'k','c'=>'Z','d'=>2,'e'=>'d','f'=>6,'g'=>'o','h'=>'R','i'=>3,'j'=>'M','k'=>'s','l'=>'j','m'=>8,'n'=>'i','o'=>'L','p'=>'W','q'=>0,'r'=>9,'s'=>'G','t'=>'C','u'=>'t','v'=>4,'w'=>7,'x'=>'U','y'=>'p','z'=>'F',0=>'q',1=>'a',2=>'H',3=>'e',4=>'N',5=>1,6=>5,7=>'B',8=>'v',9=>'y','A'=>'K','B'=>'Q','C'=>'x','D'=>'u','E'=>'f','F'=>'T','G'=>'c','H'=>'w','I'=>'D','J'=>'b','K'=>'z','L'=>'V','M'=>'Y','N'=>'A','O'=>'n','P'=>'r','Q'=>'O','R'=>'g','S'=>'E','T'=>'I','U'=>'J','V'=>'P','W'=>'m','X'=>'S','Y'=>'h','Z'=>'l');
			$pass=str_split($pass);
			$c=count($pass);
			for($i=0;$i<$c;$i++){
				if(array_key_exists($pass[$i],$crypttable))
					$pass[$i]=$crypttable[$crypttable[$pass[$i]]];
			}
			$pass=implode('',$pass);
		}
		$string='<?php $smailservice='.$serv.";\n".'$smailname=\''.$mustang."';\n".'$settingmail=\''.$viper."';\n".'$smailhost=\''.$host."';\n".'$smailport='.$port.";\n".'$smailssl='.$ssl.";\n".'$smailauth='.$auth.";\n".'$smailuser=\''.$mustang."';\n".'$smailpassword=\''.$mustang."';\n ?>";
		if(file_put_contents('../config/stmp.php',$string))
			echo json_encode(array(0=>'Saved'));
		else
			echo json_encode(array(0=>'Error'));
		exit();
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act'])  && $_POST['act']=='monitoring_code'){

		if(rtrim(preg_replace('/\s+/','',$_POST['code'])!='')){
			$string='<?php $monitoringcode=\''.addslashes($_POST['code']).'\'; ?>';
			if(file_put_contents('../config/monintoring.php',$string))
				echo json_encode(array(0=>'Saved'));
			else
				echo json_encode(array(0=>'Error'));
			exit();
		}
		else
			echo json_encode(array(0=>'Empty'));
	}
	
	else if(isset($_SESSION['views']) && isset($_POST['act']) && $_POST['act']=='save_options'){
		if(preg_replace('/\s+/','',$_POST['dataf'])!='' && preg_replace('/\s+/','',$_POST['datai'])!='' && rtrim(preg_replace('/\s+/','',$_POST['tz'])!='')){
			$var = file($fileconfig, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			
			$dataf=rtrim(preg_replace('/\s+/','',$_POST['dataf']));
			
			list($anno, $mese, $giorno)=explode('-',$dataf);
			
			$horaf=rtrim(preg_replace('/\s+/','',$_POST['horaf']));
			$moraf=rtrim(preg_replace('/\s+/','',$_POST['moraf']));
			$soraf=rtrim(preg_replace('/\s+/','',$_POST['soraf']));
			
			$horaf=($horaf!='' && is_numeric($horaf))? $horaf:'00';
			$moraf=($moraf!='' && is_numeric($moraf))? $moraf:'00';
			$soraf=($soraf!='' && is_numeric($soraf))? $soraf:'00';

			$oraf=$horaf.':'.$moraf.':'.$soraf;

			$datai=rtrim(preg_replace('/\s+/','',$_POST['datai']));
			$horai=rtrim(preg_replace('/\s+/','',$_POST['horai']));
			$morai=rtrim(preg_replace('/\s+/','',$_POST['morai']));
			$sorai=rtrim(preg_replace('/\s+/','',$_POST['sorai']));
			
			$horai=($horai!='' && is_numeric($horai))? $horai:'00';
			$morai=($morai!='' && is_numeric($morai))? $morai:'00';
			$sorai=($soraf!='' && is_numeric($sorai))? $sorai:'00';

			$orai=$horai.':'.$morai.':'.$sorai;

			$url=preg_replace('/\s+/','',$_POST['urls']);
			/*write file*/

			$_POST['urls']=(rtrim(preg_replace('/\s+/','',$_POST['urls'])==''))? '**@****nullo**@****':$_POST['urls'];
			$_POST['phrase']=(rtrim(preg_replace('/\s+/','',$_POST['phrase'])==''))? '**@****nullo**@****':preg_replace('/\s+/',' ',$_POST['phrase']);
			$_POST['footerph']=(rtrim(preg_replace('/\s+/','',$_POST['footerph'])==''))? '**@****nullo**@****':rtrim(preg_replace('/\s+/',' ',$_POST['footerph']));
			$_POST['progph']=(rtrim(preg_replace('/\s+/','',$_POST['progph'])==''))? '**@****nullo**@****':rtrim(preg_replace('/\s+/',' ',$_POST['progph']));
			$_POST['metadesc']=(rtrim(preg_replace('/\s+/','',$_POST['metadesc'])==''))? '**@****nullo**@****':rtrim(preg_replace('/\s+/',' ',$_POST['metadesc']));
			$_POST['metakey']=(rtrim(preg_replace('/\s+/','',$_POST['metakey'])==''))? '**@****nullo**@****':rtrim(preg_replace('/\s+/',' ',$_POST['metakey']));
			$_POST['psphrase']=(rtrim(preg_replace('/\s+/','',$_POST['psphrase'])==''))? '**@****nullo**@****':rtrim(preg_replace('/\s+/',' ',$_POST['psphrase']));
			$_POST['perc']=(rtrim(preg_replace('/\s+/','',$_POST['perc'])==''))? '**@****nullo**@****':$_POST['perc'];
			$_POST['emailad']=(rtrim(preg_replace('/\s+/','',$_POST['emailad'])==''))? '**@****nullo**@****':preg_replace('/\s+/',' ',$_POST['emailad']);
			$_POST['tz']=(rtrim(preg_replace('/\s+/','',$_POST['tz'])==''))? '**@****nullo**@****':preg_replace('/\s+/','',$_POST['tz']);
			$_POST['enfitetx']=(rtrim(preg_replace('/\s+/','',$_POST['enfitetx'])=='yes'))? 'yes':'no';

			$_POST['mailimit']=rtrim(str_replace(' ','',$_POST['mailimit']));
			$_POST['pertime']=rtrim(str_replace(' ','',$_POST['pertime']));
			$_POST['mailimit']=(is_numeric($_POST['mailimit']))? (int)$_POST['mailimit']:'none';
			$_POST['pertime']=(is_numeric($_POST['pertime']))? (int)$_POST['pertime']:'none';
			$_POST['eparam']=(rtrim(preg_replace('/\s+/','',$_POST['eparam'])==''))? 'php5-cli':rtrim(preg_replace('/\s+/','',$_POST['eparam']));

			$horaf=(int)$horaf;
			$giorno=(int)$giorno;
			$mese=(int)$mese;
			$anno=(int)$anno;	

			$diff=(int)get_timezone_offset($_POST['tz']);
			if($diff!=0)
				list($horaf, $giorno ,$mese)=explode('-',serverinsdata($diff,$horaf,$giorno,$mese,$anno));

			$add="$moraf $horaf $giorno $mese * ".$_POST['eparam']." ".realpath(dirname(__FILE__)).'/sendmailcron.php '.$_POST['psphrase'].' '.realpath(dirname(dirname(__FILE__)));

			$fs=fopen($filefrontmess,"w+");
				fwrite($fs,$_POST['phrase']);
			fclose($fs);

			$fs=fopen($frontotinfo,"w+");
				fwrite($fs,$_POST['footerph']."\n".$_POST['progph']."\n".$_POST['metadesc']."\n".$_POST['metakey']);
			fclose($fs);

			$fs=fopen($fileconfig,"w+");
				fwrite($fs,$datai."\n".$orai."\n".$dataf."\n".$oraf."\n".$_POST['urls']."\n".$_POST['pgtit']."\n".$_POST['perc']."\n".$_POST['emailad']."\n".$_POST['shcf']."\n".$_POST['shsf']."\n".$_POST['tz']."\n".$_POST['shunl']."\n".$_SERVER['SERVER_NAME']."\n");
				fwrite($fs,dirname(dirname($_SERVER['SCRIPT_NAME']))."\n".$_POST['mailimit']."\n".$_POST['pertime']."\n".$_POST['dispclock']."\n".$_POST['dispprog']."\n".$_POST['enfitetx']."\n".$_POST['psphrase']."\n".$add."\n".$_POST['eparam']);
			fclose($fs);

			$output = shell_exec('crontab -l');
			if(isset($var[20]) && $var[20]!='' && strrpos($output,$var[20])>=0){
				if(strrpos($output,$var[20])>=0 && strrpos($var[20],realpath(dirname(dirname(__FILE__))))>=0){
					$output=str_replace($var[20],$add,$output);
					file_put_contents('../config/crontab.txt', $output.PHP_EOL);
				}
				else{
					file_put_contents('../config/crontab.txt', $output.$add.PHP_EOL);
				}
			}
			else{
				file_put_contents('../config/crontab.txt', $output.$add.PHP_EOL);
			}
			echo exec('crontab ../config/crontab.txt');
			unlink('../config/crontab.txt');
			if(isset($var[10]) && $var[10]==$_POST['tz'])
				echo json_encode(array(0=>'Saved',1=>$add));
			else
				echo json_encode(array(0=>'Saved'));
		}
		else
			echo json_encode(array(0=>'Empty'));
	}
	
	else{
		echo json_encode(array(0=>'No Action Selected'));
	}
	
	function get_timezone_offset($remote_tz) {
		$origin_tz=date_default_timezone_get();
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return ($offset/3600);
	}
	
	function serverinsdata($diff,$ora,$giorno,$mese,$anno){
		$gimesi=array(1=>31,2=>28,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
		$dora=$ora+$diff;
		if($dora<0){
			$ora=24+$dora;
			$giorno-=1;
			if($giorno<=0){
				$mese-=1;
				$mese=($mese==0)? 12:$mese;
				$giorno=($anno%4==0 && $mese==2)? 29:$gimesi[$mese];
			}	
		}
		else if($dora>23){
			$ora=-1+$dora;
			$giorno+=1;
			$giorno=($giorno>$gimesi[$mese])? 1:$giorno;
			$giorno=($mese==2 && $anno%4==0)? 29:$giorno;
			$mese=($giorno==1)? $mese+1:$mese;
		}
		else
			$ora=$dora;
			
		$fdata=$ora.'-'.$giorno.'-'.$mese;
		return $fdata;
	}
	
	function retrive_ip(){if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];}elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip=$_SERVER['REMOTE_ADDR'];}return $ip;}
?>