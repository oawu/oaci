<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      OA Wu <comdan66@gmail.com>
 * @copyright   Copyright (c) 2015 OA Wu Design
 */

include_once 'ImageBaseUtility.php';

class ImageImagickUtility extends ImageBaseUtility {
  private $CI = null;
  private $options = null;

  public function __construct ($fileName, $options = array ()) {
    parent::__construct ($fileName);
    $this->CI =& get_instance ();
    $this->CI->load->helper ('oa');
    $this->CI->load->library ("cfg");
    $this->_init ()->_setOptions ($options);
  }


  public function make_block9 ($fileNames, $fileName = null, $rawData = true) {
    if (count ($fileNames) < 9)
      return false;
    $fileName = $fileName ? $fileName : utilitySameLevelPath (Cfg::system ('model', 'uploader', 'temp_directory') . DIRECTORY_SEPARATOR . Cfg::system ('model', 'uploader', 'temp_file_name')) . '.png';

    $newImage = new Imagick ();
    $newImage->newImage (266, 200, new ImagickPixel ('white'));
    $newImage->setFormat (pathinfo ($fileName, PATHINFO_EXTENSION));

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

    for ($i = 0; $i < 9; $i++)
      $newImage->compositeImage (ImageUtility::create ($fileNames[$i])->getImage (), imagick::COMPOSITE_DEFAULT, $positions[$i]['left'], $positions[$i]['top']);

    return $newImage->writeImages ($fileName, $rawData) ? $fileName : false;
  }

  public function addFont ($text, $font, $startX = 0, $startY = 12, $color = 'black', $fontSize = 12, $alpha = 1, $degree = 0) {
    if (!$text) return $this;

    if (!is_readable ($font))
      show_error ("The font not readable or not find!<br/>Please confirm your program again.");

    if (($startX < 0) || ($startY < 0))
      show_error ("The start point format error.<br/>It must be a numeric bigger or equal than zero.<br/>Please confirm your program again.");

    if (!is_string ($color))
      show_error ("The text color format error.<br/>It must be an string.<br/>Please confirm your program again.");

    if (!($fontSize && $fontSize > 0))
      show_error ("The text font size format error.<br/>It must be a numeric bigger or equal than one.<br/>Please confirm your program again.");

    if (!($alpha && is_numeric ($alpha) && ($alpha >= 0) && ($alpha <= 1)))
      show_error ("The alpha format error.<br/>It must be a float from 0 to 1.<br/>Please confirm your program again.");

    if (!is_numeric ($degree))
      show_error ("The degree format error.<br/>It must be a numeric.<br/>Please confirm your program again.");

    $draw = $this->_createFont ($font, $fontSize, $color, $alpha);

    if ($this->format == 'gif') {
      $newImage = new Imagick ();
      $newImage->setFormat ($this->format);
      $imagick = $this->image->clone ()->coalesceImages ();
      do {
        $temp = new Imagick ();
        $temp->newImage ($this->dimension['width'], $this->dimension['height'], new ImagickPixel ('transparent'));
        $temp->compositeImage ($imagick, imagick::COMPOSITE_DEFAULT, 0, 0);
        $temp->annotateImage ($draw, $startX, $startY, $degree, $text);
        $newImage->addImage ($temp);
        $newImage->setImageDelay ($imagick->getImageDelay ());
      } while ($imagick->nextImage ());
    } else {
      $newImage = $this->image->clone ();
      $newImage->annotateImage ($draw, $startX, $startY, $degree, $text);
    }

    return $this->_updateImage ($newImage);
  }

  private function _createFont ($font, $fontSize, $color, $alpha) {
    $draw = new ImagickDraw ();
    $draw->setFont ($font);
    $draw->setFontSize ($fontSize);
    $draw->setFillColor ($color);
    $draw->setFillAlpha ($alpha);
    return $draw;
  }

