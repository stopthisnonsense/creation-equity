<?php
    add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
    add_filter('et_websafe_fonts', 'load_divi_custom_font',10,2);
    add_shortcode( 'team_members', 'team_members' );

function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_register_script( 'team_modal', 'https://unpkg.com/micromodal/dist/micromodal.min.js', [], null, true );
    wp_add_inline_script( 'team_modal', "
    MicroModal.init(
      {
        disableScroll: true,
      }
    );
    " );

    wp_register_script( 'team_parallax', 'https://cdn.jsdelivr.net/parallax.js/1.4.2/parallax.min.js', [ 'jquery' ], null, true );
    wp_add_inline_script( 'team_parallax', "jQuery( '.js-parallax--$number' ).parallax()" );

    wp_register_script( 'fade_in', get_stylesheet_directory_uri() . '/js/fade-in.js', ['jquery'], null, true );

    // wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ), false );
}

if( function_exists('acf_add_options_page') ) {

	acf_add_options_page();

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

function team_members() {
  $params = [
    'limit' => -1,
  ];

  $team_query = pods( 'team_member', $params );
  // var_dump( $team_query );

  if( 0 < $team_query->total() ) {

    if( !wp_script_is( 'team_modal', 'enqueued' ) ) {
      wp_enqueue_script( 'team_modal' );
    }
      $query_count = 1;

    $backgrounds = get_field( 'backgrounds', 'options' );

    $templates = "<div class='team-member-container'>";
    while( $team_query->fetch() ) {

      $team_id = $team_query->display('ID');
      $team_image = get_the_post_thumbnail_url( $team_id, 'full' );
      $team_popup_image = get_the_post_thumbnail( $team_id, 'full' );
      $team_name = get_the_title( $team_id );
      $team_title = $team_query->display( 'job_title' );
      $team_quote = $team_query->display( 'quote' );
      $team_content = $team_query->display( 'post_content' );
      $team_mail = $team_query->display( 'email' ) ? $team_query->display( 'email' ) : 'info@creationequity.com' ;

      // var_dump( $team_query->fetch() );
      $template = "<div class='team-member team-member--$team_id' data-micromodal-trigger='team-$team_id' style='background-image:url($team_image)'>
        <div class='team-member__content team-member__content--$team_id'>
          <h3 class='team-member__name team-member__name--$team_id'>$team_name</h3>
          <p class='team-member__title team-member__title--$team_id'><span>$team_title</span></p>
        </div>
      </div>
      <div id='team-$team_id' class='team-member-popup team-member-popup--$team_id' data-micromodal-close>
        <div class='team-member-popup__overlay team-member-popup__overlay--$team_id'>
          <div class='team-member-popup__image team-member-popup__image--$team_id'>
            $team_popup_image
          </div>
          <div class='team-member-popup__quote team-member-popup__quote--$team_id'>
            $team_quote
          </div>
          <div class='team-member-popup__info team-member-popup__info--$team_id'>
            <h4>$team_name</h4>
            <h4>$team_title</h4>
            $team_content
            <a href='mailto:$team_mail'>Contact $team_name</a>
          </div>

        </div>

      </div>
      "
      ;
      $templates .= $template;

      foreach( $backgrounds as $background ) {
        $templates .= team_member_backgrounds( $query_count, $background );
      }

      $query_count++;
    }
    $templates .= "</div>";
  }
  return $templates;
}

function data_set($item) {
  if( $item ) {
    return $item;
  }
}

function background_constructor( $data, $number ) {
  if( !$data ) {
    return;
  }
  $background_name = data_set( $data['name'] );
  $background_order = data_set( $data['order'] );
  $background_parallax = data_set( $data['parallax'] );
  $background_bg = esc_attr(data_set( $data['background_image'] ));
  $background_text = data_set( $data['text'] );
  $background_size = data_set( $data['size'] );

  $background_data = '';
  $classes = "team-member-container__background team-member-container__background--$number ";

  // if( $background_size ) {
  //   var_dump( $background_size );
  // }

  if( $background_parallax && $background_bg ) {
    if( !wp_script_is( 'team_parallax', 'enqueued' ) ) {
      wp_enqueue_script( 'team_parallax' );
    }
    $background_data .=  "data-parallax='scroll' data-image-src='$background_bg' style='background:transparent'";
    $classes .= " team_block-statement js-parallax--$number ";



  } else {
    $background_data .= "style='background-image:url($background_bg)'";

  }

  if( $background_size ) {
    $background_row_start = $background_size['row_start'] ? $background_size['row_start'] : 'auto';
    $background_column_start = $background_size['column_start'] ? $background_size['column_start'] : 'auto'  ;
    $background_rows = $background_size[ 'rows' ];
    $background_rows_mobile = $background_size[ 'rows' ] <= 2 ? $background_size[ 'rows' ] : 2;
    $background_columns = $background_size[ 'columns' ];
    $background_columns_mobile = $background_size[ 'columns' ] <= 2 ? $background_size[ 'columns' ] : 2;

    $background_css = "
    <style>
      .team-member-container__background--$number {
        grid-area:auto / auto / span 1 / span 1;
      }
      @media( min-width: 479px ) {
        .team-member-container__background--$number {
          grid-area: auto / auto / span $background_rows_mobile / span $background_columns_mobile;
        }
      }
      @media( min-width: 980px ) {
        .team-member-container__background--$number {
          grid-area: $background_row_start / $background_column_start / span $background_rows / span $background_columns;
        }
      }

    </style>";
    $background_parallax = $background_css;
  }

  if( $background_text ) {
    $classes .= ' team_text-statement ';
    $content = "<div class='et_pb_module js-fadein js-fadein--$number'>
      $background_text
    </div>";
    if( !wp_script_is( 'fade_in', 'enqueued' ) ) {
      wp_enqueue_script( 'fade_in' );
    }
  }

  $template = "<div class='$classes' $background_data>
      $content
    </div>
    $background_css";

  return $template;

}

function team_member_backgrounds( $number, $data ) {
  $template = '';
  // var_dump( $backgrounds );
  global $blockNumber;
  if( !isset( $blockNumber ) ) {
   $blockNumber = 1;
  }

  if( $number == $data[ 'order' ] ) {
    $template .= background_constructor( $data, $blockNumber );
    $blockNumber++;
  }


  return $template;

}
?>