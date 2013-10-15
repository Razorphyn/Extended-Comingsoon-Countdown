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

 if(!is_dir('config')) {header('Location: admin/datacheck.php');exit();}
 header("Cache-Control: no-cache, must-revalidate");  ?>
<?php 
	require_once 'translator/class.translation.php';
	if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);
	
	if(is_file('../config/monintoring.php')){
		include_once('../config/monintoring.php');
		if(file_put_contents('../config/monintoring.txt',$monitoringcode))
			unlink('../config/monintoring.php');
	}

	$file='config/config.txt';
	$filelogo= 'config/logo.txt';
	$filefrontmess= 'config/frontmess.txt';
	$socialfile='config/social.txt';
	$filenews= 'config/news.txt';
	$frontotinfo= 'config/indexfooter.txt';
	$filemail='config/mail.txt';
	
	if(isset($var)) unset($var);
	if(isset($logo)) unset($logo);
	if(isset($social)) unset($social);
	if(isset($phrase)) unset($phrase);
	if(isset($news)) unset($news);
	
	if(!is_file($file))file_put_contents($file,'');
	
	$var=file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if(!isset($var[1]) || $var[1]=='' || $var[1]==null){echo "<script type='text/javascript'>location.href = 'admin/index.php';</script>";exit();}
	if(is_file($filemonitor)){$monitoringcode=file($filemonitor, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);$monitoringcode=implode("\n",$monitoringcode);}
	if(is_file($filelogo)) $logo=file_get_contents($filelogo);
	if(is_file($filefrontmess)) $phrase=file_get_contents($filefrontmess);
	if(is_file($frontotinfo)) $frontph=file($frontotinfo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if(is_file($socialfile)) $social=file($socialfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if(is_file($filenews)) $news=file($filenews, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	date_default_timezone_set($var[10]);
	
	list($anno, $mese, $giorno)=explode('-',$var[2]);
	$info2=$mese.'/'.$giorno.'/'.$anno;

	list($data, $ora)=explode(' ',date("Y-m-d H:i:s"));
	list($oraf,$minuf,$secf)=explode(':',$var[3]);
	list($orac,$minuc,$secc)=explode(':',$ora);
	list($orai,$minui,$seci)=explode(':',$var[1]);
	
	$fsec=dateDifference($var[0],$var[2])*86400+($oraf-$orai)*3600+($minuf-$minui)*60+($secf-$seci);
	$csec=dateDifference($var[0],$data)*86400+($orac-$orai)*3600+($minuc-$minui)*60+($secc-$seci);

	$valore=round(100*$csec/$fsec,2);
	$interval=$fsec*0.1;
	
	$siteurl=explode('?',curPageURL());
	$siteurl=$siteurl[0];
	?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta http-equiv="Pragma" content="no-cache"/>
	<meta http-equiv="expires" content="0"/>
	<meta name="viewport" content="width=device-width,initial-scale=1.0" />
	<?php 
		if(isset($frontph[2]) && $frontph[2]!='**@****nullo**@****')echo '<meta name="description" content="'.$frontph[2].'">'; 
		if(isset($frontph[3]) && $frontph[3]!='**@****nullo**@****')echo '<meta name="Title" content="'.$frontph[3].'">'; 
	?>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

	<!--[if lt IE 9]><script src="js/html5shiv-printshiv.js"></script><![endif]-->
	
	<link rel="stylesheet" type="text/css" href="<?php echo $siteurl.'min/?b=css&amp;f=style.css,bootstrap.css,bootstrap-responsive.css,jquery-ui.css,magnific-popup.css&amp;5259487' ?>"/>
	
	<script type="text/javascript"  src="<?php echo $siteurl.'min/?b=js&amp;f=jquery-1.10.2.js,countdown.js,jquery-ui-1.10.3.custom.min.js,bootstrap.min.js,jquery.fittext.js&amp;5259487' ?>"></script>
	
	<title><?php if(isset($var[5]))echo $var[5];?></title>
	<?php if(isset($monitoringcode)) echo '<script>'.stripslashes($monitoringcode).'</script>'; ?>
</head>
<body>
<div class='wrapper'>
	<div class="container">
		<div class="masthead">
			<img id='logo' src='css/images/<?php if(isset($logo) && rtrim($logo)!='') echo $logo;else echo "logo.png"; ?>' alt='<?php if(isset($var[5]))echo $var[5];?>' title='<?php if(isset($var[5]))echo $var[5];?>' />
			<div class='socialcont'>
				<?php
					if(isset($social[0]) && $social[0]!=''){
						if($social[0]!='**@****nullo**@****'){
							?><a href='<?php echo $social[0]?>'><img src='css/icon/blogger.png' alt='<?php $translate->__('Follow me on Blogger',false); ?>' title='<?php $translate->__('Follow me on Blogger',false); ?>' /></a><?php
						} if($social[1]!='**@****nullo**@****'){
							?><a href='<?php echo $social[1]?>'><img src='css/icon/deviantart.png' alt='<?php $translate->__('Follow me on DeviantArt',false); ?>' title='<?php $translate->__('Follow me on DeviantArt',false); ?>'/></a><?php
						} if($social[2]!='**@****nullo**@****'){
							?><a href='<?php echo $social[2]?>'><img src='css/icon/facebook.png' alt='<?php $translate->__('Follow me on Facebook',false); ?>' title='<?php $translate->__('Follow me on Facebook',false); ?>'/></a><?php
						} if($social[3]!='**@****nullo**@****'){
							?><a href='<?php echo $social[3]?>'><img src='css/icon/flickr.png' alt='<?php $translate->__('Follow me on Flickr',false); ?>' title='<?php $translate->__('Follow me on Flickr',false); ?>'/></a><?php
						} if($social[4]!='**@****nullo**@****'){
							?><a href='<?php echo $social[4]?>'><img src='css/icon/linkedin.png' alt='<?php $translate->__('Follow me on Picasa',false); ?>' title='<?php $translate->__('Follow me on Picasa',false); ?>'/></a><?php
						} if($social[5]!='**@****nullo**@****'){
							?><a href='<?php echo $social[5]?>'><img src='css/icon/twitter.png' alt='<?php $translate->__('Follow me on Twitter',false); ?>' title='<?php $translate->__('Follow me on Twitter',false); ?>'/></a><?php 
						} if($social[6]!='**@****nullo**@****'){
							?><a href='<?php echo $social[6] ?>'><img src='css/icon/wordpress.png' alt='<?php $translate->__('Follow me on Wordpress',false); ?>' title='<?php $translate->__('Follow me on Wordpress',false); ?>'/></a><?php 
						} if($social[7]!='**@****nullo**@****'){
							?><a href='<?php echo $social[7] ?>'><img src='css/icon/youtube.png' alt='<?php $translate->__('Follow me on Youtube',false); ?>' title='<?php $translate->__('Follow me on Youtube',false); ?>'/></a><?php
						}
					} ?>
			</div>
		</div>
		<div class='divider'></div>
		<div class="jumbotron">
			<?php if(isset($phrase) && $phrase!='**@****nullo**@****')echo '<div id="title" class="lead title">'.$phrase.'</div>';?>
			<?php if(isset($var[16]) && $var[16]=='yes') { ?>
				<div class="timer-area">
					<ul id="countdown">
						<li><span class="days">00</span><p class="timeRefDays"><?php $translate->__('Days',false); ?></p></li>
						<li><span class="hours">00</span><p class="timeRefHours"><?php $translate->__('Hours',false); ?></p></li>
						<li><span class="minutes">00</span><p class="timeRefMinutes"><?php $translate->__('Minutes',false); ?></p></li>
						<li><span class="seconds">00</span><p class="timeRefSeconds"><?php $translate->__('Seconds',false); ?></p></li>
					</ul>
				</div>
			<?php } if(isset($var[17]) && $var[17]=='yes') { if(isset($frontph[1]) && $frontph[1]!='**@****nullo**@****') echo"<div class='progph'>".stripslashes($frontph[1]).'</div>'; ?>
					<div id='cornice' >
					<img  class='progbk' src='css/images/progress-container.png'/>
					<div id="progressbar"></div></div>
					
			<?php } ?>
			<div class='divider'></div>
		</div>
		
		
		<div class="footer">
			<?php if(count($news)>2){ ?>
				<div class='sectioncol'><?php $translate->__('Last News',false); ?></div>
					<div class='row-fluid collapsable newscol'>
						<?php
						$news=array_reverse($news);
						$max=(count($news)>12)? 12:count($news);
						$lnew=array();
						for($i=0;$i<$max;$i+=3){
							$lnew[]= "<div class='span3'><h3><p class='ptitle'>".htmlspecialchars($news[$i+2],ENT_QUOTES,'UTF-8')."</p></h3>";
							$lnew[]="<span class='datapost'>".htmlspecialchars($news[$i+1],ENT_QUOTES,'UTF-8')."</span>";
							$ns=accorcia($news[$i],$i,$translate->__('Read More',true),$siteurl);
							$lnew[]="<div class='pmessage'>".$ns."</div></div>";
						}
						echo implode('',$lnew);
						if(count($news)>11) { ?>
							<span class='more'><a id='readmore' title='<?php $translate->__("Read More News",false) ?>' alt='<?php $translate->__("Read More News",false); ?>' href='<?php echo $siteurl; ?>/news.php?btn=0' class='visible-desktop snews'><?php $translate->__('Read More News',false); ?></a><a class='hidden-desktop' id='readmore' title='Read More News' alt='<?php $translate->__('Read More News',false); ?>' href='<?php echo $siteurl; ?>/news.php?btn=1'><?php $translate->__('Read More News',false); ?></a></span>
						<?php } ?>
					</div>
			<?php } ?>
			<?php if(isset($var[9]) && $var[9]=='yes') { ?>
			<div class='sectioncol'><?php $translate->__('Newsletter',false); ?></div>
				<div class='collapsable'>
					<div class='sub'><?php $translate->__('Do you want to know when the site will be ready? Subscribe!',false); ?></div>
						<form class="form-horizontal" id="mailform" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
							<div class="row">
								<div class="span2"><label for='nameinput'><?php $translate->__('Name',false); ?></label></div>
								<div class="span3"><input type="text" id="nameinput" name="nameinput" placeholder="<?php $translate->__('Your Name',false); ?>" required></div>
							</div><br/>
							<div class="row">
								<div class="span2"><label for='lnameinput'><?php $translate->__('Lastname',false); ?></label></div>
								<div class="span3"><input type="text" id="lnameinput" name="lnameinput" placeholder="<?php $translate->__('Your Lastname',false); ?>"></div>
							</div><br/>
							<div class="row">
								<div class="span2"><label for='mailinput'><?php $translate->__('Email',false); ?></label></div>
								<div class="span3"><input type="text" id="mailinput" name="mailinput" placeholder="<?php $translate->__('Email',false); ?>" required></div>
							</div>
							<div class="row">
								<div class="span2 offset2"><input onclick='javascript:return false;' type="submit" id="mailsubmit" name="mailsubmit" class="btn btn-success subform" value='<?php $translate->__('Subscribe',false); ?>' /></div>
							</div>
						</form>
				</div>
			<?php }if(isset($var[8]) && $var[8]=='yes' && $var[7]!='**@****nullo**@****') { ?>
			<div class='sectioncol'><?php $translate->__('Contact',false); ?></div>
				<div class='collapsable'>
					<form class="form-horizontal" id="contactform" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" value='submessage'>
						<div class="row-fluid">
							<div class="span1"><label for='senname'><?php $translate->__('Name',false); ?></label></div>
							<div class="span4"><input type="text" id="senname" name="senname" placeholder="<?php $translate->__('Your/Company Name',false); ?>" required></div>
							<div class="span1"><label for='senphone'><?php $translate->__('Telephone',false); ?></label></div>
							<div class="span4"><input type="tel" id="senphone" name="senphone" placeholder="<?php $translate->__('Telephone Number',false); ?>"></div>
						</div><br/>
						<div class="row-fluid">
							<div class="span1"><label for='senmail'><?php $translate->__('Email',false); ?></label></div>
							<div class="span4"><input type="email" id="senmail" name="senmail" placeholder="<?php $translate->__('Email',false); ?>" required></div>
							<div class="span1"><label for='subject'><?php $translate->__('Subject',false); ?></label></div>
							<div class="span4"><input type="text" id="subject" name="subject" placeholder="<?php $translate->__('Subject',false); ?>" required></div>
						</div><br/>
						<div class="row-fluid">
							<div class="span1"><label for='message'><?php $translate->__('Message',false); ?> </label></div>
							<div class="span4"><textarea cols='90' rows='5' id='message' name='message' required></textarea></div>
						</div>
						<div class="row-fluid">
							<div class="span2 offset5"><input onclick='javascript:return false;' type="submit" id="sendmail" name="sendmail" class="btn btn-success subform" value='<?php $translate->__('Send Mail',false); ?>' /></div>
						</div>
					</form>
				</div>
			<?php } ?>
			<div class='sectioncol'><?php $translate->__('Tell a Friend',false); ?></div>
			<div class='collapsable'>
				<p><?php $translate->__('Tell your friends how awesome this is!',false); ?></p>
				<a href="mailto:friend's mail?subject=<?php $translate->__('Check this out!&amp;body=I thought you might be interested in seeing this new site:',false); echo curPageURL(); ?> ."><font color="#33FF00"><?php $translate->__('Click here to send them an email',false); ?></font></a>
			</div>
		<?php if(isset($frontph[0]) && $frontph[0]!='**@****nullo**@****'){ echo '<div class="footerphrase">'.stripslashes($frontph[0]).'</div>'; } ?>
		</div>
	</div>
</div>
	
	<script type="text/javascript"  src="<?php echo $siteurl.'min/?b=js&amp;f=jquery.validate.min.js,jquery.magnific-popup.min.js,noty/jquery.noty.js,noty/layouts/top.js,noty/themes/default.js&amp;5259487' ?>"></script>
	<script type='text/javascript'>
	  $(document).ready(function() {
			<?php if(isset($var[18]) && $var[18]=='yes'){ ?> $("#title").fitText(); <?php } ?>
			<?php if(isset($var[17]) && $var[17]=='yes'){ ?>
				var perc=<?php if( isset($var[6]) && $var[6]!='**@****nullo**@****')echo $var[6];else echo $valore=($valore>100)?100:$valore; ?>,
					up= (parseInt(screen.height)*3.7037/100).toFixed(0)+'';
				
				$("#progressbar").progressbar({ value: perc,max:100 });
				$("#progressbar").attr('title','<?php echo $translate->__('Complete',true); ?>: '+perc+'%');
				
				$("#progressbar").tooltip({ position: { my: "top center", at: "top top-"+up, collision: "flipfit" } });
				$('.container').resize(function (){var presize=($('.container').width()*48.4375/100).toFixed(0);
				$("#progressbar").children('.ui-progressbar').css('max-width',presize);});

			<?php } 
				if(isset($var[16]) && $var[16]=='yes'){
					if(isset($var[23]) && $var[23]=='yes'){
			?>
						$("#countdown").countdown({date:'<?php if(isset($info2) && isset($var[3])) echo $info2.' '.$var[3]; ?>',format:'on'},function(){<?php if(isset($var[4]) && $var[4]!='**@****nullo**@****')echo 'window.location = "'.$var[4].'";'; ?>},"<?php echo $translate->__('Day',true); ?>","<?php echo $translate->__('Days',true); ?>","<?php echo $translate->__('Hour',true); ?>","<?php echo $translate->__('Hours',true); ?>","<?php echo $translate->__('Minute',true); ?>","<?php echo $translate->__('Minutes',true); ?>","<?php echo $translate->__('Second',true); ?>","<?php echo $translate->__('Seconds',true); ?>");
			<?php 
					}
					else{
			?>
						$("#countdown").countdown({date:'<?php if(isset($info2) && isset($var[3])) echo $info2.' '.$var[3]; ?>',format:'on'},null,"<?php echo $translate->__('Day',true); ?>","<?php echo $translate->__('Days',true); ?>","<?php echo $translate->__('Hour',true); ?>","<?php echo $translate->__('Hours',true); ?>","<?php echo $translate->__('Minute',true); ?>","<?php echo $translate->__('Minutes',true); ?>","<?php echo $translate->__('Second',true); ?>","<?php echo $translate->__('Seconds',true); ?>");
			<?php
					}
				} 
			?>

			$(".sectioncol").click(function(){$(this).next(".collapsable").slideToggle(800,function(){$("html,body").animate({scrollTop:$(this).offset().top},1E3)})});
			
			$('.snews').magnificPopup({
				type: 'iframe',
				iframe: {
					patterns: {
						news: {
							index: '<?php echo $siteurl; ?>',
							id: '?',
							src: '<?php echo $siteurl.'news.php?%id%'; ?>'
						}
					}
				}
			});
			
			$("#sendmail").click(function (){
				var a = $("#senname").val(),
					e = $("#senphone").val(),
					b = $("#senmail").val(),
					c = $("#subject").val(),
					d = $("#message").val();

				"" != a.replace(/\s+/g, "") && "" != b.replace(/\s+/g, "") && "" != c.replace(/\s+/g, "") && "" != d.replace(/\s+/g, "") && e.match(/[0-9+]/g)? $.ajax({
					type: "POST",
					url: "admin/function.php",
					data: {act: "send_mail",senname: a,senphone: e,senmail: b,subject: c,message: d},
					dataType: "json",
					success: function (a) {
						"Sent" == a[0] ? noty({text: "<?php echo $translate->__('Your email has been sent!',true); ?>",type: "success",timeout: 9E3}) : noty({text: a[0],type: "error",timeout: 9E3})
					}
				}).fail(function (a, b) {noty({text: b,type: "error",timeout: 9E3})}) : noty({text: "<?php echo $translate->__('Complete all the fields',true); ?>",type: "error",timeout: 9E3});
				return !1
			});
			
			$("#mailsubmit").click(function () {
				var c = $("#nameinput").val(),
					d = $("#lnameinput").val(),
					a = $("#mailinput").val();
				"" != a.replace(/\s+/g, "") ? $.ajax({
					type: "POST",
					url: "admin/function.php",
					data: {act: "subscribe",nameinput: c,lnameinput: d,mailinput: a},
					dataType: "json",
					success: function (b) {
						if("Added" == b[0]) 
							noty({text: "<?php echo $translate->__('Thank you for subscribing!',true); ?>",type: "success",timeout: 9E3}) 
						else if("Already" == b[0])
							noty({text: "<?php echo $translate->__('You are already subscribed to our system',true); ?>",type: "information",timeout: 9E3})
						else if("Empty" == b[0])
							noty({text: "<?php echo $translate->__('Please Complete all the fields',true); ?>",type: "error",timeout: 9E3})
						else
							 noty({text: b[0],type: "error",timeout: 9E3})
					}
				}).fail(function (b, a) {noty({text: a,type: "error",timeout: 9E3})}) : noty({text: "<?php echo $translate->__('Please Complete all the fields',true); ?>",type: "error",timeout: 9E3});
				return !1
			});
	});
	</script>

<?php

	function dateDifference($startDate, $endDate){list($anno,$mese,$giorno)=explode('-',$startDate);list($fanno,$fmese,$fgiorno)=explode('-',$endDate);$days=gregoriantojd($fmese, $fgiorno, $fanno) -gregoriantojd($mese, $giorno, $anno);return $days;} 
	function curPageURL() {$pageURL = 'http';if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";$pageURL .= "://";if (isset($_SERVER["HTTPS"]) && $_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];return $pageURL;}
	function accorcia($frase,$i,$str,$siteurl){
		$frase=strip_tags($frase);
		$len=strlen($frase);
		if($len>120)
			$frase=substr($frase,0,100).'...<br/><a title="'.$str.'" alt="'.$str.'" href="'.$siteurl.'/news.php?id='.$i.'&btn=0" class="visible-desktop snews">'.$str.'</a><a class="hidden-desktop" title="'.$str.'" alt="'.$str.'" href="'.$siteurl.'/news.php?id='.$i.'&btn=1">'.$str.'</a>';
		return $frase;
	}
?>
</body>
</html>