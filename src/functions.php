<?php

function inlineImageExtended($images, $config = [], $debugger = null) {

  return function($Excerpt) use ($images, $config, $debugger) {

    $Inline = $this->inlineImage($Excerpt);
    $InlineOriginal = parent::inlineImage($Excerpt);

    $imageData = getOriginalImage($InlineOriginal['element']['attributes']['src'], $images);

    $Inline = manipulateElement($Inline, $imageData, $config, $debugger);
    
    return $Inline;
  };
}

function manipulateElement($Inline, $imageData, $config, $debuger = null) {
    
    if(!isset($Inline)) return null;
    
    if($imageData) {

      @[$image, $params] = $imageData;

      $image->derivatives([400,800,1000,1200]);
    }

    // return $Inline;
    // unset($Inline['element']['attributes']['sizes']);
    // $Inline['element']['attributes']['data-sizes'] = 'auto';



    $Inline['element']['attributes']['data-srcset'] = $Inline['element']['attributes']['srcset'];
    // $Inline['element']['attributes']['data-src'] = $Inline['element']['attributes']['src'];

    // $Inline['element']['attributes']['sizes'] = '(max-width: 400px) 800px, 90vw';
    // $Inline['element']['attributes']['data-sizes'] = '(max-width: 400px) 800px, 90vw';
    
    $Inline['element']['attributes']['class'] .= ' lazyload';

    unset($Inline['element']['attributes']['srcset']);
    unset($Inline['element']['attributes']['src']);

    return $Inline;

    // unset($Inline['element']['attributes']['sizes']);
    
    // set src fallback when image is not in page media
    if ($imageData) {
    }

    // set fallback when config fallback==true
    if ($imageData && $config['fallback']) {
      
      [$image, $params] = $imageData;

      $fallbackSettings = is_array($config['fallback_settings']) ? $config['fallback_settings'] : []; 

      foreach($fallbackSettings as $filter) {
        applyImageFilter($imgage, $filter['key'], $filter['value']);
      }

      $Inline['element']['attributes']['src'] = $image->url();
    }

    return $Inline;
}

// helper

function getOriginalImage($src, $images) {

  @['basename' => $basename, 'params' => $params] = parseImageSrc($src);
  
  if (!array_key_exists($basename, $images)) {
    return null;
  }

  return [ $images[$basename], $params];
}

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
    applyImageFilter($img, $type, $value);
  }
}

function applyImageFilter($img, $type, $value) {
  try {
    $img->__call($type, [ $value ]);
  } 
  // silently fail on invalid number of arguments or a filter doesnt exist
  catch (\InvalidArgumentException $e) {}
  catch (\BadFunctionCallException $e) {}
}
