<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */
require_once 'ImageBaseUtility.php';

class ImageGdUtility extends ImageBaseUtility {
  private $CI = null;
  private $options = null;
  
  public function __construct ($fileName, $options = array ()) {
    parent::__construct ($fileName);
    $this->CI =& get_instance ();
    $this->CI->load->helper ('oa');
    $this->CI->load->library ("cfg");
    $this->_init ()->_setOptions ($options);
  }

  public static function make_block9 ($fileNames, $fileName = null, $interlace = null, $jpegQuality = 100) {
    if (count ($fileNames) < 9)
      return false;
    $CI =& get_instance ();
    $CI->load->library ('ImageUtility');

    $positions = array (
      array ('left' =>   2, 'top' =>   2, 'width' => 130, 'height' => 130),
      array ('left' => 134, 'top' =>   2, 'width' =>  64, 'height' =>  64),
      array ('left' => 200, 'top' =>   2, 'width' =>  64, 'height' =>  64),
      array ('left' => 134, 'top' =>  68, 'width' =>  64, 'height' =>  64),
      array ('left' => 200, 'top' =>  68, 'width' =>  64, 'height' =>  64),
      array ('left' =>   2, 'top' => 134, 'width' =>  64, 'height' =>  64),
      array ('left' =>  68, 'top' => 134, 'width' =>  64, 'height' =>  64),
      array ('left' => 134, 'top' => 134, 'width' =>  64, 'height' =>  64),
      array ('left' => 200, 'top' => 134, 'width' =>  64, 'height' =>  64),
    );

    $image = imagecreatetruecolor (266, 200);
    imagefill ($image, 0, 0, imagecolorallocate ($image, 255, 255, 255));
    for ($i = 0; $i < 9; $i++)
      imagecopymerge ($image, ImageUtility::create ($fileNames[$i])->getImage (), $positions[$i]['left'], $positions[$i]['top'], 0, 0, $positions[$i]['width'], $positions[$i]['height'], 100);

    if ($interlace === true)
      imageinterlace ($image, 1);
    else if ($interlace === false)
      imageinterlace ($image, 0);

    $fileName = $fileName ? $fileName : utilitySameLevelPath (Cfg::system ('model', 'uploader', 'temp_directory') . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'temp_file_name')) . '.png';

