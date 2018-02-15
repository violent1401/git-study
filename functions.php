<?php

define("TEMPLATE_URI", get_template_directory_uri());

function joints_theme_support() {

	// Add WP Thumbnail Support
	add_theme_support('post-thumbnails');
	// Add Support for WP Controlled Title Tag
	add_theme_support('title-tag');
	// Add HTML5 Support
	add_theme_support( 'html5',
		array(
			'comment-list',
			'comment-form',
			'search-form',
		)
	);


	remove_action('wp_head','wp_generator');
  remove_action('wp_head','wlwmanifest_link');
  remove_action('wp_head','rsd_link');
  remove_action('wp_head','rest_output_link_wp_head',10);
  remove_action('wp_head','wp_oembed_add_discovery_links');
	remove_action('rest_api_init', 'wp_oembed_register_route');
	remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
	remove_action('wp_head', 'wp_oembed_add_host_js');
  remove_action('wp_head', 'feed_links_extra', 3);
  remove_action('welcome_panel', 'wp_welcome_panel');
  remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
	add_filter( 'emoji_svg_url', '__return_false' );

	show_admin_bar(false);

	// Set the maximum allowed width for any content in the theme, like oEmbeds and images added to posts.
	$GLOBALS['content_width'] = apply_filters( 'joints_theme_support', 1200 );

	/*
	//support feed
  add_theme_support('automatic-feed-links');

  //default thumb size
  set_post_thumbnail_size(260, 260, true);
  add_image_size( 'simple_featured', 1140, 1140, true);
  load_theme_textdomain( 'textdomain', TEMPLATE_URI.'/languages' );
  add_theme_support( 'custom-background', array('default-color' => '#ffffff',));
  if(is_admin()){
    //Настройки страница темы
    include 'opt/theme-settings.php';
  }
  add_theme_support( 'custom-header',array(
	'flex-width'    => false,
	'width'         => 264,
	'flex-height'   => false,
	'height'        => 78,
	'default-image' => TEMPLATE_URI.'/img/logo.png',
  ));*/

	// Custom theme for admin interface
  include 'opt/trim-admin.php';
  // Widgets and sidebars
  include('opt/theme-widgets.php');
  // Ru translit slug
  include('opt/ru-translit.php');
	// Disabling WP REst Api
  include('opt/rest-api.php');

} /* end theme support */

add_action( 'after_setup_theme', 'joints_theme_support' );

function theme_style() {
  //wp_enqueue_style('font-pt-sans', '//fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic');
	//wp_enqueue_style('font-css', get_template_directory_uri() . '/assets/css/fonts.css', array(), null, 'all' );

  wp_enqueue_style( 'foundation-css', get_template_directory_uri() . '/assets/css/foundation.min.css', array(), '', 'all' );
  wp_enqueue_style( 'basic-css', get_template_directory_uri() . '/assets/css/basic-style.css', array(), '', 'all' );
  wp_enqueue_style( 'thm-css', get_template_directory_uri() . '/style.css', array(), '', 'all' );
}

function theme_scripts() {
  wp_deregister_script('jquery');
  wp_deregister_script('jquery-migrate');

  wp_register_script('jquery', TEMPLATE_URI.'/assets/js/jquery.min.js', array(), null, false );
  wp_enqueue_script( 'what-input', get_template_directory_uri() . '/assets/js/what-input.min.js', array(), '', true );
  wp_enqueue_script( 'foundation-js', get_template_directory_uri() . '/assets/js/foundation.min.js', array( 'jquery' ), '6.2.3', true );
  wp_enqueue_script( 'themejs', get_template_directory_uri() . '/assets/js/scripts.js', array( 'jquery' ), '', true );

  wp_enqueue_script('gmap', '//maps.googleapis.com/maps/api/js?key='.get_option('googleapi_key'), array('themejs'), null);
  /*
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
  */
}

add_action('wp_print_styles', 'theme_style');
add_action('wp_enqueue_scripts', 'theme_scripts');

register_nav_menus(
	array(
		'main-nav' => 'Главное меню',
	)
);