  public function saveAnalysisChart ($fileName, $font, $maxCount = 10, $fontSize = 14, $rawData = true) {
    if (!is_readable ($font))
      show_error ("The font not readable or not find!<br/>Please confirm your program again.");

    if (!(($mime_formats = Cfg::system ('image_utility', 'imgk', 'mime_formats')) && ($format = pathinfo ($fileName, PATHINFO_EXTENSION)) && in_array ($format, Cfg::system ('image_utility', 'imgk', 'allow_formats'))))
      show_error ("This file format is no support!<br/>Please confirm your file format again.");

    if (!$datas = $this->getAnalysisDatas ($maxCount))
      $show_error ("The image get analysis datas error!<br/>Please confirm your permissions again.");

    $newImage = new Imagick ();

    foreach ($datas as $data) {
      $newImage->newImage (400, 20, new ImagickPixel ('white'));

      $draw = new ImagickDraw ();
      $draw->setFont ($font);
      $draw->setFontSize ($fontSize);
      $newImage->annotateImage ($draw, 25, 14, 0, 'Percentage of total pixels : ' . (strlen ($data['percent'])<2?' ':'') . $data['percent'] . '% (' . $data['count'] . ')');

      $tile = new Imagick ();
      $tile->newImage (20, 20, new ImagickPixel ('rgb(' . $data['color']['r'] . ',' . $data['color']['g'] . ',' . $data['color']['b'] . ')'));

      $newImage->compositeImage ($tile, Imagick::COMPOSITE_OVER, 0, 0);
    }

    $newImage = $newImage->montageImage (new imagickdraw (), '1x' . count ($datas) . '+0+0', '400x20+4+2>', imagick::MONTAGEMODE_UNFRAME, '0x0+3+3');
    $newImage->setImageFormat ($format);
    $newImage->setFormat ($format);
    $newImage->writeImages ($fileName, $rawData);
    return $this;
  }

  public function getAnalysisDatas ($maxCount = 10) {
    if ($maxCount < 1)
      show_error ("The 'maxCount' of the position error.<br/>It must be a numeric bigger or equal than one.<br/>Please confirm your program again.");

    $temp = $this->image->clone ();

    $temp->quantizeImage ($maxCount, Imagick::COLORSPACE_RGB, 0, false, false );
    $pixels = $temp->getImageHistogram ();

    $datas = array ();
    $index = 0;
    $pixelCount = $this->dimension['width'] * $this->dimension['height'];

    if ($pixels && $maxCount) {
      foreach ($pixels as $pixel) {
        if ($index++ < $maxCount) {
          array_push ($datas, array ('color' => $pixel->getColor (), 'count' => $pixel->getColorCount (), 'percent' => round ($pixel->getColorCount () / $pixelCount * 100)));
        }
      }
    }
    return $this->_sort2dArray ('count', $datas);
  }

  private function _sort2dArray ($key, $list) {
    if ($list) {
      $tmp = array ();
      foreach ($list as &$ma) $tmp[] = &$ma[$key];
      array_multisort ($tmp, SORT_DESC, $list);
    }
    return $list;
  }

  public function lomography () {
    $newImage = new Imagick ();
    $newImage->setFormat ($this->format);

    if ($this->format == 'gif') {
      $imagick = $this->image->clone ()->coalesceImages ();
      do {
        $temp = new Imagick ();

        $imagick->setimagebackgroundcolor ("black");
        $imagick->gammaImage (0.75);
        $imagick->vignetteImage (0, max ($this->dimension['width'], $this->dimension['height']) * 0.2, 0 - ($this->dimension['width'] * 0.05), 0 - ($this->dimension['height'] * 0.05));

        $temp->newImage ($this->dimension['width'], $this->dimension['height'], new ImagickPixel ('transparent'));
        $temp->compositeImage ($imagick, imagick::COMPOSITE_DEFAULT, 0, 0);

        $newImage->addImage ($temp);
        $newImage->setImageDelay ($imagick->getImageDelay ());
      } while ($imagick->nextImage ());
    } else {
      $newImage = $this->image->clone ();
      $newImage->setimagebackgroundcolor("black");
      $newImage->gammaImage (0.75);
      $newImage->vignetteImage (0, max ($this->dimension['width'], $this->dimension['height']) * 0.2, 0 - ($this->dimension['width'] * 0.05), 0 - ($this->dimension['height'] * 0.05));
    }
    return $this->_updateImage ($newImage);
  }

  public function filter ($radius, $sigma, $channel = Imagick::CHANNEL_DEFAULT) {
    $items = array (imagick::CHANNEL_UNDEFINED, imagick::CHANNEL_RED,     imagick::CHANNEL_GRAY,  imagick::CHANNEL_CYAN,
                    imagick::CHANNEL_GREEN,     imagick::CHANNEL_MAGENTA, imagick::CHANNEL_BLUE,  imagick::CHANNEL_YELLOW,
                    imagick::CHANNEL_ALPHA,     imagick::CHANNEL_OPACITY, imagick::CHANNEL_MATTE, imagick::CHANNEL_BLACK,
                    imagick::CHANNEL_INDEX,     imagick::CHANNEL_ALL,     imagick::CHANNEL_DEFAULT);
    if (!is_numeric ($radius))
      show_error ("The radius format error.<br/>It must be a numeric.<br/>Please confirm your program again.");

    if (!is_numeric ($sigma))
      show_error ("The sigma format error.<br/>It must be a numeric.<br/>Please confirm your program again.");

    if (!in_array ($channel, $items))
      show_error ("The filter error.<br/>It must be one of Imagick channel constants of 'CHANNEL_UNDEFINED', 'CHANNEL_RED', 'CHANNEL_GRAY', 'CHANNEL_CYAN',<br/>'CHANNEL_GREEN', 'CHANNEL_MAGENTA', 'CHANNEL_BLUE', 'CHANNEL_YELLOW', 'CHANNEL_ALPHA', 'CHANNEL_OPACITY', 'CHANNEL_MATTE',<br/>'CHANNEL_BLACK', 'CHANNEL_INDEX', 'CHANNEL_ALL' or 'CHANNEL_DEFAULT'!<br/>Please confirm your program again.");

    $workingImage = $this->_machiningImageFilter ($radius, $sigma, $channel);

    return $this->_updateImage ($workingImage);
  }

