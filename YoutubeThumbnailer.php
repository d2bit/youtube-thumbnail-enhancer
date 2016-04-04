<?php
include "./FileSystem.php";

class YoutubeThumbnailer
{
  const HIGH_QUALITY = "hq";
  const MEDIUM_QUALITY = "mq";
  const PLAY_BTN = "havePlayBtn";

  const FILENAME_VARIANTS = array(
    self::MEDIUM_QUALITY => "-mq",
    self::PLAY_BTN => "-play"
  );

  const FILENAME_EXT = ".jpg";
  const FILENAME_FOLDER = "i/";

  public function YoutubeThumbnailer($options)
  {
    $this->fileSystem = new FileSystem();

    $this->input = trim($options["input"]);
    $this->inputAddHTTP();

    $this->quality = ($options["quality"] == self::HIGH_QUALITY) ?  self::HIGH_QUALITY : self::MEDIUM_QUALITY;
    $this->play = ($options["havePlayBtn"]) ? self::PLAY_BTN : "";
    $this->shouldRefresh = $options["shouldRefresh"];
  }

  public function injectFileSystem(FileSystem $fileSystem)
  {
    $this->fileSystem = $fileSystem;
  }

  public function isURL()
  {
    $pattern = "/^https?:\/\//";
    return !!preg_match($pattern, $this->input);
  }

  public function getID()
  {
    if (!$this->isURL())
    {
      return $this->input;
    }

    return $this->getYouTubeIdFromURL();
  }

  public function getFilename()
  {
    $filename = $this->getID();
    $filename .= self::FILENAME_VARIANTS[$this->quality];
    $filename .= self::FILENAME_VARIANTS[$this->play];

    return $filename;
  }

  public function getOutputFilename()
  {
    return self::FILENAME_FOLDER . $this->getFilename() . self::FILENAME_EXT;
  }

  public function hasValidCachedVersion()
  {
    $fileExist = $this->fileSystem->file_exists($this->getOutputFilename());
    $shouldRefresh = $this->shouldRefresh;

    return ($fileExist AND !$shouldRefresh);
  }

  public function isValidYouTubeVideo()
  {
    $id = $this->getID();
    $handle = curl_init("https://www.youtube.com/watch/?v=" . $id);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($handle);

    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);

    $invalid = ($httpCode == 404 OR !$response);
    return !$invalid;
  }

  public function createThumbnailImage()
  {
    $filename = $this->getFilename();
    $id = $this->getID();

    $image = $this->getImageFromYouTube();

    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    // ADD THE PLAY ICON
    $play_icon = $this->play ? "play-" : "noplay-";
    $play_icon .= $this->quality . ".png";
    $logoImage = imagecreatefrompng($play_icon);

    imagealphablending($logoImage, true);

    $logoWidth = imagesx($logoImage);
    $logoHeight = imagesy($logoImage);

    // CENTER PLAY ICON
    $left = round($imageWidth / 2) - round($logoWidth / 2);
    $top = round($imageHeight / 2) - round($logoHeight / 2);

    // CONVERT TO PNG SO WE CAN GET THAT PLAY BUTTON ON THERE
    imagecopy($image, $logoImage, $left, $top, 0, 0, $logoWidth, $logoHeight);
    imagepng($image, $filename .".png", 9);

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
  }

  private function getImageFromYouTube()
  {
    $youtubeURL = "http://img.youtube.com/vi/" . $this->getID() . "/" . $this->quality . "default.jpg";
    $image = imagecreatefromjpeg($youtubeURL);

    if($this->quality == self::HIGH_QUALITY)
    {
      $image = removeBlackBarsFromImage($image);
    }

    return $image
  }

  private function removeBlackBarsFromImage($image)
  {
    $cleft = 0;
    $ctop = 45;
    $canvas = imagecreatetruecolor(480, 270);
    imagecopy($canvas, $image, 0, 0, $cleft, $ctop, 480, 360);

    return $canvas;
  }

  private function getYouTubeIdFromURL()
  {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
    preg_match($pattern, $this->input, $matches);
    return isset($matches[1]) ? $matches[1] : false;
  }

  private function inputAddHTTP()
  {
    if ($this->inputStartsLikeURL())
    {
      $this->input = "http://" . $this->input;
    }
  }

  private function inputStartsLikeURL()
  {
    $pattern = "/^(www\.|youtube\.|youtu\.be)/";

    return preg_match($pattern, $this->input);
  }
}
