<?php

use Spec\ImageMock;

describe('tests', function() {

  beforeEach(function() {
    $this->config = [ 
      'widths' => [
        [ 'image_width' => 480, 'inherent_width' => 480 ], 
        [ 'image_width' => 960, 'inherent_width' => 960 ] 
      ],
    ];
  });


  it('should set `srcset` and remove `src` attribute', function() {

    // given
    $images = [ 'bar.jpeg' => new ImageMock('/foo/bar.jpeg') ];
    $Inline = [
      'element' => [
        'attributes' => [
          'src' => 'bar.jpeg'
        ]
      ]
    ];

    // when
    $el = manipulateElement($Inline, $images, $this->config); 
    
    // then
    expect($el['element']['attributes']['srcset'])->toBe('/foo/bar.jpeg?mocked 480w,/foo/bar.jpeg?mocked 960w');
    expect(isset($el['element']['attributes']['src']))->toBeFalsy();
  });


  it('should not set `srcset` when image not in page media', function() {

    // given
    $Inline = [
      'element' => [
        'attributes' => [
          'src' => 'bar.jpeg'
        ]
      ]
    ];

    // when
    $el = manipulateElement($Inline, [], $this->config); 

    // // then
    expect($el['element']['attributes']['src'])->toBe('bar.jpeg');
    expect(isset($el['element']['attributes']['srcset']))->toBeFalsy();
  });


  it('should apply image manipulation arguments', function() {

    // given
    $image = new ImageMock('/foo/bar.jpeg');
    $images = [ 'bar.jpeg' => $image ];
    $Inline = [
      'element' => [
        'attributes' => [
          'src' => 'bar.jpeg?sepia&gaussianBlur=2'
        ]
      ]
    ];

    // then
    expect($image)->toReceive('sepia')->with('')->times(2);
    expect($image)->toReceive('gaussianBlur')->with('2')->times(2);
    expect($image)->toReceive('resize')->with(480)->with(960);
    // expect($image)->toReceive('resize')->times(1);

    // when
    $el = manipulateElement($Inline, $images, $this->config); 
  });
  

  it('should not increase image size when original image is smaller', function() {

    // given
    $image = new ImageMock('/foo/bar.jpeg', [ 500, 500 ]);
    $images = [ 'bar.jpeg' => $image ];
    $Inline = [
      'element' => [
        'attributes' => [
          'src' => 'bar.jpeg'
        ]
      ]
    ];   

    // then
    expect($image)->toReceive('resize')->times(1);
    expect($image)->toReceive('resize')->with(480);

    // when
    $el = manipulateElement($Inline, $images, $this->config); 
  });

});


