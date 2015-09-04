<?php
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (! isset( $content_width)) {
    $content_width = 740; /* pixels */
}

if (! function_exists('silencio_setup')) {
    function silencio_setup() {
        require_once(dirname(__FILE__) . '/res/functions/admin.php');
        require(get_template_directory() . '/res/functions/template-tags.php');
        require(get_template_directory() . '/res/functions/shortcodes.php');
        require(get_template_directory() . '/res/functions/widgets.php');
        require(get_template_directory() . '/res/functions/excerpts.php');
        require(get_template_directory() . '/res/functions/tinymce.php');
        require(get_template_directory() . '/res/functions/users.php');
        require(get_template_directory() . '/res/functions/metaboxes.php');
        require( get_template_directory() . '/res/functions/jetpack.php');
        // require( get_template_directory() . '/res/functions/post-types.php');
        // require( get_template_directory() . '/res/functions/taxonomies.php');

        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        // add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'video', 'gallery' ) );

        register_nav_menu('primary', __('Primary Menu', 'silencio'));
        register_nav_menu('ancillary', __('Ancillary Menu', 'silencio'));
        // register_nav_menu( 'footer-menu', __( 'Footer Menu', 'silencio' ) );
    }
}

add_action('after_setup_theme', 'silencio_setup');

// Custom Thumbnail Sizes
if (function_exists('add_image_size')) {
    add_image_size('blog-thumb', 200, 160, true); //(cropped)
    add_image_size('header-thumb', 1170, 400, true); //(cropped)
}

/**
 * Enqueue scripts and styles
 */
function silencio_scripts() {
    if (!is_admin() && defined('VIA_ENVIRONMENT') && VIA_ENVIRONMENT == 'dev') {
        wp_deregister_script('jquery');
        wp_register_script('jquery', ("//code.jquery.com/jquery-1.10.2.js"), false, '1.8.2', true);
        wp_enqueue_script('jquery');

        // Enqueue 3rd party libs
        wp_enqueue_script('bootstrap', get_template_directory_uri() . '/res/js/bootstrap.js', array('jquery'), false, true);
        wp_enqueue_script('fitvids', get_template_directory_uri() . '/res/js/fitvids.js', array('jquery'), false, true);
        wp_enqueue_script('google.fastbutton', get_template_directory_uri() . '/res/js/google.fastbutton.js', array('jquery'), false, true);
        wp_enqueue_script('jquery.google.fastbutton', get_template_directory_uri() . '/res/js/jquery.google.fastbutton.js', array('jquery'), false, true);
    } elseif (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', ("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"), false, '1.8.2', true);
        wp_enqueue_script('jquery');
    }

    // Disable this environment check & load min.css if you want to test in IE8 with respond.js
    if (defined('VIA_ENVIRONMENT') && VIA_ENVIRONMENT == 'dev') {
        $global = 'js/global.js';
        $style = 'css/global.css';
    } else {
        $global = 'build/global.min.js';
        $style = 'build/global.min.css';
    }

    /*
     * If VIA_DEPLOYMENT is defined, contents are appended to bust client-side caches.
     * If it's not defined, fall back to file modified time.
     */
    wp_enqueue_script(
        'global',
        get_template_directory_uri() . '/res/' . $global,
        array('jquery'),
        defined('VIA_DEPLOYMENT') ? VIA_DEPLOYMENT : filemtime(get_stylesheet_directory() . '/res/' . $global),
        true
    );

    wp_register_style(
        'via-style',
        get_template_directory_uri() . '/res/' . $style,
        array(),
        defined('VIA_DEPLOYMENT') ? VIA_DEPLOYMENT : filemtime(get_stylesheet_directory() . '/res/' . $style),
        'all'
    );

    // enqueing:
    wp_enqueue_style('via-style');

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('wp_enqueue_scripts', 'silencio_scripts');

/**
 * Add oEmbed support for widgets
 */
add_filter('widget_text', array($wp_embed, 'run_shortcode'), 8);
add_filter('widget_text', array($wp_embed, 'autoembed'), 8);

/**
 * Register widgetized area and update sidebar with default widgets
 */
function silencio_widgets_init() {

    register_sidebar(array(
        'name' => __('Home Sidebar', 'silencio'),
        'id' => 'home-sidebar',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    register_sidebar(array(
        'name' => __('Page Sidebar', 'silencio'),
        'id' => 'page-sidebar',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    register_sidebar(array(
        'name' => __('Post Sidebar', 'silencio'),
        'id' => 'post-sidebar',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));

    register_sidebar(array(
        'name' => __('Footer Sidebar', 'silencio'),
        'id' => 'footer-sidebar',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
}

add_action('widgets_init', 'silencio_widgets_init');


/**
 * Fix Uncaught Reference Error from Gravity Forms - Puts js call in the footer, where it should be.
 */
add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
    return true;
}

/*
 * Registering Theme Options:
 */
require_once('res/functions/silencio-theme-settings-basic.php');

/*
 * Takes our theme option's location info and produces a maps link appropriate to the user's device.
 * @return string URL to appropriate maps service.
 */
function silencio_build_directions_url($street, $city, $state, $zip) {
    // Format our address for URLs
    $address = str_replace(' ', '+', implode('+', array($street, $city, $state, $zip))); // replaces all spaces w/ +

    // Check UA to see if this is an iPhone
    $is_iOS = strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone');

    // If yes, parse out the version of iOS
    if ($is_iOS !== false) {
        $results = array();
        preg_match('/iPhone OS (\d+)_(\d+)\s+/', $_SERVER['HTTP_USER_AGENT'], $results);
        $iOS_version = $results[1] . '.' . $results[2];
    }

    // Serve up an Apple Maps link if on an iOS 6 device, otherwise serve up a Google Maps link.
    if (isset($iOS_version) && $iOS_version >= 6) {
        return "https://maps.apple.com/maps?q=" . $address;
    } else {
        return "https://maps.google.com/maps?q=" . $address;
    }
}

/*
* Helper function for debugging variables
*
*/
if (!function_exists('debug')) {
    function debug($var) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);

        echo '<p><strong>' . basename($caller['file']) . ' - Line: ' . $caller['line'] . '</strong></p>';
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if (file_exists(dirname(__FILE__) . '/cmb2/init.php')) {
    require_once dirname(__FILE__) . '/cmb2/init.php';
} elseif (file_exists(dirname(__FILE__) . '/CMB2/init.php')) {
    require_once dirname(__FILE__) . '/CMB2/init.php';
}