// The Top Menu
function joints_top_nav() {
	 wp_nav_menu(array(
        'container' => false,                           // Remove nav container
        'menu_class' => 'vertical medium-horizontal menu',       // Adding custom nav class
        'items_wrap' => '<ul id="%1$s" class="%2$s" data-responsive-menu="accordion medium-dropdown">%3$s</ul>',
        'theme_location' => 'main-nav',        			// Where it's located in the theme
        'depth' => 5,                                   // Limit the depth of the nav
        'fallback_cb' => false,                         // Fallback function (see below)
        'walker' => new Topbar_Menu_Walker()
    ));
}

// Big thanks to Brett Mason (https://github.com/brettsmason) for the awesome walker
class Topbar_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = Array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"menu\">\n";
    }
}

// The Off Canvas Menu
function joints_off_canvas_nav() {
	 wp_nav_menu(array(
        'container' => false,                           // Remove nav container
        'menu_class' => 'vertical menu',       // Adding custom nav class
        'items_wrap' => '<ul id="%1$s" class="%2$s" data-accordion-menu>%3$s</ul>',
        'theme_location' => 'main-nav',        			// Where it's located in the theme
        'depth' => 5,                                   // Limit the depth of the nav
        'fallback_cb' => false,                         // Fallback function (see below)
        'walker' => new Off_Canvas_Menu_Walker()
    ));
}

class Off_Canvas_Menu_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = Array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"vertical menu\">\n";
    }
}

// The Footer Menu
function joints_footer_links() {
    wp_nav_menu(array(
    	'container' => 'false',                         // Remove nav container
    	'menu' => __( 'Footer Links', 'jointswp' ),   	// Nav name
    	'menu_class' => 'menu',      					// Adding custom nav class
    	'theme_location' => 'footer-links',             // Where it's located in the theme
        'depth' => 0,                                   // Limit the depth of the nav
    	'fallback_cb' => ''  							// Fallback function
	));
} /* End Footer Menu */

// Header Fallback Menu
function joints_main_nav_fallback() {
	wp_page_menu( array(
		'show_home' => true,
    	'menu_class' => '',      						// Adding custom nav class
		'include'     => '',
		'exclude'     => '',
		'echo'        => true,
        'link_before' => '',                           // Before each link
        'link_after' => ''                             // After each link
	) );
}

// Footer Fallback Menu
function joints_footer_links_fallback() {
	/* You can put a default here if you like */
}

// Add Foundation active class to menu
function required_active_nav_class( $classes, $item ) {
    if ( $item->current == 1 || $item->current_item_ancestor == true ) {
        $classes[] = 'active';
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'required_active_nav_class', 10, 2 );

// Register sidebars/widget areas
//require_once(get_template_directory().'/assets/functions/sidebar.php');

function joints_comments($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class('panel'); ?>>
		<div class="media-object">
			<div class="media-object-section">
			    <?php echo get_avatar( $comment, 75 ); ?>
			  </div>
			<div class="media-object-section">
				<article id="comment-<?php comment_ID(); ?>">
					<header class="comment-author">
						<?php
							// create variable
							$bgauthemail = get_comment_author_email();
						?>
						<?php printf(__('%s', 'jointswp'), get_comment_author_link()) ?> on
						<time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__(' F jS, Y - g:ia', 'jointswp')); ?> </a></time>
						<?php edit_comment_link(__('(Edit)', 'jointswp'),'  ','') ?>
					</header>
					<?php if ($comment->comment_approved == '0') : ?>
						<div class="alert alert-info">
							<p><?php _e('Your comment is awaiting moderation.', 'jointswp') ?></p>
						</div>
					<?php endif; ?>
					<section class="comment_content clearfix">
						<?php comment_text() ?>
					</section>
					<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</article>
			</div>
		</div>
	<!-- </li> is added by WordPress automatically -->
<?php
}