  private function _machiningImageFilter ($radius, $sigma, $channel) {
    if ($this->format == 'gif') {
      $newImage = $this->image->clone ()->coalesceImages ();
      do {
        $newImage->adaptiveBlurImage ($radius, $sigma, $channel);
      } while ($newImage->nextImage () || !$newImage = $newImage->deconstructImages ());
    } else {
      $newImage = $this->image->clone ();
      $newImage = $newImage->adaptiveBlurImage ($radius, $sigma, $channel);
    }
    return $newImage;
  }

  public function rotate ($degree, $color = 'transparent') {
    if (!is_numeric ($degree))
      show_error ("The degree format error.<br/>It must be a numeric.<br/>Please confirm your program again.");

    if (!is_string ($color))
      show_error ("The new color format error.<br/>It must be an string.<br/>Please confirm your program again.");

    if (!($degree % 360)) return $this;

    $newImage = $this->_machiningImageRotate ($degree, $color);

    return $this->_updateImage ($newImage);
  }

  private function _machiningImageRotate ($degree, $color = 'transparent') {
    $newImage = new Imagick ();
    $newImage->setFormat ($this->format);
    $imagick = $this->image->clone ();

    if ($this->format == 'gif') {
      $imagick->coalesceImages();
      do {
        $temp = new Imagick ();

        $imagick->rotateImage (new ImagickPixel ($color), $degree);
        $newDimension = $this->getDimension ($imagick);
        $temp->newImage ($newDimension['width'], $newDimension['height'], new ImagickPixel ($color));
        $temp->compositeImage ($imagick, imagick::COMPOSITE_DEFAULT, 0, 0);

        $newImage->addImage ($temp);
        $newImage->setImageDelay ($imagick->getImageDelay ());
      } while ($imagick->nextImage ());
    } else {
      $imagick->rotateImage (new ImagickPixel ($color), $degree);
      $newDimension = $this->getDimension ($imagick);
      $newImage->newImage ($newDimension['width'], $newDimension['height'], new ImagickPixel ($color));
      $newImage->compositeImage ($imagick, imagick::COMPOSITE_DEFAULT, 0, 0);
    }
    return $newImage;
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

    $newImage = $this->_machiningImageCrop ($startX, $startY, $width, $height);

    return $this->_updateImage ($newImage);
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

    return $this->_updateImage ($this->_machiningImageCrop ($cropX, $cropY, $newDimension['width'], $newDimension['height']));
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

    $cropX = $cropY = 0;

    if ($this->dimension['width'] > $newDimension['width'])
      $cropX = intval (($percent / 100) * ($this->dimension['width'] - $newDimension['width']));
    else if ($this->dimension['height'] > $newDimension['height'])
      $cropY = intval (($percent / 100) * ($this->dimension['height'] - $newDimension['height']));

    $newImage = $this->_machiningImageCrop ($cropX, $cropY, $newDimension['width'], $newDimension['height']);

    return $this->_updateImage ($newImage);
  }

  private function _machiningImageCrop ($cropX, $cropY, $width, $height, $color = 'transparent') {
    $newImage = new Imagick ();
    $newImage->setFormat ($this->format);

    if ($this->format == 'gif') {
      $imagick = $this->image->clone ()->coalesceImages ();
      do {
        $temp = new Imagick ();
        $temp->newImage ($width, $height, new ImagickPixel ($color));
        $imagick->chopImage ($cropX, $cropY, 0, 0);
        $temp->compositeImage ($imagick, imagick::COMPOSITE_DEFAULT, 0, 0);

        $newImage->addImage ($temp);
        $newImage->setImageDelay ($imagick->getImageDelay ());
      } while ($imagick->nextImage ());
    } else {
      $imagick = $this->image->clone ();
      $imagick->chopImage ($cropX, $cropY, 0, 0);
      $newImage->newImage ($width, $height, new ImagickPixel ($color));
      $newImage->compositeImage ($imagick, imagick::COMPOSITE_DEFAULT, 0, 0 );
    }
    return $newImage;
  }


