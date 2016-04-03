<?php
include './YoutubeThumbnailer.php';

class YoutubeThumbnailerTest extends PHPUnit_Framework_TestCase
{
  public function testAddHTTP()
  {
    $wwwUrl = "www.youtube.com/watch?v=XZ4X1wcZ1GE";
    $youtubeComUrl = "youtube.com/watch?v=XZ4X1wcZ1GE";
    $youtuBeUrl = "youtu.be/watch?v=XZ4X1wcZ1GE";

    $urlArray = [$wwwUrl, $youtubeComUrl, $youtuBeUrl];
    for($i = 0; $i < count($urlArray); $i++) {
      $url = $urlArray[$i];
      $thumbnailer = new YoutubeThumbnailer($url);

      $this->assertEquals("http://" . $url, $thumbnailer->addHTTP()->input);
      $this->assertTrue($thumbnailer->isURL());
    }
  }

  public function testDoNotAddHTTP()
  {
    $httpUrl = "http://www.youtube.com/watch?v=XZ4X1wcZ1GE";
    $thumbnailer = new YoutubeThumbnailer($httpUrl);

    $this->assertEquals($httpUrl, $thumbnailer->addHTTP()->input);
  }

  public function testGetID()
  {
    $youtubeId = "XZ4X1wcZ1GE";
    $httpUrl = "http://www.youtube.com/watch?v=" . $youtubeId;
    $thumbnailer = new YoutubeThumbnailer($httpUrl);
    $thumbnailer->addHTTP();

    $this->assertEquals($youtubeId, $thumbnailer->getID());
  }
}
