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

  private function inputStartsLikeURL()
  {
    $pattern = "/^(www\.|youtube\.|youtu\.be)/";

    return preg_match($pattern, $this->input);
  }
}
