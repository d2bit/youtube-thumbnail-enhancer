<?php
class YoutubeThumbnailer
{
  public function YoutubeThumbnailer($input)
  {
    $this->input = $input;
  }

  public function addHTTP()
  {
    if ($this->inputStartsLikeURL())
    {
      $this->input = "http://" . $this->input;
    }

    return $this;
  }

  public function isURL()
  {
    $pattern = "/^https?:\/\//";
    return !!preg_match($pattern, $this->input);
  }

  public function getID()
  {
    $inpt = $this->input;

    if ($this->isURL())
    {
      $id = $this->getYouTubeIdFromURL($inpt);
    } else {
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
