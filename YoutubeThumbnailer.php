<?php
class YoutubeThumbnailer
{
  public function YoutubeThumbnailer($input, $quality = "mq", $play = false)
  {
    $this->input = trim($input);
    $this->inputAddHTTP();

    $this->quality = ($quality == "hq") ? "hq" : "mq";
    $this->play = $play;
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
    $play_btn_file_name = ($this->play) ? "-play" : "";
    $id = $this->getID();

    $filename = ($this->quality == "mq") ? $id . "-mq": $id;
    $filename .= $play_btn_file_name;

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
