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

header("Cache-Control: no-cache, must-revalidate");  
require_once 'translator/class.translation.php';
if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);if(!is_file('translator/lang/'.$lang.'.csv'))$lang='en';}else $lang='en';$translate = new Translator($lang);
$siteurl=explode('?',dirname(curPageURL()));
$siteurl=$siteurl[0];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<!--[if lt IE 9]><script src="js/html5shiv-printshiv.js"></script><![endif]-->
	
	<link rel="stylesheet" type="text/css" href="<?php echo $siteurl.'/min/?b=css&amp;f=news.css,bootstrap.css,bootstrap-responsive.css&amp;5259487' ?>"/>
	<script type="text/javascript"  src="<?php echo $siteurl.'/min/?b=js&amp;f=jquery-1.10.2.js,bootstrap.min.js&amp;5259487' ?>"></script>

	<?php
		$filenews= 'config/news.txt';
		if(isset($news)) unset($news);

		$news = file($filenews, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$news=array_values(array_filter($news, "trim"));
		$news=array_reverse($news);
		$btn=$_GET['btn'];
		if(isset($_GET['id']) && !is_numeric($_GET['id']))
			exit();
		else if(isset($_GET['id']) && is_numeric($_GET['id'])){
			$id=$_GET['id'];
	?>

	<title><?php echo htmlspecialchars($news[$id+2],ENT_QUOTES,'UTF-8'); ?></title>
</head>
<body>
	<div class='container'>
		<div id='main' class='ext2'>
			<div class='row-fluid'>
				<div id='span12 corp' class='newscont'>
					<p class='titlenews'><?php echo htmlspecialchars($news[$id+2],ENT_QUOTES,'UTF-8'); ?></p>
					<p class='datenews'><?php $translate->__('Posted on:',false); echo htmlspecialchars($news[$id+1],ENT_QUOTES,'UTF-8'); ?></p>
					<div class='corpnews'><?php echo$news[$id]; ?></div>
					<?php if($btn!=0){ ?><a href='index.php' class='moren btn btn-warning'><?php $translate->__('Back to Homepage',false); ?></a><?php } ?>
				</div>
			</div>
		</div>
	</div>
</body>

<?php } else if(!isset($_GET['id'])){?>
	<title><?php $translate->__('News',false); ?></title>
</head>
<body>
	<div class='container'>
		<div id='main' class='ext2'>
			<div class='row-fluid'>
				<div id='corp' class='span12 newscont'>
				
				<?php if($btn!=0){ ?><a href='index.php' class='moren btn btn-warning'><?php $translate->__('Back to Homepage',false); ?></a><?php } ?>
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
	var news=new Array();
	news=<?php echo json_encode($news); ?>;
	var i=0;
	var arrlen=<?php echo count($news); ?>;
	 $(document).ready(function() {
		var stmp=new Array();
		for(i=0;i<12;i+=3){
			if(i==0)stmp.push("<h2 class='titlenews'>"+encodeHTML(news[i+2])+"</h2><p class='datenews'><?php echo $translate->__('Posted on:',true); ?> "+news[i+1]+"</p><div class='corpnews'>"+news[i]+"</div>");
			else stmp.push("<p class='titlenews'>"+encodeHTML(news[i+2])+"</p><p class='datenews'><?php echo $translate->__('Posted on:',true); ?> "+news[i+1]+"</p><div class='corpnews'>"+news[i]+"</div>");
			if(i==9 && arrlen>12)stmp.push("<button id='moren' class='moren btn btn-info' onclick='addnews("+i+");' style='position:relative;display:block;margin:0 auto'><?php echo $translate->__('Load More News',true); ?></button>");
		}
		$('#corp').append(stmp.join(''));
	});
	
	function addnews(){
		add= new Array();
		$('#moren').remove();
		add.push("<p class='titlenews'>"+news[i+2]+"</p><p class='datenews'><?php echo $translate->__('Posted on:',true); ?> "+news[i+1]+"</p><div class='corpnews'>"+news[i]+"</div>");
		if(i+3<arrlen-1)add.push("<button id='moren' class='moren btn btn-info' onclick='addnews("+i+");' style='position:relative;display:block;margin:0 auto'><?php echo $translate->__('Load More News',true); ?></button>");
		i+=3;
		$('#corp').append(add.join(''));
	}
	
	function encodeHTML(s) {
		return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#x27;').replace(/\//g, '&#x2F;');
	}
	</script>
</body>

<?php } else exit(); ?>
</html>
<?php function curPageURL() {$pageURL = 'http';if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";$pageURL .= "://";if (isset($_SERVER["HTTPS"]) && $_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];return $pageURL;}?>