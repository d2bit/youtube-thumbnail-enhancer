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
    $inpt = $this->input;

    if(substr($inpt, 0, 4) == "www.")
    {
      $inpt = "http://" . $inpt;
      $this->isURL = true;
    }

    if(substr($inpt, 0, 8) == "youtube.")
    {
      $inpt = "http://" . $inpt;
      $this->isURL = true;
    }

    if(substr($inpt, 0, 8) == "youtu.be")
    {
      $inpt = "http://" . $inpt;
      $this->isURL = true;
    }

    $this->input = $inpt;

    return $this;
  }

  public function isURL()
  {
    return $this->isURL;
  }
}
