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
    $pattern = "/^(www\.|youtube\.|youtu\.be)/";
    if (preg_match($pattern, $this->input))
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
}
