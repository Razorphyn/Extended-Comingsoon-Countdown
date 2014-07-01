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
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('../translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);	ini_set('session.auto_start', '0');
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
	$fileconfig='../config/config.txt';
	$filemail= '../config/mail.txt';
	$dir='../config/scheduled';
	$dirm='../config/mails';
	
	if(!isset($adminpassword) || !is_dir('../config') && !isset($_SESSION['created']) && $_SESSION['created']==true){header('Location: datacheck.php');exit();}

	if(!is_dir('../config/scheduled'))
		mkdir('../config/scheduled');
	
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
	
	$scan=array_values(array_diff(scandir($dir), array('..', '.','.htaccess')));
	$sched=array();
	$count=count($scan);
	if(isset($scan[0])){
		for($i=0;$i<$count;$i++){
			$tmp=file($dir.'/'.$scan[$i],FILE_IGNORE_NEW_LINES);
			$sched[$i]=array($scan[$i],$tmp[4],$tmp[10]);
		}
		unset($tmp);
	}
	//Remove Scheduled
	if(isset($_GET['act']) && isset($_GET['id']) && $_GET['act']=='del'){
		$id=(string)$_GET['id'];
		if(is_file($dir.'/'.$id)){
			$dsched=file($dir.'/'.$id,FILE_IGNORE_NEW_LINES);
			$dsched=$dsched[12];
			if(unlink($dir.'/'.$id)){
				$output = shell_exec('crontab -l');
				$output= implode(PHP_EOL,array_values(array_filter(explode(PHP_EOL,$output), 'strlen')));
				
				$output=str_replace($dsched,'',$output);
				file_put_contents('../config/crontab.txt', $output.PHP_EOL);
				echo exec('crontab ../config/crontab.txt');
				unlink('../config/crontab.txt');
				header('Location: managesched.php');
				exit();
			}
			else
				$translate->__("There was a problem and the operation couldn't be completed,please contact us",false);
		}
		header('Location: managesched.php');
		exit();
	}
	
	//Edit Scheduled
	if(isset($_POST['smail'])){
		$old=preg_replace('/\s+/','',$_POST['fsched']);
		$iden=MD5($_POST['object'].$_POST['sendate'].$_POST['sentime']);
		$bod=preg_replace('/\s+/',' ',$_POST['message']);
		$footer=preg_replace('/\s+/',' ',$_POST['footerfn']);
		
		$nd=file($dir.'/'.$old,FILE_IGNORE_NEW_LINES);
		
		$_POST['shtb']=($_POST['shtb']=='yes')? 'yes':'no';

		$scan=array_values(array_diff(scandir($dirm), array('..', '.','.htaccess')));
		$mailist=array();
		$count=count($scan);
		if(isset($scan[0])){
			for($i=0;$i<$count;$i++){
				$mailist[$i]=file($dirm.'/'.$scan[$i],FILE_IGNORE_NEW_LINES);
				$mailist[$i]=array($mailist[$i][2],$mailist[$i][7]);
			}
		}
		$_POST['shtb']=($_POST['shtb']=='yes')? $_POST['shtb']:'no';
		if($_POST['shtb']=='no'){
			$c=count($_POST['semail']);
			for($i=0,$j=0;$i<$count;$i++){
				$key = array_search($_POST['semail'], $mailist[$i]);
				if($key==1){
					$mailist[$j]=array($mailist[$i][0],$mailist[$i][1]);
					$j++;
				}
				if($j==$c)
					break;
			}
			for($i=$c;$i<$count;$i++)
				unset($mailist[$i]);
		}
		
		if(is_file($dir.'/'.$old) && $old==$iden || !is_file($dir.'/'.$iden)){
			rename($dir.'/'.$old,$dir.'/'.$iden);
			$nd[0]=$bod;
			$nd[1]=$footer;
			$nd[2]=json_encode($mailist);
			$nd[3]=$_POST['sender'];
			$nd[4]=$_POST['object'];
			$nd[10]=$_POST['sendate'].' '.$_POST['sentime'];
			$nd[11]=$_POST['shtb'];
			$find=$nd[12];
			
			
			list($anno,$mese,$giorno)=explode('-',$_POST['sendate']);
			list($ora,$minuto)=explode(':',$_POST['sentime']);

			$diff=(int)get_timezone_offset($var[10]);
			if($diff!=0)
				list($ora, $giorno ,$mese)=explode('-',serverinsdata($diff,$ora,$giorno,$mese,$anno));
				
			$change="$minuto $ora $giorno $mese * php5-cli ".realpath(dirname(__FILE__))."/sendsched.php ".$iden." ".$anno;
			$nd[12]=$change;
			file_put_contents($dir.'/'.$iden,implode("\n",$nd));
			
			$output = shell_exec('crontab -l');
			$output= implode(PHP_EOL,array_values(array_filter(explode(PHP_EOL,$output), 'strlen')));
			
			$output=str_replace($find,$change,$output);
			file_put_contents('../config/crontab.txt', $output.PHP_EOL);
			echo exec('crontab ../config/crontab.txt');
			unlink('../config/crontab.txt');
			header('Location: managesched.php');
			exit();
		}
		else
			$error='This Scheduled Mail Already Exist';
	}
	
	if(isset($_POST['fcheck'])){
		header('Location: datacheck.php');
	}
	
}

