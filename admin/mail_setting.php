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
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('../translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);

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
	$filefnmail='../config/fnmail.txt';
	$filefnmessage= '../config/fnmessage.txt';
	$filefnfooter= '../config/footermail.txt';
	
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
	
	$fnmail= file($filefnmail, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$messagefn= file_get_contents($filefnmessage);
	$footermail= file_get_contents($filefnfooter);
	
	if(is_file('../config/stmp.php')) include_once '../config/stmp.php';
	if(isset($smailpassword)){
		include_once ('endecrypt.php');
		$smailpassword=base64_decode($smailpassword);
		$e = new Encryption(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$pass = $e->decrypt($smailpassword, $skey);
	}
	if(isset($_POST['fcheck'])){
		header('Location: datacheck.php');
	}
	
}
	if (isset($var[10]))date_default_timezone_set($var[10]);
?>
<!DOCTYPE html>
<html  lang="<?php echo $lang; ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="viewport" content="width=device-width">
		<title><?php $translate->__("Setup",false); ?></title>
		
		
		<!--[if lt IE 9]><script src="../js/html5shiv-printshiv.js"></script><![endif]-->
		<link rel="stylesheet" href="../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
		<link rel="stylesheet" href="adminstyle.css" type="text/css"/>
		
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		
		<script type="text/javascript"  src="../js/jquery.js"></script>
		<script type="text/javascript"  src="../js/bootstrap.min.js"></script>
		<script  type="text/javascript" src="../ckeditor/ckeditor.js"></script>
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
								<li class="dropdown active" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Setup",false); ?> <b class="caret"></b></a>
									<ul class="dropdown-menu" role="menu">
										<li role="presentation"><a href="index.php" tabindex="-1" role="menuitem"><?php $translate->__("Site",false); ?></a></li>
										<li role="presentation"  class='active'><a href="mail_setting.php" tabindex="-1" role="menuitem"><?php $translate->__("Mail",false); ?></a></li>
									</ul>
								</li>
								<li class="dropdown" role='button'>
									<a id="drop1" class="dropdown-toggle" role='button' data-toggle="dropdown" href="#"><?php $translate->__("Mail",false); ?><b class="caret"></b></a>
									<ul class="dropdown-menu" aria-labelledby="drop1" role="menu">
										<li role="presentation"><a href="mail.php" tabindex="-1" role="menuitem"><?php $translate->__("Send Mail",false); ?></a></li>
										<li role="presentation"><a href="managesched.php" tabindex="-1" role="menuitem"><?php $translate->__("Manage Scheduled Mail",false); ?></a></li>
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
		
			<div class='formcor' style='text-align:center' ><button onclick='javascript:location.href="../index.php"' value='<?php $translate->__("See Frontend",false); ?>' class='btn btn-info'><?php $translate->__("See Frontend",false); ?></button></div>
			<form name="ckform" id="ckform"  method="post"  class='formcor form-inline'>
				<h2 class='titlesec'><?php $translate->__("Database Files Checking",false); ?></h2>
				<input type="submit" name="fcheck" id="fcheck" value="<?php $translate->__("Check Database Files",false); ?>" class="btn"/>
			</form>
					
			<form name="completesitemail" id="completesitemail"  method="post"  class='formcor'>
				<h2 class='titlesec'><?php $translate->__("SMTP Setting",false); ?></h2>
				<div class='stmpinfo' >
					<div class='form-group'>
						<div class='row'>
							<div class='col-xs-12 col-sm-6 col-md-2'><label>STMP Service</label></div>
							<div class='col-xs-12 col-sm-6 col-md-4'><select  class='form-control' id='stmpserv' ><option value='0'>This Server</option><option value='1'>External Service</option></select></div>
						</div>
					</div>
					<div class='form-group'>
						<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmpname'>Name</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input id='stmpname' type='text' class='form-control'  value='<?php if(isset($smailname)) echo $smailname;?>' required/></div>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmpmail'>Mail Address</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input class='form-control' id='stmpmail' type='email' value='<?php if(isset($settingmail)) echo $settingmail; ?>' required /></div>
						</div>
					</div>
					<div class='form-group'>
						<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmphost'>Hostname</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input id='stmphost' type='text' class='form-control'  value='<?php if(isset($smailhost)) echo $smailhost; ?>' required  /></div>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmpport'>Port</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input id='stmpport' type='text' class='form-control'  value='<?php if(isset($smailport)) echo $smailport; ?>' required /></div>
						</div>
					</div>
					<div class='form-group'>
						<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmpsec'>SSL/TLS</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><select class='form-control' id='stmpsec' ><option value='0'>No</option><option value='1'>SSL</option><option value='2'>TLS</option></select></div>
						</div>
					</div>
					<div class='form-group'>
						<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmpaut'>Authentication</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><select class='form-control' id='stmpaut' ><option value='0'>No</option><option value='1'>Yes</option></select></div>
						</div>
					</div>
					<div class='form-group'>
						<div class='row'>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmpusr'>Username</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input id='stmpusr' type='text' class='form-control'  value='<?php if(isset($smailuser)) echo $smailuser; ?>' /></div>
								<div class='col-xs-12 col-sm-6 col-md-2'><label for='stmppas'>Password</label></div>
								<div class='col-xs-12 col-sm-6 col-md-4'><input id='stmppas' class='form-control' type='password' value='<?php if(isset($smailpassword)) echo $smailpassword; ?>' /></div>
						</div>
					</div>
					<br/>
					<input type='submit' id='savestmp' onclick='javascript:return false;' value='Save' class='btn btn-success'/>
				</div>
			</form>
			
			<form name="defmailinfo" id="defmailinfo"  method="post"  class='formcor'>
				<h2 class='titlesec'><?php $translate->__("Common Email Section",false); ?></h2>

				<div class='form-group'>
					<div class='row'>
						<div class='col-xs-12'><label><?php $translate->__("Footer:",false); ?></label></div>
						<div class='col-xs-12'><textarea id='footerfn' name='footerfn' class='footerfn' rows='10' cols='100'><?php if(isset($footermail) && $footermail!='**@****nullo**@****')echo $footermail; ?></textarea></div>
					</div>
				</div>
				<input onclick='javascript:return false;' type="submit" name="senddefmail" id="senddefmail" value="<?php $translate->__("Update",false); ?>" class="btn btn-success"/>
			</form>
					
			<form name="completesitemail" id="completesitemail"  method="post"  class='formcor'>
				<h2 class='titlesec'><?php $translate->__("Completed Site Mail",false); ?></h2>
				<div class='form-group'>
					<div class='row'>
						<div class='col-xs-12 col-sm-6 col-md-3'><label><?php $translate->__("Do you want to alert your users once the site is finished?",false); ?></label></div>
						<div class='col-xs-6 col-sm-3'><input type="radio" name="warnus" value="yes" <?php if(isset($fnmail[2]) && $fnmail[2]=='yes') echo "checked"; else if(!isset($fnmail[2])) echo "checked";?>/><?php $translate->__("Yes",false); ?> </div>
						<div class='col-xs-6 col-sm-3'><input type="radio" name="warnus" value="no" <?php if(isset($fnmail[2]) && $fnmail[2]=='no') echo "checked"; ?>/><?php $translate->__("No",false); ?></div>
					</div>
				</div>
				<div class='form-group'>
					<div class='row'>
						<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Sender:",false); ?></label></div>
						<div class='col-xs-12 col-sm-6 col-md-3'><input	type="text" class='form-control' id="senderfn" 	name="senderfn" <?php if(isset($fnmail[0]) && $fnmail[0]!='**@****nullo**@****') echo 'value="'.$fnmail[0].'"'; ?>/></div>
						<div class='col-xs-12 col-sm-6 col-md-2'><label><?php $translate->__("Object:",false); ?></label></div>
						<div class='col-xs-12 col-sm-6 col-md-3'><input	type="text" class='form-control' id="objectfn" 	name="objectfn" <?php if(isset($fnmail[1]) && $fnmail[1]!='**@****nullo**@****') echo 'value="'.$fnmail[1].'"'; ?>/></div>
					</div>
				</div>
				<div class='form-group'>
					<div class='row'>
						<div class='col-xs-12'><label><?php $translate->__("Message:",false); ?></label></div>
						<div class='col-xs-12'><textarea id='messagefn' name='messagefn' rows='10' cols='100'><?php if(isset($messagefn) && $messagefn!='**@****nullo**@****') echo $messagefn; ?></textarea></div>
					</div>
				</div>
				<input onclick='javascript:return !1;' type="submit" name="fnmailbut" id="fnmailbut" value="<?php $translate->__("Update Final Message",false); ?>" class="btn btn-success"/>
			</form>
					
			<form name="logoutfor" id="logoutfor" method="post"  class='formcor'>
				<input type="submit" name="logout" id="logout" value="Logout" class="btn btn-danger"/>
			</form>
		</div>
		<?php } else { ?>
		<div class='container main'>
			<form name="formdata" id="formdata" method="post"  class='formcor form-inline'>
				<h2 class='titlesec'>Login</h2>
					<!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" /><![endif]-->
					<?php if(isset($acc) && $acc==false){ ?>
					<div class='row'><div class='col-xs-12'><p><?php $translate->__("Wrong Password",false); ?><p></div></div>
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
		<script type="text/javascript"  src="../js/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript"  src="../js/jquery.validate.min.js"></script>
		<script type="text/javascript"  src="../js/timezoneautocomplete.js"></script>
		<script type="text/javascript"  src="../js/noty/jquery.noty.js"></script>
		<script type="text/javascript"  src="../js/noty/layouts/top.js"></script>
		<script type="text/javascript"  src="../js/noty/themes/default.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
		
			CKEDITOR.replace('footerfn');
			CKEDITOR.replace('messagefn');
			
			<?php if(isset($stmpserv)){ ?>
				$('#stmpsec').val(<?php echo $stmpserv; ?>);
			<?php }if(isset($smailssl)){ ?>
				$('#stmpsec').val(<?php echo $smailssl; ?>);
			<?php } if(isset($smailauth)){ ?>
				$('#stmpaut').val(<?php echo $smailauth; ?>);
			<?php } ?>
		
			$("#savestmp").click(function () {
				var a = $("#stmpserv").val(),
					c = $("#stmpname").val(),
					d = $("#stmphost").val(),
					e = $("#stmpport").val(),
					f = $("#stmpsec > option:selected").val(),
					g = $("#stmpmail").val(),
					h = $("#stmpaut > option:selected").val(),
					k = $("#stmpusr").val(),
					l = $("#stmppas").val();
				$.ajax({
					type: "POST",
					url: "function.php",
					data: {act: "save_stmp",serv: a,name: c,host: d,port: e,ssl: f,mail: g,auth: h,usr: k,pass: l},
					dataType: "json",
					success: function (b) {
						"Saved" == b[0] ? noty({text: "STMP Information Saved",type: "success",timeout: 9E3}) : noty({text: b[0],type: "error",timeout: 9E3})
					}
				}).fail(function (b, a) {noty({text: a,type: "error",timeout: 9E3})})
			});
			$('#senddefmail').click(function(){
				var footerfn=CKEDITOR.instances.footerfn.getData().replace(/\s+/g,' ');
				if(footerfn.replace(/\s+/g,'')!=''){
					var request= $.ajax({
						type: 'POST',
						url: 'function.php',
						data: {act:'save_common_mail',footerfn:footerfn},
						dataType : 'json',
						success : function (data){
							if(data[0]=='Sent')
								var n = noty({text: "<?php echo $translate->__("The Footer has been saved",true); ?>",type:'success',timeout:9000});
							else if(data[0]=='Empty')
								var n = noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
							else
								var n = noty({text: "<?php echo $translate->__("A problem has occured,please try again",true); ?>",type:'error',timeout:9000});
						}
					});
					request.fail(function(jqXHR, textStatus){var n = noty({text: textStatus,type:'error',timeout:9000});});
				}
				else
					var n = noty({text: "<?php echo $translate->__("Empty Field",true); ?>",type:'error',timeout:9000});
				return false;
			});
			
			$('#fnmailbut').click(function(){
				var warnus=$('input[type=radio][name="warnus"]:checked').val();
				var senderfn=$('#senderfn').val();
				var objectfn=$('#objectfn').val();
				var messagefn=CKEDITOR.instances.messagefn.getData().replace(/\s+/g,' ');
				if(warnus.replace(/\s+/g,'')!='' && senderfn.replace(/\s+/g,'')!='' && objectfn.replace(/\s+/g,'')!='' && messagefn.replace(/\s+/g,'')!=''){
					var request= $.ajax({
						type: 'POST',
						url: 'function.php',
						data: {act:'complete_site_mail',warnus:warnus,senderfn:senderfn,objectfn:objectfn,messagefn:messagefn},
						dataType : 'json',
						success : function (data) {
							if(data[0]=='Saved')
								var n = noty({text: "<?php echo $translate->__("Final Mail Saved",true); ?>",type:'success',timeout:9000});
							else if(data[0]=='Error')
								var n = noty({text: "<?php echo $translate->__("A problem has occured,please try again",true); ?>",type:'error',timeout:9000});
							else if(data[0]=='Empty')
								var n = noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
						}
					});
					request.fail(function(jqXHR, textStatus){var n = noty({text: textStatus,type:'error',timeout:9000});});
				}
				else
					var n = noty({text: "<?php echo $translate->__("Please Complete all the fields",true); ?>",type:'error',timeout:9000});
				return false;
			});
			
			$("#sendmailform").validate(
			{
				rules:{sender: "required",message: "required",object:"required"},
				messages:{sender: "Complete field",message: "Complete field",object: "Complete field",footerfn: "Complete field"}
			});
			
			$("#completesitemail").validate(
			{
				rules:{senderfn: "required",message: "required",objectfn:"required",footerfn: "required"},
				messages:{senderfn: "Complete field",messagefn: "Complete field",objectfn: "Complete field",footerfn: "Complete field"}
			});
		});
		</script>
	<?php } ?>
	</body>
</html>
<?php function retrive_ip(){if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])){$ip=$_SERVER['HTTP_CLIENT_IP'];}elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];}else{$ip=$_SERVER['REMOTE_ADDR'];}return $ip;} ?>