<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2014 OA Wu Design
 */
class ImageBaseUtility {
  private $fileName = null;
  protected $mime = null;
  protected $format = null;
  protected $image = null;
  protected $dimension = null;

  public function __construct ($fileName) {
    if (!is_readable ($fileName))
      return show_error ("The image file : " . $fileName . " not found or not readable!<br/>Please confirm your program again.");
    $this->setFileName ($fileName);
  }

  protected function setFileName ($fileName) {
    $this->fileName = $fileName;
    return $this;
  }

  public function getFileName () {
    return $this->fileName;
  }

  public function getMime () {
    return $this->mime;
  }

  public function getFormat () {
    return $this->format;
  }

  public function getImage () {
    return $this->image;
  }


  public function colorHex2Rgb ($hex) {
    if (($hex = str_replace ('#', '', $hex)) && ((strlen ($hex) == 3) || (strlen ($hex) == 6))) {
      if(strlen ($hex) == 3) {
        $r = hexdec (substr ($hex, 0, 1) . substr ($hex, 0, 1));
        $g = hexdec (substr ($hex, 1, 1) . substr ($hex, 1, 1));
        $b = hexdec (substr ($hex, 2, 1) . substr ($hex, 2, 1));
      } else {
        $r = hexdec (substr ($hex, 0, 2));
        $g = hexdec (substr ($hex, 2, 2));
        $b = hexdec (substr ($hex, 4, 2));
      }
      return array ($r, $g, $b);
    } else {
      return array ();
    }
  }

  protected function verifyColor ($color) {
    return ($color = is_string ($color) ? $this->colorHex2Rgb ($color) : $color) && is_array ($color) && (count ($color) == 3) && ($color[0] >= 0) && ($color[0] <= 255) && ($color[1] >= 0) && ($color[1] <= 255) && ($color[2] >= 0) && ($color[2] <= 255) ? $color : false;
  }

  protected function createDimension ($width, $height) {
    return ($width > 0) && ($height > 0) ? array (
      'width'  => intval ($width),
      'height' => intval ($height)
    ) : show_error ("Create dimension Error!<br/>Please confirm your program again.");
  }

  protected function calcWidth ($oldDimension, $newDimension) {
    $newWidthPercentage = ceil (100 * $newDimension['width'] / $oldDimension['width']);
    $height             = ceil ($oldDimension['height'] * $newWidthPercentage / 100);
    return $this->createDimension ($newDimension['width'], $height);
  }

  protected function calcHeight ($oldDimension, $newDimension) {
    $newHeightPercentage  = ceil (100 * $newDimension['height'] / $oldDimension['height']);
    $width                = ceil ($oldDimension['width'] * $newHeightPercentage / 100);
    return $this->createDimension ($width, $newDimension['height']);
  }

  protected function calcImageSize ($oldDimension, $newDimension) {
    $newSize = $this->createDimension ($oldDimension['width'], $oldDimension['height']);

    if ($newDimension['width'] > 0) {
      $newSize = $this->calcWidth ($oldDimension, $newDimension);
      if (($newDimension['height'] > 0) && ($newSize['height'] > $newDimension['height']))
        $newSize = $this->calcHeight($oldDimension, $newDimension);
    }
    if ($newDimension['height'] > 0) {
      $newSize = $this->calcHeight ($oldDimension, $newDimension);
      if (($newDimension['width'] > 0) && ($newSize['width'] > $newDimension['width']))
        $newSize = $this->calcWidth ($oldDimension, $newDimension);
    }
    return $newSize;
  }

  protected function calcImageSizeStrict ($oldDimension, $newDimension) {
    $newSize = $this->createDimension ($newDimension['width'], $newDimension['height']);
    
    if ($newDimension['width'] >= $newDimension['height']) {
      if ($oldDimension['width'] > $oldDimension['height'])  {
        $newSize = $this->calcHeight ($oldDimension, $newDimension);
        
        if ($newSize['width'] < $newDimension['width']) {
          $newSize = $this->calcWidth ($oldDimension, $newDimension);
        }
      } else if ($oldDimension['height'] >= $oldDimension['width']) {
        $newSize = $this->calcWidth ($oldDimension, $newDimension);
        
        if ($newSize['height'] < $newDimension['height']) {
          $newSize = $this->calcHeight ($oldDimension, $newDimension);
        }
      }
    } else if ($newDimension['height'] > $newDimension['width']) {
      if ($oldDimension['width'] >= $oldDimension['height']) {
        $newSize = $this->calcWidth ($oldDimension, $newDimension);
        
        if ($newSize['height'] < $newDimension['height']) {
          $newSize = $this->calcHeight ($oldDimension, $newDimension);
        }
      } else if ($oldDimension['height'] > $oldDimension['width']) {
        $newSize = $this->calcHeight ($oldDimension, $newDimension);
        
        if ($newSize['width'] < $newDimension['width']) {
          $newSize = $this->calcWidth ($oldDimension, $newDimension);
        }
      }
    }
    return $newSize;
  }

  protected function calcImageSizePercent ($percent, $dimension) {
    return $this->createDimension (ceil ($dimension['width'] * $percent / 100), ceil ($dimension['height'] * $percent / 100));
  }
}