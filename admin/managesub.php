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

	require_once ('../config/pass.php');
	$fileconfig='../config/config.txt';
	$filemail= '../config/mail.txt';
	$dir='../config/mails';

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
	if (isset($var[10]))date_default_timezone_set($var[10]);
	
	$scan=array_values(array_diff(scandir($dir), array('..', '.','.htaccess')));
	$mailist=array();
	$count=count($scan);
	if(isset($scan[0])){
		for($i=0;$i<$count;$i++){
			$mailist[$i]=file($dir.'/'.$scan[$i],FILE_IGNORE_NEW_LINES);
		}
	}
	/*Remove Email*/
	if(isset($_GET['act']) && isset($_GET['id']) && $_GET['act']=='del'){
		$id=(is_numeric($_GET['id']))?(int)$_GET['id']:null;
		$mail=preg_replace('/\s+/','',$_GET['mail']);
		if(is_file($dir.'/'.$mail)){
			$mailist=file($dir.'/'.$mail,FILE_IGNORE_NEW_LINES);
			if($mailist[7]==$id){
				if(unlink($dir.'/'.$mail)){
					header('Location: managesub.php');
					exit();
				}
				else
					$translate->__("There was a problem and the operation couldn't be completed,please contact us",false);
			}
			else
				$translate->__("The information don't correspond,please contact us",false);
		}
		unset($_GET['act']);
		header('Location: managesub.php');
		exit();
	}
	
	/*Edit Mail*/
	if(isset($_POST['smail'])){
		$mail=preg_replace('/\s+/','',$_POST['mail']);
		if(is_file($dir.'/'.$mail)){
			$mailist=file($dir.'/'.$mail,FILE_IGNORE_NEW_LINES);
			rename($dir.'/'.$mail,'../config/mails/'.$_POST['tmail']);
			$mailist[0]=$_POST['tname'];
			$mailist[1]=$_POST['tlname'];
			$mailist[2]=$_POST['tmail'];
			file_put_contents($dir.'/'.$_POST['tmail'],implode("\n",$mailist));
		}
		header('Location: managesub.php');
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
	<link rel="stylesheet" href="../css/bootstrap.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.css" />
	<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
	
	<link rel="stylesheet" href="../lib/DataTables/css/jquery.dataTables.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	
	<script type="text/javascript"  src="../js/jquery-1.10.2.js"></script>
	<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
	
	</head>
	<body>
	<div class='container'>
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
								<li class="dropdown" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Setup",false); ?><b class="caret"></b></a>
									<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
										<li role="presentation"><a href="index.php" tabindex="-1" role="menuitem"><?php $translate->__("Site",false); ?></a></li>
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
								<li class='active'><a href='managesub.php'><?php $translate->__("Manage Subscriptions",false); ?></a></li>
								<li><a href='postnews.php'><?php $translate->__("Post News",false); ?></a></li>
								<li><a href='managenews.php'><?php $translate->__("Manage News",false); ?></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='row-fluid main'>
			<form name="ckform" id="ckform" action="function.php" method="post"  target="hidden_upload" class='formcor form-inline'>
				<h2 class='titlesec'><?php $translate->__("Export Emails to CSV",false); ?></h2>
				<input type="submit" name="expcsvmail" id="expcsvmail" value="<?php $translate->__("Export Emails to CSV",false); ?>" class="btn btn-primary"/>
			</form>
			
			<div  class='formcor'>
				<?php if(isset($_GET['act'])&& isset($_GET['mail']) && $_GET['act']=='edit'){ $mail=preg_replace('/\s+/','',$_GET['mail']);if(is_file($dir.'/'.$mail)){$mailist=file($dir.'/'.$mail,FILE_IGNORE_NEW_LINES); ?>
					<form name="formmail" id="formmail"  method="post" >
						<h2 class='titlesec'><?php $translate->__("Edit Contact Information",false); ?></h2>
						<input  type="hidden" id='mail' name='mail' value='<?php echo $mail; ?>'/>
						<div class='row'>
							<div class='span3'><?php $translate->__("Mail:",false); ?><br/><input type="text" id="tmail" name="tmail" value='<?php echo $mailist[2]; ?>'/></div>
							<div class='span3'><?php $translate->__("Name:",false); ?><br/><input type="text" id="tname" name="tname" value='<?php echo $mailist[0]; ?>'/></div>
							<div class='span3'><?php $translate->__("LastName:",false); ?><br/><input type="text" id="tlname" name="tlname" value='<?php echo $mailist[1]; ?>'/></div>
						</div>
						<br/><br/>
						<input type="submit" name="smail" id="smail" value="<?php $translate->__("Save Changes",false); ?>" class="btn btn-success"/>
					</form>
				<?php }} else { ?>
				<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mails" width="100%">
					<thead>
					</thead>
					<tbody>
					<?php
					$count=count($mailist);
					if(isset($mailist[0]) && $count>0){
						for($i=0;$i<$count;$i++){
							if(!isset($mailist[$i][1])) $mailist[$i][1]='';if(!isset($mailist[$i][2])) $mailist[$i][2]='';if(!isset($mailist[$i][3])) $mailist[$i][3]='';if(!isset($mailist[$i][4])) $mailist[$i][4]='';if(!isset($mailist[$i][5])) $mailist[$i][5]='';if(!isset($mailist[$i][6])) $mailist[$i][6]='';
							echo '<tr><td>'.$mailist[$i][7].'</td><td>'.$mailist[$i][0].'</td><td>'.$mailist[$i][1].'</td><td>'.$mailist[$i][2].'</td><td>'.$mailist[$i][3].'</td><td>'.$mailist[$i][4].'</td><td>'.$mailist[$i][5].'</td><td>'.$mailist[$i][6].'</td><td><div class="btn-group"><button class="btn btn-danger" title="'.$translate->__("Delete Contact",true).'" onclick="javascript:contrch(\'managesub.php?act=del&id='.$mailist[$i][7].'&mail='.$mailist[$i][2].'\');" ><i class="icon-remove"></i></button><button class="btn btn-info" title="'.$translate->__("Edit Contact",true).'" onclick="javascript:location.href=\'managesub.php?act=edit&mail='.$mailist[$i][2].'\';" ><i class="icon-edit"></i></button></div></td></tr>';
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
		<div class='row-fluid main'>
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
		<script type="text/javascript"  src="../lib/DataTables/js/jquery.dataTables.js"></script>
		<script type="text/javascript"  src="../ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('#mails').dataTable({
					"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<<'span6'i><'span6'p>>",
					"aoColumns": [
							{ "sTitle": "<?php echo $translate->__("Id",true); ?>",'sClass':'hidden-phone' },
							{ "sTitle": "<?php echo $translate->__("Name",true); ?>" },
							{ "sTitle": "<?php echo $translate->__("Lastname",true); ?>" },
							{ "sTitle": "<?php echo $translate->__("Email",true); ?>" },
							{ "sTitle": "<?php echo $translate->__("Added Date",true); ?>",'sClass':'hidden-phone' },
							{ "sTitle": "<?php echo $translate->__("Proxy IP",true); ?>","bVisible": false},
							{ "sTitle": "<?php echo $translate->__("User IP",true); ?>",'sClass':'hidden-phone'},
							{ "sTitle": "<?php echo $translate->__("User Agent",true); ?>",'sClass':'visible-desktop'},
							{ "sTitle": "<?php echo $translate->__("Toggle",true); ?>","bSortable": false,"bSearchable":false,'sWidth':'60px' }
						]
					});
				$.extend( $.fn.dataTableExt.oStdClasses, {
					"sWrapper": "dataTables_wrapper form-inline"
				} );
				$('#mails').on('click','.editmail',function(){
					var id=$(this).val();
					
					oTable = $('#deptable').dataTable();
					var node=this.parentNode.parentNode.parentNode;
					var pos=oTable.fnGetPosition(node,null,true);
					var info = oTable.fnGetData(node);
					if($('#'+info['id']).length > 0){
						$('html,body').animate({scrollTop:$('#'+info['id']).offset().top},1500);
					}
					else{
						var editform="<hr><form action='' method='post' class='submit_changes_depa' id='"+info['id']+"'><span>Edit "+info['name']+"</span><button  class='btn btn-link btn_close_form'>Close</button><input type='hidden' name='depa_edit_id' value='"+info['id']+"'/><input type='hidden' name='depa_edit_pos' value='"+pos+"'/><div class='row-fluid'><div class='span2'><label>Name</label></div><div class='span4'><input type='text' name='edit_depa_name' placeholder='Department Name' value='"+info['name']+"'required /></div></div><div class='row-fluid'><div class='span2'><label>Is Active?</label></div><div class='span4'><select name='edit_depa_active' id='activedep'><option value='1'>Yes</option><option value='0'>No</option></select></div><div class='span2'><label>Is Public?</label></div><div class='span4'><select name='edit_depa_public'><option value='1'>Yes</option><option value='0'>No</option></select></div></div><input type='submit' class='btn btn-success submit_changes' value='Submit Changes' onclick='javascript:return false;' /></form>";
						$('#deplist').after(editform);
						var active=(info['active']=='Yes')? 1:0;
						var dpublic=(info['public']=='Yes')? 1:0;
						$('select[name="edit_depa_active"]:first option[value='+active+']').attr('selected','selected');
						$('select[name="edit_depa_public"]:first option[value='+dpublic+']').attr('selected','selected');
					}
				});
			} );
			function contrch(link){
				   if(window.confirm("Do you want to delete this email?"))
					 window.location.href = link;
				}
		</script>
	<?php } ?>
	</body>
</html>
<?php function retrive_ip(){if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];}elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip=$_SERVER['REMOTE_ADDR'];}return $ip;} ?>