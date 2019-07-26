<?php

function parseImageSrc($src) {
  
  // destruct array
  @['path' => $path, 'query' => $query] = parse_url($src);

  // parse query params
  parse_str($query, $params);

  return [
    'basename' => basename($path), 
    'params' => is_array($params) ? $params : [], 
  ];
}

function applyImageFilters($img, $params) {
  foreach ($params as $type => $value) {
    try {
      $img->__call($type, [ $value ]);
    } 
    // silently fail on invalid number of arguments or a filter doesnt exist
    catch (\InvalidArgumentException $e) {}
    catch (\BadFunctionCallException $e) {}
  }
}

function manipulateElement($Inline, $images, $config, $debuger = null) {
  
  $src = $Inline['element']['attributes']['src'];

  @['basename' => $basename, 'params' => $params] = parseImageSrc($src);

  $widths = $config['widths'];

  if (!is_array($widths)) return $Inline;

  // if image is not in collection, return original
  if (!array_key_exists($basename, $images)) return $Inline;

  $image = $images[$basename];

  // map srcset
  $srcset = array_map(function($w) use ($image, $params) {

    $img = $image->copy();

    // apply arguments
    applyImageFilters($img, $params);

    // maximum width = original image width
    if ($w['image_width'] < $img->width) {
      $img->resize($w['image_width']);
    }

    return $img->url() . ' ' . $w['inherent_width'] . 'w';
  
  }, $widths);

  $Inline['element']['attributes']['srcset'] = join($srcset, ',');
  $Inline['element']['attributes']['sizes'] = 'auto';

  // remove src tag from image
  unset($Inline['element']['attributes']['src']);

  return $Inline; 
}

function inlineImageExtended($images, $config = [], $debugger = null) {

  return function($Excerpt) use ($images, $config, $debugger) {

    $Inline = parent::inlineImage($Excerpt);

    return manipulateElement($Inline, $images, $config, $debugger);
  };
}