?>
<!DOCTYPE html>
<html  lang="<?php echo $lang; ?>">
	<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width">
    <title><?php $translate->__("Manage Subscriptions",false); ?></title>
	
	
	<!--[if lt IE 9]><script src="../js/html5shiv-printshiv.js"></script><![endif]-->
	<link rel="stylesheet" href="../css/bootstrap.min.css" />
   
	<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
	<link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
	
	<link rel="stylesheet" href="../lib/DataTables/css/jquery.dataTables.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	
	<script type="text/javascript"  src="../js/jquery.js"></script>
	<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
	
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
								<li class="dropdown" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Setup",false); ?> <b class="caret"></b></a>
									<ul class="dropdown-menu" role="menu">
										<li role="presentation"><a href="index.php" tabindex="-1" role="menuitem"><?php $translate->__("Site",false); ?></a></li>
										<li role="presentation"><a href="mail_setting.php" tabindex="-1" role="menuitem"><?php $translate->__("Mail",false); ?></a></li>
									</ul>
								</li>
								<li class="dropdown active" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Mail",false); ?><b class="caret"></b></a>
									<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
										<li role="presentation"><a href="mail.php" tabindex="-1" role="menuitem"><?php $translate->__("Send Mail",false); ?></a></li>
										<li role="presentation"  class='active'><a href="managesched.php" tabindex="-1" role="menuitem"><?php $translate->__("Manage Scheduled Mail",false); ?></a></li>
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
			<div  class='formcor'>
				<?php 
					if(isset($_GET['act']) && isset($_GET['id']) && $_GET['act']=='edit'){
						$fsched=preg_replace('/\s+/','',$_GET['id']);
						if(is_file($dir.'/'.$fsched)){
							$sched=file($dir.'/'.$fsched,FILE_IGNORE_NEW_LINES);
							list($d,$time)=explode(' ',$sched[10]); 
							$sched[2]=json_decode($sched[2],true);
							$scan=array_values(array_diff(scandir($dirm), array('..', '.','.htaccess')));
							$mailist=array();
							$count=count($scan);
							if(isset($scan[0])){
								for($i=0;$i<$count;$i++){
									$mailist[$i]=file($dirm.'/'.$scan[$i],FILE_IGNORE_NEW_LINES);
									$mailist[$i]=array($mailist[$i][2],$mailist[$i][7]);
								}
							}
							if (isset($var[10]))date_default_timezone_set($var[10]);
				?>
						<form name="formmail" id="formmail"  method="post" >
							<h2 class='titlesec'><?php $translate->__("Edit Scheduled Mail",false); ?></h2>
							<input  type="hidden" id='fsched' name='fsched' value='<?php echo $fsched; ?>'/>
							<div class='row form-group scheduled'>
								<div class='col-xs-6 col-sm-6 col-md-2'><label><?php $translate->__("Date:",false); ?></label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input type="text" id="sendate" name="sendate" class='form-control' value='<?php echo $d; ?>' /></div>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Time:",false); ?> (hh:mm)</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input type="time" id="sentime" name="sentime" pattern='([01]?[0-9]|[2]?[0-3]):[0-5][0-9]' value='<?php echo $time; ?>' class='form-control' /></div>
							</div>
							
							<div class='row form-group'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Do you want to contact all the users?",false); ?></label></div>
								<div class='col-xs-12 col-sm-3 col-md-1'><label class="radio"><input type="radio" name="shtb" value="yes" onclick='javascript:$("#conttab").css("display","none");' <?php if($sched[11]=='yes') echo 'checked' ?> /><?php $translate->__("Yes",false); ?></label></div>
								<div class='col-xs-12 col-sm-3 col-md-1'><label class="radio"><input type="radio" name="shtb" value="no" onclick='javascript:$("#conttab").css("display","block");' <?php if($sched[11]=='no') echo 'checked' ?> /><?php $translate->__("No",false); ?></label></div>
							</div>
							<div class='form-group' id='conttab'>
								<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mails" width="100%">
									<thead>
									<tr>
										<th><?php $translate->__("Send Mail",false); ?></th>
										<th><?php $translate->__("Mail",false); ?></th>
									</tr>
									</thead>
									<tbody>
									<?php 
										$count=count($mailist);
										if($count>0){
											if($sched[11]=='no'){
												for($i=0;$i<$count;$i++){
													if(array_search($mailist[$i][1],$sched[2])>=0)
														echo '<tr><td><input type="checkbox" class="mal" name="semail[]" value="'.$mailist[$i][1].'" checked/></td><td>'.$mailist[$i][0].'</td></tr>';
													else
														echo '<tr><td><input type="checkbox" class="mal" name="semail[]" value="'.$mailist[$i][1].'"/></td><td>'.$mailist[$i][0].'</td></tr>';
												}
											}
											else
												for($i=0;$i<$count;$i++)
														echo '<tr><td><input type="checkbox" class="mal" name="semail[]" value="'.$mailist[$i][1].'"/></td><td>'.$mailist[$i][0].'</td></tr>';
										}
									?>
									</tbody>
								</table>
							</div>
							
							<div class='row form-group'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Sender:",false); ?></label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input type="text" class='form-control' id="sender" name="sender" value='<?php echo $sched[3]; ?>' required/></div>
								<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Object:",false); ?></label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input	type="text" class='form-control' id="object" name="object" value='<?php echo $sched[4]; ?>' required/></div>
							</div>
							
							<div class='form-group'>
								<div class='row'>
									<div class='col-xs-12'><label><?php $translate->__("Message:",false); ?></label></div>
									<div class='col-xs-12'><textarea id='message' name='message' rows='10' cols='100' required > <?php echo $sched[0]; ?></textarea></div>
								</div>
							</div>
							<div class='form-group'>
								<div class='row'>
									<div class='col-xs-12'><label><?php $translate->__("Use Different Footer:",false); ?></label></div>
									<div class='col-xs-12'><textarea id='footerfn' name='footerfn' class='footerfn' rows='10' cols='100'><?php echo $sched[1]; ?></textarea></div>
								</div>
							</div>
							<input type="submit" name="smail" id="smail" value="<?php $translate->__("Save Changes",false); ?>" class="btn btn-success"/>
						</form>
				<?php 	
						}
					} else if(isset($error)) {
						$scan=array_values(array_diff(scandir($dirm), array('..', '.','.htaccess')));
						$nnn=array();
						$count=count($scan);
						if(isset($scan[0])){
							for($i=0;$i<$count;$i++){
								$nnn[$i]=file($dirm.'/'.$scan[$i],FILE_IGNORE_NEW_LINES);
								$nnn[$i]=array($nnn[$i][2],$nnn[$i][7]);
							}
						}
						if (isset($var[10]))date_default_timezone_set($var[10]);
				?> 
						<form name="formmail" id="formmail"  method="post" >
							<strong><?php echo $error;?></strong>
							<h2 class='titlesec'><?php $translate->__("Edit Scheduled Mail",false); ?></h2>
							<input  type="hidden" id='fsched' name='fsched' value='<?php echo $fsched; ?>' />
							<div class='row form-group scheduled'>
								<div class='col-xs-12 col-sm-3 col-md-3'><label><?php $translate->__("Date:",false); ?></label></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><input type="text" id="sendate" name="sendate" value='<?php echo $_POST['sendate']; ?>' required /></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Time:",false); ?> (hh:mm)</label></div>
								<div class='col-xs-12 col-sm-6 col-md-3'><input type="time" id="sentime" name="sentime" pattern='([01]?[0-9]|[2]?[0-3]):[0-5][0-9]' value='<?php echo $_POST['sentime']; ?>' required /></div>
							</div>
							<br/><br/>
							<div class='row form-group'>
								<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Do you want to contact all the users?",false); ?></label></div>
								<div class='col-xs-12 col-sm-3 col-md-1'><label class="radio"><input type="radio" name="shtb" value="yes" onclick='javascript:$("#conttab").css("display","none");' <?php if($_POST['shtb']=='yes') echo 'checked' ?> /><?php $translate->__("Yes",false); ?></label></div>
								<div class='col-xs-12 col-sm-3 col-md-1'><label class="radio"><input type="radio" name="shtb" value="no" onclick='javascript:$("#conttab").css("display","block");' <?php if($_POST['shtb']=='no') echo 'checked' ?> /><?php $translate->__("No",false); ?></label></div>
							</div>
							<div class='form-group' id='conttab'>
								<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mails" width="100%">
									<thead>
									<tr>
										<th><?php $translate->__("Send Mail",false); ?></th>
										<th><?php $translate->__("Mail",false); ?></th>
									</tr>
									</thead>
									<tbody>
									<?php 
										$count=count($nnn);
										if($count>0){
											if($sched[11]=='no'){
												for($i=0;$i<$count;$i++){
													if(array_search($nnn[$i][1],$mailist)>=0)
														echo '<tr><td><input type="checkbox" class="mal" name="semail[]" value="'.$nnn[$i][1].'" checked/></td><td>'.$nnn[$i][0].'</td></tr>';
													else
														echo '<tr><td><input type="checkbox" class="mal" name="semail[]" value="'.$nnn[$i][1].'"/></td><td>'.$nnn[$i][0].'</td></tr>';
												}
											}
											else
												for($i=0;$i<$count;$i++)
														echo '<tr><td><input type="checkbox" class="mal" name="semail[]" value="'.$nnn[$i][1].'"/></td><td>'.$nnn[$i][0].'</td></tr>';
										}
									?>
									</tbody>
								</table>
							</div>
							<div class='row form-group'>
								<div class='col-xs-12 col-sm-6 col-md-4'><label><?php $translate->__("Sender:",false); ?></label><input type="text" id="sender" name="sender" value='<?php echo $_POST['sender']; ?>' required/></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><label><?php $translate->__("Object:",false); ?></label><input	type="text" id="object" name="object" value='<?php echo $_POST['object']; ?>' required/></div>
							</div>
								
							<textarea id='message' name='message' rows='10' cols='100' required></textarea>
								<br/><br/>
								<label><?php $translate->__("Use Different Footer:",false); ?></label><textarea id='footerfn' name='footerfn' class='footerfn' rows='10' cols='100'></textarea>
							<br/><br/>
							
							<div class='form-group'>
								<div class='row'>
									<div class='col-xs-12'><label><?php $translate->__("Message:",false); ?></label></div>
									<div class='col-xs-12'><textarea id='message' name='message' rows='10' cols='100' required > <?php echo $_POST['message']; ?></textarea></div>
								</div>
							</div>
							<div class='form-group'>
								<div class='row'>
									<div class='col-xs-12'><label><?php $translate->__("Use Different Footer:",false); ?></label></div>
									<div class='col-xs-12'><textarea id='footerfn' name='footerfn' class='footerfn' rows='10' cols='100'><?php echo $_POST['footerfn']; ?></textarea></div>
								</div>
							</div>
							<input type="submit" name="smail" id="smail" value="<?php $translate->__("Save Changes",false); ?>" class="btn btn-success"/>
						</form>
				<?php	
					} else { 
				?>
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="tbschedmails" width="100%">
					<thead>
					</thead>
					<tbody>
					<?php
					$count=count($sched);
					if(isset($sched[0]) && $count>0){
						for($i=0;$i<$count;$i++){
							echo '<tr><td>'.($i+1).'</td><td>'.$sched[$i][1].'</td><td>'.$sched[$i][2].'</td><td><div class="btn-group"><button class="btn btn-danger" title="'.$translate->__("Delete Contact",true).'" onclick="javascript:contrch(\'managesched.php?act=del&id='.$sched[$i][0].'\');" ><span class="glyphicon glyphicon-remove"></span></button><button class="btn btn-info" title="'.$translate->__("Edit Contact",true).'" onclick="javascript:location.href=\'managesched.php?act=edit&id='.$sched[$i][0].'\';" ><span class="glyphicon glyphicon-edit"></span></button></div></td></tr>';
						}
					}
					?>
					</tbody>
				</table>
				<?php } ?>
			</div>
			<form name="logoutfor" id="logoutfor"  method="post"  class='formcor'>
				<input type="submit" name="logout" id="logout" value="Logout" class="btn btn-danger"/>
			</form>
			<iframe id="hidden_upload" name="hidden_upload" style="display:none"></iframe>
		</div>
		<?php } else { ?>
		<div class='container main'>
			<form name="formdata" id="formdata" method="post"  class='formcor form-inline'>
				<h2 class='titlesec'>Login</h2>
					<!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" /><![endif]-->
					<?php if(isset($acc) && $acc==false){ ?>
					<div class='row'><div class='col-xs-12 col-sm-3 col-md-12'><p><?php $translate->__("Wrong Password",false); ?><p></div></div>
					<?php } ?>
				<div class='row'>
					<div class='col-xs-12 col-sm-6 col-md-2'><label>Password</label></div>
					<div class='col-xs-12 col-sm-6 col-md-4'><input type="password" id="pwd" name="pwd" placeholder="Password"></div>
				</div>
				<br/><br/>
				<input type="submit" name="loginb" id="loginb" value="Login" class="btn btn-success"/>
			</form>
		</div>
		<?php } 
		?>
	<?php if(isset($_SESSION['views']) && $_SESSION['views']==1946 ){ ?>
		<script type="text/javascript"  src="../lib/DataTables/js/jquery.dataTables.js"></script>
		<script type="text/javascript"  src="../ckeditor/ckeditor.js"></script>
		<script type="text/javascript"  src="../js/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() { 
				<?php if(isset($_GET['act']) || isset($error)){ ?>
					CKEDITOR.replace('message');
					CKEDITOR.replace('footerfn'); 
					$('#mails').dataTable({
						"sDom": "<'row'<'span6'l><'span6'f>r>t<<'span6'i><'span6'p>>",
						"aoColumns": [
							{ "sTitle": "<?php echo $translate->__("Send Mail",true); ?>","bSortable": false,"bSearchable":false,'sWidth':'60px'},
							{ "sTitle": "<?php echo $translate->__("Mail",true); ?>"}
						]
					});
				<?php }	?>
				
				
				$('#sendate').datepicker({ dateFormat: 'yy-mm-dd' });
				var dateArray = new String("<?php echo date("Y-m-d");?>").split('-');
				var dateObject = new Date(dateArray[0], dateArray[1]-1, dateArray[2]);
				$("#sendate" ).datepicker("option", "minDate", dateObject);
				
				$('#tbschedmails').dataTable({
					"sDom": "<'row'<'span6'l><'span6'f>r>t<<'span6'i><'span6'p>>",
					"aoColumns": [
						{ "sTitle": "<?php echo $translate->__("Number",true); ?>",'sWidth':'60px'},
						{ "sTitle": "<?php echo $translate->__("Subject",true); ?>"},
						{ "sTitle": "<?php echo $translate->__("Date",true); ?>",'sWidth':'160px' },
						{ sTitle: "<?php echo $translate->__("Toggle",true); ?>",bSortable: false,bSearchable:false, sWidth:'95px' }
					]
				});
			} );
			function contrch(link){
				if(window.confirm("Do you want to delete this scheduled mail?"))
					window.location.href = link;
			}
		</script>
	<?php } ?>
	</body>
</html>

<?php

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