// Replace 'older/newer' post links with numbered navigation
// Numeric Page Navi (built into the theme by default)
function joints_page_navi($before = '', $after = '') {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	$numposts = $wp_query->found_posts;
	$max_page = $wp_query->max_num_pages;
	if ( $numposts <= $posts_per_page ) { return; }
	if(empty($paged) || $paged == 0) {
		$paged = 1;
	}
	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor($pages_to_show_minus_1/2);
	$half_page_end = ceil($pages_to_show_minus_1/2);
	$start_page = $paged - $half_page_start;
	if($start_page <= 0) {
		$start_page = 1;
	}
	$end_page = $paged + $half_page_end;
	if(($end_page - $start_page) != $pages_to_show_minus_1) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}
	if($end_page > $max_page) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}
	if($start_page <= 0) {
		$start_page = 1;
	}
	echo $before.'<nav class="page-navigation"><ul class="pagination">'."";
	if ($start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = __( 'First', 'jointswp' );
		echo '<li><a href="'.get_pagenum_link().'" title="'.$first_page_text.'">'.$first_page_text.'</a></li>';
	}
	echo '<li>';
	previous_posts_link( __('Previous', 'jointswp') );
	echo '</li>';
	for($i = $start_page; $i  <= $end_page; $i++) {
		if($i == $paged) {
			echo '<li class="current"> '.$i.' </li>';
		} else {
			echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
		}
	}
	echo '<li>';
	next_posts_link( __('Next', 'jointswp'), 0 );
	echo '</li>';
	if ($end_page < $max_page) {
		$last_page_text = __( 'Last', 'jointswp' );
		echo '<li><a href="'.get_pagenum_link($max_page).'" title="'.$last_page_text.'">'.$last_page_text.'</a></li>';
	}
	echo '</ul></nav>'.$after."";
} /* End page navi */

// Remove 4.2 Emoji Support
function disable_wp_emoji() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emoji_tinymce' );
}
add_action( 'init', 'disable_wp_emoji' );

function disable_emoji_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}

/*
//добавляем тип поста
add_action( 'init', 'add_post_type' );

function add_post_type() {
  register_post_type('projects', array(
      'labels' => array(
          'name' => 'Проекты',
          'add_new' => 'Добавить',
          'singular_name' => 'Проекты',
          'add_new' => 'Добавить',
          'add_new_item' => 'Добавление проекта',
          'edit_item' => 'Редактирование проекта',
          'new_item' => 'Добавление проекта',
          'all_items' => 'Все проекты',
          'view_item' => 'Просмотреть на сайте',
          'search_items' => 'Найти',
      ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-archive',
      'show_ui' => true,
      'menu_position' => 8,
      'capability_type' => 'post',
      'hierarchical' => false,
      'query_var' => true,
      //перносит ссылку на второй уровень, первый уровень становится projects(пр. example.com/projects/post_name)
      //'rewrite' => array('slug'=>'projects'),
      'supports' => array(
          'title',
          'editor',
          'thumbnail'
      )
  ));

}

// добавляем тип поста
add_action('init', 'add_post_taxonomies');

// Custom Taxonomy
function add_post_taxonomies() {
  register_taxonomy( 'catprojects', array( 'projects', 'post' ),
    array(
      'labels' => array(
        'name'              => 'Тип проекта',
        'singular_name'     => 'Тип проекта',
        'search_items'      => 'Search Animal Families',
        'all_items'         => 'Все типы проектов',
        'edit_item'         => 'Редактирование типа проекта',
        'update_item'       => 'Обновить тип проекта',
        'add_new_item'      => 'Добавить тип проекта',
        'new_item_name'     => 'Название тип проекта',
        'menu_name'         => 'Типы проектов',
      ),
      'hierarchical' => true,
      'sort' => true,
      'args' => array( 'orderby' => 'term_order' ),
      'rewrite' => array( 'slug' => 'catprojects' ),
      'show_admin_column' => true
    )
  );
}
*/

// Related post function - no need to rely on plugins
// require_once(get_template_directory().'/assets/functions/related-posts.php');


function callback_apikey(){
  echo "<input class='regular-text' type='text' name='googleapi_key' value='". esc_attr(get_option('googleapi_key'))."'>";
}

function google_api(){
  add_settings_field('google_key','Google Maps API Key','callback_apikey','reading');
  register_setting('reading','googleapi_key');
}

add_action('admin_init', 'google_api');

function googleapi_admin($api) {
  $api['key'] = get_option('googleapi_key');
  return $api;
}

add_filter('acf/fields/google_map/api', 'googleapi_admin');
