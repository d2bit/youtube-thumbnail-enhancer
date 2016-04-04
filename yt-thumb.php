<?php
/*
 * YouTube Thumbnail Enchancer by Hal Gatewood
 * License: The just-use-it license. Have fun!
 *
 * Dependances:
 * curl
 * GD Library
 * coffee
 *
 * Parameters:
 * inpt = YouTube URL or YouTube ID
 * quality = hq or mq
 * refresh = skips the cache to grab a fresh one
 * play = show play button in middle
 *
 * Usage:
 * http://example.com/yt-thumb.php?quality=hq&inpt=http://www.youtube.com/watch?v=XZ4X1wcZ1GE
 * http://example.com/yt-thumb.php?quality=mq&inpt=http://www.youtube.com/watch?v=XZ4X1wcZ1GE
 * http://example.com/yt-thumb.php?quality=hq&inpt=XZ4X1wcZ1GE
 * http://example.com/yt-thumb.php?quality=mq&inpt=XZ4X1wcZ1GE
 * http://example.com/yt-thumb.php?quality=hq&inpt=XZ4X1wcZ1GE&play
 * http://example.com/yt-thumb.php?quality=hq&inpt=XZ4X1wcZ1GE&play&refresh
 *
 */

include './YoutubeThumbnailer.php';

$options = array(
  'input' => $_REQUEST['inpt'],
  'quality' => $_REQUEST['quality'],
  'havePlayBtn' => $_REQUEST['play'],
  'shouldRefresh' => $_GET['refresh']
);
$thumbnailer = new YoutubeThumbnailer($options);
$id = $thumbnailer->getID();

$filename = $thumbnailer->getFilename();

if($thumbnailer->hasValidCachedVersion())
{
  header("Location: " . $thumbnailer->getOutputFilename());
  die;
}


// CHECK IF YOUTUBE VIDEO
$handle = curl_init("https://www.youtube.com/watch/?v=" . $id);
curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($handle);


// CHECK FOR 404 OR NO RESPONSE
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
if($httpCode == 404 OR !$response) 
{
	header("Status: 404 Not Found");
	die("No YouTube video found or YouTube timed out. Try again soon."); 
}

curl_close($handle);


// IF NOT ID THROW AN ERROR
if(!$id) 
{
	header("Status: 404 Not Found");
	die("YouTube ID not found");
}


// CREATE IMAGE FROM YOUTUBE THUMB
$image = imagecreatefromjpeg( "http://img.youtube.com/vi/" . $id . "/" . $thumbnailer->quality . "default.jpg" );


// IF HIGH QUALITY WE CREATE A NEW CANVAS WITHOUT THE BLACK BARS
if($thumbnailer->quality == "hq")
{
	$cleft = 0;
	$ctop = 45;
	$canvas = imagecreatetruecolor(480, 270);
	imagecopy($canvas, $image, 0, 0, $cleft, $ctop, 480, 360);
	$image = $canvas;
}


$imageWidth 	= imagesx($image);
$imageHeight 	= imagesy($image);



// ADD THE PLAY ICON
$play_icon = $thumbnailer->play ? "play-" : "noplay-";
$play_icon .= $thumbnailer->quality . ".png";
$logoImage = imagecreatefrompng( $play_icon );

imagealphablending($logoImage, true);

$logoWidth 		= imagesx($logoImage);
$logoHeight 	= imagesy($logoImage);

// CENTER PLAY ICON
$left = round($imageWidth / 2) - round($logoWidth / 2);
$top = round($imageHeight / 2) - round($logoHeight / 2);


// CONVERT TO PNG SO WE CAN GET THAT PLAY BUTTON ON THERE
imagecopy( $image, $logoImage, $left, $top, 0, 0, $logoWidth, $logoHeight);
imagepng( $image, $filename .".png", 9);


// MASHUP FINAL IMAGE AS A JPEG
$input = imagecreatefrompng($filename .".png");
$output = imagecreatetruecolor($imageWidth, $imageHeight);
$white = imagecolorallocate($output,  255, 255, 255);
imagefilledrectangle($output, 0, 0, $imageWidth, $imageHeight, $white);
imagecopy($output, $input, 0, 0, 0, 0, $imageWidth, $imageHeight);

// OUTPUT TO 'i' FOLDER
imagejpeg($output, $thumbnailer->getOutputFilename(), 95);

// UNLINK PNG VERSION
@unlink($filename .".png");

// REDIRECT TO NEW IMAGE
header("Location: " . $thumbnailer->getOutputFilename());
die;

?>
