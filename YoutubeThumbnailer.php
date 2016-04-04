<?php
class YoutubeThumbnailer
{
  const HIGH_QUALITY = "hq";
  const MEDIUM_QUALITY = "mq";
  const PLAY_BTN = "havePlayBtn";

  const FILENAME_EXTENSIONS = array(
    self::MEDIUM_QUALITY => "-mq",
    self::PLAY_BTN => "-play"
  );

  public function YoutubeThumbnailer($options)
  {
    $this->input = trim($options["input"]);
    $this->inputAddHTTP();

    $this->quality = ($options["quality"] == self::HIGH_QUALITY) ?  self::HIGH_QUALITY : self::MEDIUM_QUALITY;
    $this->play = ($options["havePlayBtn"]) ? self::PLAY_BTN : "";
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
    $filename .= self::FILENAME_EXTENSIONS[$this->quality];
    $filename .= self::FILENAME_EXTENSIONS[$this->play];

    return $filename;
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
