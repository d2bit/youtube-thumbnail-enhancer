<?php
class YoutubeThumbnailer
{
  public function YoutubeThumbnailer($input)
  {
    $this->input = $input;
    $this->isURL = false;
  }

  public function addHTTP()
  {
    if ($this->inputStartsLikeURL())
    {
      $this->input = "http://" . $this->input;
      $this->isURL = true;
    }

    return $this;
  }

  public function isURL()
  {
    return $this->isURL;
  }

  public function getID()
  {
    $inpt = $this->input;

    if(substr($inpt, 0, 7) == "http://" OR substr($inpt, 0, 8) == "https://")
    {
      $this->isURL = true;
      $id = $this->getYouTubeIdFromURL($inpt);
    }

    if(!$this->isURL()) {
      $id = $inpt;
    }

    return $id;
  }

  private function getYouTubeIdFromURL($url)
  {
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
    preg_match($pattern, $url, $matches);
    return isset($matches[1]) ? $matches[1] : false;
  }

  private function inputStartsLikeURL()
  {
    $pattern = "/^(www\.|youtube\.|youtu\.be)/";

    return preg_match($pattern, $this->input);
  }
}
