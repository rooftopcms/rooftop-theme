<?php
function setup() {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 825, 510, true );

    register_nav_menus( array(
        'main' => __('Main Menu',      'rooftopcms'),
        'footer'  => __('Footer Menu', 'rooftopcms'),
    ));

    // set the default Rooftop cache time (configured here rather than in the plugin itself which should have a generic config)
    add_filter('rooftop_response_header_options', function($options) {
        $options['cache_max_age_seconds'] = 60*10; // 10 mins
        return $options;
    });

    // FIXME: move these rest_query_vars filters into the rooftop-request-parser plugin
    add_filter( 'rest_query_vars', function( $valid_vars ) {
        $valid_vars = array_merge( $valid_vars, array( 'post__in', 'post__not_in' ) );

        return $valid_vars;
    });

    add_filter ( 'rest_query_vars', function( $valid_vars ) {
        $valid_vars = array_merge( $valid_vars, array( 'meta_key', 'meta_value', 'meta_query' ) );

        return $valid_vars;
    });
}
setup();

use ICanBoogie\Inflector;
class RooftopCMS {
    static function addContentType($type, $args = null) {
        $inflector = ICanBoogie\Inflector::get('en');
        $sanitised = str_replace(" ","_",strtolower($type));
        $singular = $inflector->titleize($type);
        $plural = $inflector->pluralize($singular);
        $default_args = array(
            'hierarchical' => false,
            'labels' => array(
                'name' => $plural,
                'singular_name' => $singular,
                'menu_name' => $plural,
                'name_admin_bar' => $singular,
                'all_items' => "All $plural",
                'add_new' => "New $singular",
                'add_new_item' => "New $singular",
                'edit_item' => "Edit $singular",
                'new_item' => "New $singular",
                'view_item' => "View $singular",
                'search_items' => "Search $plural",
                'not_found' => "No $plural found",
                'not_found_in_trash' => "No $plural found in trash",
                'parent_item_colon' => "Parent $singular:"
            ),
            'description' => "A $type",
            'public' => true,
            'supports' => array(
                'title', 'editor'
            ),
            'show_ui' => true,
            'menu_position' => 20,
            'capability_type' => 'page',
            'has_archive' => true,
            'show_in_rest' => true,
            'rest_base' => $inflector->underscore($inflector->pluralize($type)),
            'include_taxonomies_in_response' => true
        );

        if(is_null($args) || !is_array($args)){
            $args = $default_args;
        }else {
            $args = array_merge($default_args, $args);
        }

        register_post_type($sanitised, $args);
    }

    static function addTaxonomy($name, $content_type, $args = null) {
        $inflector = ICanBoogie\Inflector::get('en');
        $sanitised = str_replace(" ","_",strtolower($name));
        $human = $inflector->titleize($sanitised);
        $plural = $inflector->pluralize($human);
        $singular = $inflector->singularize($human);
        $default_args = array(
            'name' => $plural,
            'singular_name' => $singular,
            'labels' => array(
                'name' => $plural,
                'singular_name' => $singular,
                'menu_name' => $plural,
                'all_items' => "All $plural",
                'edit_item' => "Edit $singular",
                'view_item' => "View $singular",
                'update_item' => "Update $singular",
                'add_new_item' => "Add new $singular",
                'new_item_name' => "New $singular name",
                'parent_item' => "Parent $singular",
                'parent_item_colon' => "Parent $singular:",
                'search_items' => "Search $plural",
                'popular_items' => "Popular $plural",
                'separate_items_with_commas' => "Separate $plural with commas",
                'add_or_remove_items' => "Add or remove $plural",
                'choose_from_most_used' => "Most used $plural",
                'not_found' => "No $plural found"
            ),
            'show_in_rest' => true,
            'query_var' => true
        );

        if(is_null($args) || !is_array($args)){
            $args = $default_args;
        }else {
            $args = array_merge($default_args, $args);
        }

        if(is_array($content_type)){
            $types = $content_type;
        }elseif($content_type == 'all') {
            $types = get_post_types(array('public' => true));
        }else {
            $types = [$content_type];
        }

        foreach($types as $key => $type) {
            register_taxonomy($sanitised, $type, $args);
        }
    }
}

add_action( 'login_enqueue_scripts', function() {
    wp_enqueue_style( 'rooftop-login', get_stylesheet_directory_uri() . '/rooftop-login.css' );
} );

add_action( 'login_body_class', function() {
    $env = @$_ENV['WP_ENV'];
    return ["env-${env}"];
})
?>