  public function pad ($width, $height, $color = 'transparent') {
    if (!((($width = intval ($width)) > 0) && (($height = intval ($height)) > 0)))
      show_error ("The new dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    if (($width == $this->dimension['width']) && ($height == $this->dimension['height']))
      return $this;

    if (!is_string ($color))
      show_error ("The new color format error.<br/>It must be an string.<br/>Please confirm your program again.");

    if (($width < $this->dimension['width']) || ($height < $this->dimension['height']))
      return $this->resize ($width, $height);

    $newImage = new Imagick ();
    $newImage->setFormat ($this->format);

    if ($this->format == 'gif') {
      $imagick = $this->image->clone ()->coalesceImages ();
      do {
        $temp = new Imagick ();
        $temp->newImage ($width, $height, new ImagickPixel ($color));
        $temp->compositeImage ($imagick, imagick::COMPOSITE_DEFAULT, intval (($width - $this->dimension['width']) / 2), intval (($height - $this->dimension['height']) / 2) );

        $newImage->addImage ($temp);
        $newImage->setImageDelay ($imagick->getImageDelay ());
      } while ($imagick->nextImage ());
    } else {
      $newImage->newImage ($width, $height, new ImagickPixel ($color));
      $newImage->compositeImage ($this->image->clone (), imagick::COMPOSITE_DEFAULT, intval (($width - $this->dimension['width']) / 2), intval (($height - $this->dimension['height']) / 2));
    }

    return $this->_updateImage ($newImage);
  }

  public function resize ($width, $height, $method = 'b') {
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

    return $this->_updateImage ($this->_machiningImageResize ($newDimension));
  }

  private function _machiningImageResize ($newDimension) {
    $newImage = $this->image->clone ()->coalesceImages ();

    if ($this->format == 'gif')
      do {
        $newImage->resizeImage ($newDimension['width'], $newDimension['height'], Imagick::FILTER_LANCZOS, 0.8, true);
      } while ($newImage->nextImage () || !$newImage = $newImage->deconstructImages ());
    else
      $newImage->resizeImage ($newDimension['width'], $newDimension['height'], Imagick::FILTER_LANCZOS, 0.8, true);

    return $newImage;
  }

  private function _updateImage ($image) {
    $this->image = $image;
    $this->dimension = $this->getDimension ($image);
    return $this;
  }

  public function save ($fileName, $rawData = true) {
    try {
      return $this->image->writeImages ($fileName, $rawData);
    } catch (Exception $e) {
      return false;
    }
  }

  private function _init () {
    if (!function_exists ('mime_content_type'))
      throw new ImageUtilityException ('ImageGdUtility 錯誤！', '尚未找尋到 mime_content_type 函式！', '請確認 php.ini 中的 fileinfo 有開啟！');

    if (!$this->mime = mime_content_type ($this->getFileName()))
      show_error ("The file format error!<br/>Please confirm your file format again.");

    if (!(($mime_formats = Cfg::system ('image_utility', 'imgk', 'mime_formats')) && isset ($mime_formats[$this->mime]) && ($this->format = $mime_formats[$this->mime]) && in_array ($this->format, Cfg::system ('image_utility', 'imgk', 'allow_formats'))))
      show_error ("This file format is no support!<br/>Please confirm your file format again.");

    if (!$this->image = new Imagick ($this->getFileName()))
      show_error ("The image is empty!<br/>Please confirm your program again.");

    if (!$this->dimension = $this->getDimension ($this->image))
      show_error ("The dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");

    return $this;
  }

  public function getDimension ($image = null) {
    $image = $image ? $image : $this->image->clone ();
    if (!($imagePage = ($imagePage = $image->getImagePage ()) && isset ($imagePage['width']) && isset ($imagePage['height']) && ($imagePage['width'] > 0) && ($imagePage['height'] > 0)  ? $imagePage : (($imagePage = $image->getImageGeometry ()) && isset ($imagePage['width']) && isset ($imagePage['height']) && ($imagePage['width'] > 0) && ($imagePage['height'] > 0) ? $imagePage : null)))
      show_error ("The dimension format error.<br/>It must be a number greater or equal than one.<br/>Please confirm your program again.");
    return $this->createDimension ($imagePage['width'], $imagePage['height']);
  }

  private function _setOptions ($options) {
    $this->options = array_merge (array ('resizeUp' => false), $options);
    return $this;
  }
}