    switch (pathinfo ($fileName, PATHINFO_EXTENSION)) {
      case 'jpg': return @imagejpeg ($image, $fileName, $jpegQuality) ? $fileName : '';
      case 'gif': return @imagegif ($image, $fileName) ? $fileName : '';
      case 'png': return @imagepng ($image, $fileName) ? $fileName : '';
      default: return '';
    }
  }

  public function adaptiveResizeQuadrant ($width, $height, $item = 'c') {
    if (!((($width = intval ($width)) > 0) && (($height = intval ($height)) > 0)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    if (($width == $this->dimension['width']) && ($height == $this->dimension['height']))
      return $this;

    $newDimension['width']  = ($this->options['resizeUp'] === false) && ($width > $this->dimension['width']) ? $this->dimension['width'] : $width;
    $newDimension['height'] = ($this->options['resizeUp'] === false) && ($height > $this->dimension['height']) ? $this->dimension['height'] : $height;

    if (!verifyDimension ($newDimension = $this->calcImageSizeStrict ($this->dimension, $newDimension)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    $this->resize ($newDimension['width'], $newDimension['height']);

    $newDimension['width']  = ($this->options['resizeUp'] === false) && ($width > $this->dimension['width']) ? $this->dimension['width'] : $width;
    $newDimension['height'] = ($this->options['resizeUp'] === false) && ($height > $this->dimension['height']) ? $this->dimension['height'] : $height;

    if (!verifyDimension ($newDimension))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    $newImage = $this->_preserveAlpha (function_exists ('imagecreatetruecolor') ? imagecreatetruecolor ($newDimension['width'], $newDimension['height']) : imagecreate ($newDimension['width'], $newDimension['height']));  

    $cropX = $cropY = 0;

    if ($this->dimension['width'] > $newDimension['width']) {
      switch ($item) {
        case 'l': case 'L': $cropX = 0; break;
        case 'r': case 'R': $cropX = intval ($this->dimension['width'] - $newDimension['width']); break;
        case 'c': case 'C': default: $cropX = intval (($this->dimension['width'] - $newDimension['width']) / 2); break;
      }
    } else if ($this->dimension['height'] > $newDimension['height']) {
      switch ($item) {
        case 't': case 'T': $cropY = 0; break;
        case 'b': case 'B': $cropY = intval ($this->dimension['height'] - $newDimension['height']); break;
        case 'c': case 'C': default: $cropY = intval(($this->dimension['height'] - $newDimension['height']) / 2); break;
      }
    }

    return $this->_copyReSampled ($newImage, $this->image, 0, 0, $cropX, $cropY, $newDimension['width'], $newDimension['height'], $newDimension['width'], $newDimension['height']);
  }

  public function rotate ($degree, $color = array (255, 255, 255)) {
    if (!function_exists ('imagerotate'))
      show_error ("Your version of GD does not support image rotation.<br/>Please confirm your program again.");

    if (!is_numeric ($degree))
      show_error ("The degree format error.<br/>It must be a numeric.<br/>Please confirm your program again.");

    if (!$color = $this->verifyColor ($color))
      show_error ("The new color format error.<br/>It must be an array which has three integer element 0 to 255.<br/>Please confirm your program again.");

    if (!($degree % 360)) return $this;
    
    $temp = function_exists ('imagecreatetruecolor') ? imagecreatetruecolor (1, 1) : imagecreate (1, 1);

    return $this->_updateImage (imagerotate ($this->image, 0 - $degree, imagecolorallocate ($temp, $color[0], $color[1], $color[2])));
  }

  public function cropFromCenter ($width, $height) {
    if (!((($width = intval ($width)) > 0) && (($height = intval ($height)) > 0)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    if (($width == $this->dimension['width']) && ($height == $this->dimension['height']))
      return $this;

    if (($width > $this->dimension['width']) && ($height > $this->dimension['height']))
      return $this->pad ($width, $height);

    return $this->crop (intval (($this->dimension['width'] - $width) / 2), intval (($this->dimension['height'] - $height) / 2), $this->dimension['width'] < $width ? $this->dimension['width'] : $width, $this->dimension['height'] < $height ? $this->dimension['height'] : $height);
  }

  public function crop ($startX, $startY, $width, $height) {
    if (!((($width = intval ($width)) > 0) && (($height = intval ($height)) > 0)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    if (($startX < 0) || ($startY < 0))
      show_error ("The start point format error.<br/>It must be a numeric bigger or equal than zero.<br/>Please confirm your program again.");

    if (($startX == 0) && ($startY == 0) && ($width == $this->dimension['width']) && ($height == $this->dimension['height']))
      return $this;

    $width  = $this->dimension['width'] < $width ? $this->dimension['width'] : $width;
    $height = $this->dimension['height'] < $height ? $this->dimension['height'] : $height;

    if (($startX + $width) > $this->dimension['width']) $startX = $this->dimension['width'] - $width;
    if (($startY + $height) > $this->dimension['height']) $startY = $this->dimension['height'] - $height;
    
    $workingImage = $this->_preserveAlpha ($workingImage = function_exists ('imagecreatetruecolor') ? imagecreatetruecolor ($width, $height) : imagecreate ($width, $height));
    
    return $this->_copyReSampled ($workingImage, $this->image, 0, 0, $startX, $startY, $width, $height, $width, $height);
  }

  public function pad ($width, $height, $color = array (255, 255, 255)) {
    if (!((($width = intval ($width)) > 0) && (($height = intval ($height)) > 0)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    if (($width == $this->dimension['width']) && ($height == $this->dimension['height']))
      return $this;

    if (!$color = $this->verifyColor ($color))
      show_error ("The new color format error.<br/>It must be an array which has three integer element 0 to 255.<br/>Please confirm your program again.");
    
    if (($width < $this->dimension['width']) || ($height < $this->dimension['height']))
      return $this->resize ($width, $height);
    
    $newImage = function_exists ('imagecreatetruecolor') ? imagecreatetruecolor ($width, $height) : imagecreate ($width, $height);

    imagefill ($newImage, 0, 0, imagecolorallocate ($newImage, $color[0], $color[1], $color[2]));
    
    return $this->_copyReSampled ($newImage, $this->image, intval (($width - $this->dimension['width']) / 2), intval (($height - $this->dimension['height']) / 2), 0, 0, $this->dimension['width'], $this->dimension['height'], $this->dimension['width'], $this->dimension['height']);
  }

  public function resizePercent ($percent = 0) {
    if ($percent < 1)
      show_error ("The percent of the position error.<br/>It must be a numeric bigger or equal than one.<br/>Please confirm your program again.");

    if ($percent == 100)
      return $this;

    if (!verifyDimension ($newDimension = $this->calcImageSizePercent ($percent, $this->dimension))) 
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    return $this->resize ($newDimension['width'], $newDimension['height']);
  }

  public function adaptiveResize ($width, $height) {
    return $this->adaptiveResizePercent ($width, $height, 50);
  }

  public function adaptiveResizePercent ($width, $height, $percent) {
    if (!((($width = intval ($width)) > 0) && (($height = intval ($height)) > 0)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    if (!(($percent > -1) && ($percent < 101)))
      show_error ("The percent of the position error.<br/>It must be a numeric from 0 to 100.<br/>Please confirm your program again.");

    if (($width == $this->dimension['width']) && ($height == $this->dimension['height']))
      return $this;

    $newDimension['width']  = ($this->options['resizeUp'] === false) && ($width > $this->dimension['width']) ? $this->dimension['width'] : $width;
    $newDimension['height'] = ($this->options['resizeUp'] === false) && ($height > $this->dimension['height']) ? $this->dimension['height'] : $height;

    if (!verifyDimension ($newDimension = $this->calcImageSizeStrict ($this->dimension, $newDimension)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    $this->resize ($newDimension['width'], $newDimension['height']);

    $newDimension['width']  = ($this->options['resizeUp'] === false) && ($width > $this->dimension['width']) ? $this->dimension['width'] : $width;
    $newDimension['height'] = ($this->options['resizeUp'] === false) && ($height > $this->dimension['height']) ? $this->dimension['height'] : $height;

    if (!verifyDimension ($newDimension))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    $newImage = $this->_preserveAlpha ($newImage = function_exists ('imagecreatetruecolor') ? imagecreatetruecolor ($newDimension['width'], $newDimension['height']) : imagecreate ($newDimension['width'], $newDimension['height']));  

    $cropX = $cropY = 0;

    if ($this->dimension['width'] > $newDimension['width'])
      $cropX = intval (($percent / 100) * ($this->dimension['width'] - $newDimension['width']));
    else if ($this->dimension['height'] > $newDimension['height'])
      $cropY = intval (($percent / 100) * ($this->dimension['height'] - $newDimension['height']));
    
    return $this->_copyReSampled ($newImage, $this->image, 0, 0, $cropX, $cropY, $newDimension['width'], $newDimension['height'], $newDimension['width'], $newDimension['height']);
  }

  public function resize ($width, $height, $method = 'both') {
    if (!((($width = intval ($width)) > 0) && (($height = intval ($height)) > 0)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    if (($width == $this->dimension['width']) && ($height == $this->dimension['height']))
      return $this;
 
    $newDimension['width']  = ($this->options['resizeUp'] === false) && ($width > $this->dimension['width']) ? $this->dimension['width'] : $width;
    $newDimension['height'] = ($this->options['resizeUp'] === false) && ($height > $this->dimension['height']) ? $this->dimension['height'] : $height;

    switch ($method) {
      case 'b': case 'both': default: $newDimension = $this->calcImageSize ($this->dimension, $newDimension); break;
      case 'w': case 'width': $newDimension = $this->calcWidth ($this->dimension, $newDimension); break;
      case 'h': case 'height': $newDimension = $this->calcHeight ($this->dimension, $newDimension); break;
    }

    if (!verifyDimension ($newDimension))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");
    
    $newImage = $this->_preserveAlpha ($newImage = function_exists ('imagecreatetruecolor') ? imagecreatetruecolor ($newDimension['width'], $newDimension['height']) : imagecreate ($newDimension['width'], $newDimension['height']));

    return $this->_copyReSampled ($newImage, $this->image, 0, 0, 0, 0, $newDimension['width'], $newDimension['height'], $this->dimension['width'], $this->dimension['height']);
  }

  private function _copyReSampled ($newImage, $oldImage, $newX, $newY, $oldX, $oldY, $newWidth, $newHeight, $oldWidth, $oldHeight) {
    imagecopyresampled ($newImage, $oldImage, $newX, $newY, $oldX, $oldY, $newWidth, $newHeight, $oldWidth, $oldHeight);
    return $this->_updateImage ($newImage);
  }

  private function _updateImage ($image) {
    $this->image = $image;
    $this->dimension = $this->getDimension ($this->image = $image);
    return $this;
  }
  private function _preserveAlpha ($image) {
    if (($this->format == 'png') && ($this->options['preserveAlpha'] === true)) {
      imagealphablending ($image, false);
      imagefill ($image, 0, 0, imagecolorallocatealpha ($image, $this->options['alphaMaskColor'][0], $this->options['alphaMaskColor'][1], $this->options['alphaMaskColor'][2], 0));
      imagesavealpha ($image, true);
    }

    if (($this->format == 'gif') && ($this->options['preserveTransparency'] === true)) {
      imagecolortransparent ($image, imagecolorallocate ($image, $this->options['transparencyMaskColor'][0], $this->options['transparencyMaskColor'][1], $this->options['transparencyMaskColor'][2]));
      imagetruecolortopalette ($image, true, 256);
    }
    return $image;
  }

  private function _setOptions ($options) {
    $this->options = array_merge (array ('interlace' => null, 'resizeUp' => false, 'jpegQuality' => 100, 'preserveAlpha' => true, 'preserveTransparency' => true, 'alphaMaskColor' => array (255, 255, 255), 'transparencyMaskColor' => array (0, 0, 0)), $options);
    return $this;
  }

  private function _init () {
    if (!$this->mime = mime_content_type ($this->getFileName()))
      show_error ("The file format error!<br/>Please confirm your file format again.");

    if (!(($mime_formats = Cfg::system ('image_utility', 'gd', 'mime_formats')) && isset ($mime_formats[$this->mime]) && ($this->format = $mime_formats[$this->mime]) && in_array ($this->format, Cfg::system ('image_utility', 'gd', 'allow_formats'))))
      show_error ("This file format is no support!<br/>Please confirm your file format again.");

    if (!$this->image = $this->_getOldImage ($this->format))
      show_error ("The image is empty!<br/>Please confirm your program again.");

    if (!$this->dimension = $this->getDimension ($this->image))
      show_error ("The dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    return $this;
  }

  public function save ($fileName) {
    if ($this->options['interlace'] === true)
      imageinterlace ($this->image, 1);
    else if ($this->options['interlace'] === false)
      imageinterlace ($this->image, 0);

    switch ($this->format) {
      case 'jpg': return @imagejpeg ($this->image, $fileName, $this->options['jpegQuality']);
      case 'gif': return @imagegif ($this->image, $fileName);
      case 'png': return @imagepng ($this->image, $fileName);
      default: return false;
    }
  }

  private function _getOldImage ($format) {
    switch ($format) {
      case 'gif': return imagecreatefromgif ($this->getFileName ());
      case 'jpg': return imagecreatefromjpeg ($this->getFileName ());
      case 'png': return imagecreatefrompng ($this->getFileName ());
      default: return null;
    }
  }

  public function getDimension ($image = null) {
    $image = $image ? $image : $this->_getOldImage ($this->format);
    return $this->createDimension (imagesx ($image), imagesy ($image));
  }
}