<?php
    add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
    add_filter('et_websafe_fonts', 'load_divi_custom_font',10,2);

function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    // wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ), false );
}

function load_divi_custom_font($fonts) {
  // Load the CSS-file inside your web-font kit
  wp_enqueue_style( 'divi-child', 'https://use.typekit.net/bym8odo.css' );
  // Add font to Divi's font menu
  $custom_font = array('proxima-nova' => array(
    'styles'        => '700,600,400',
    'character_set' => 'latin',
    'type'          => 'sans-serif',
    'standard'      => 1
  ));

  return array_merge($custom_font,$fonts);
}

?>