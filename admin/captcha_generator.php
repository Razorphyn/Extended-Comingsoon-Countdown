<?php 
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
if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
		ini_set('session.cookie_secure', '1');
if(isset($_COOKIE['RazorphynExtendedComingsoon']) && !is_string($_COOKIE['RazorphynExtendedComingsoon']) || !preg_match('/^[a-z0-9]{26,40}$/',$_COOKIE['RazorphynExtendedComingsoon']))
	setcookie(session_name(),'invalid',time()-3600);
session_start(); 

$image_width = 120;
$image_height = 50;
$characters_on_image = 4;
$font = 'font/monofont.ttf';


$possible_letters = 'ABCDEFGHKYILMNPQRSTUXYZ';
$random_dots = 10;
$random_lines = 30;
$captcha_background_color=array('r'=>0,'g'=>0,'b'=>0);
$captcha_text_color=array('r'=>255,'g'=>255,'b'=>255);
$captcha_noice_color =array('r'=>255,'g'=>255,'b'=>255);

$code = '';

$i = 0;
while ($i < $characters_on_image) { 
	$code .= substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
	$i++;
}

$font_size = $image_height * 0.75;
$image = @imagecreate($image_width, $image_height);


/* setting the background, text and noise colours here */
$background_color = imagecolorallocate($image, $captcha_background_color['r'], $captcha_background_color['g'], $captcha_background_color['b']);

$text_color = imagecolorallocate($image, $captcha_text_color['r'], $captcha_text_color['g'], $captcha_text_color['b']);

$image_noise_color = imagecolorallocate($image, $captcha_noice_color['r'], $captcha_noice_color['g'], $captcha_noice_color['b']);

/* generating the dots randomly in background */
for( $i=0; $i<$random_dots; $i++ )
	imagefilledellipse($image, mt_rand(0,$image_width),mt_rand(0,$image_height), 2, 3, $image_noise_color);

/* generating lines randomly in background of image */
for( $i=0; $i<$random_lines; $i++ )
	imageline($image, mt_rand(0,$image_width), mt_rand(0,$image_height),mt_rand(0,$image_width), mt_rand(0,$image_height), $image_noise_color);

/* create a text box and add 6 letters code in it */

$textbox = imagettfbbox($font_size, 0, $font, $code); 
$x = ($image_width - $textbox[4])/2;
$y = ($image_height - $textbox[5])/2;
imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code);

/* Show captcha image in the page html page */
header('Content-Type: image/jpeg');// defining the image type to be shown in browser widow
imagejpeg($image);//showing the image
imagedestroy($image);//destroying the image instance
$_SESSION['captcha_code'] = $code;

function hexrgb ($hexstr){
  $int = hexdec($hexstr);
  return array("red" => 0xFF & ($int >> 0x10),"green" => 0xFF & ($int >> 0x8),"blue" => 0xFF & $int);
}
?>