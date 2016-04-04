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

if ($thumbnailer->hasValidCachedVersion())
{
  header("Location: " . $thumbnailer->getOutputFilename());
  die;
}

if (!$thumbnailer->isValidYouTubeVideo())
{
  header("Status: 404 Not Found");
  die("No YouTube video found or YouTube timed out. Try again soon.");
}

if(!$thumbnailer->getID())
{
  header("Status: 404 Not Found");
  die("YouTube ID not found");
}

$thumbnailer->createThumbnailImage();

header("Location: " . $thumbnailer->getOutputFilename());
die;

?>
