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
