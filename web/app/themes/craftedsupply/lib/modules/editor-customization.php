<?php

namespace CraftedSupply\Modules\EditorCustomization;


/**
 * Modify MCE
 *
 * @param array $settings
 * @return array
 */
function mce_before_init( array $settings ): array {

  // Add block formats
  $settings['formats'] = json_encode( [
    'p-large' => [
      'selector' => 'p',
      'block'    => 'p',
      'classes'  => '-large',
    ],
  ], JSON_THROW_ON_ERROR );

  $block_formats = [
    'Paragraph=p',
    'Large Paragrah=p-large',
    'Heading 2=h2',
    'Heading 3=h3',
    'Heading 4=h4',
    'Heading 5=h5',
    'Heading 6=h6'
  ];

  $settings['block_formats'] = implode( ';', $block_formats );

  // Add style formats
  $settings['style_formats'] = json_encode( [
    [
      'title' => 'Button',
      'selector' => 'a',
      'classes' => 'button'
    ],
  ] );

  // Add style formats
  // $settings['style_formats'] = json_encode( [
  //   [
  //     'title' => 'Buttons',
  //     'items' => [ [
  //       'title'    => 'Button',
  //       'selector' => 'a',
  //       'classes'  => 'button'
  //     ], [
  //       'title'    => 'Button (Secondary)',
  //       'selector' => 'a',
  //       'classes'  => 'button -secondary'
  //     ] ],
  //   ]
  // ] );

  // Control paste behavior
  $settings['paste_as_text'] = TRUE;
  $settings['paste_retain_style_properties'] = '';

  return $settings;
}
add_filter( 'tiny_mce_before_init', __NAMESPACE__ . '\\mce_before_init' );


/**
 * MCE Toolbars
 *
 * @param array $toolbars
 * @return array
 */
function mce_toolbars( array $toolbars ): array {

  $toolbars['Full'] = [
    1 => [
      'styleselect',
      'formatselect',
      'bold',
      'italic',
      'bullist',
      'numlist',
      'blockquote',
      'link',
      'spellchecker',
      'strikethrough',
      'superscript',
      'subscript',
      'charmap',
      'hr',
      'indent',
      'outdent',
      'pastetext',
      'removeformat',
      'redo',
      'undo',
    ]
  ];

  $toolbars['Full (No Headers)'] = [
    1 => [
      'styleselect',
      'bold',
      'italic',
      'bullist',
      'numlist',
      'blockquote',
      'link',
      'spellchecker',
      'strikethrough',
      'superscript',
      'subscript',
      'charmap',
      'hr',
      'indent',
      'outdent',
      'pastetext',
      'removeformat',
      'redo',
      'undo',
    ]
  ];

  $toolbars['Basic Text Styles'] = [
    1 => [
      'bold',
      'italic',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Basic Text Styles & Lists'] = [
    1 => [
      'bold',
      'italic',
      'bullist',
      'numlist',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'indent',
      'outdent',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Basic Text Styles & Links'] = [
    1 => [
      'bold',
      'italic',
      'link',
      'unlink',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Basic Text Styles, Links & Lists'] = [
    1 => [
      'bold',
      'italic',
      'link',
      'unlink',
      'bullist',
      'numlist',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'indent',
      'outdent',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Only Links'] = [
    1 => [
      'link',
      'unlink',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'undo',
      'redo',
    ],
  ];

  return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars' , __NAMESPACE__ . '\\mce_toolbars'  );