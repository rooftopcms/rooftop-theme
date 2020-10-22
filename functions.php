<?php
function setup() {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 825, 510, true );

    register_nav_menus( array(
        'main' => __('Main Menu',      'rooftopcms'),
        'footer'  => __('Footer Menu', 'rooftopcms'),
    ));

    // FIXME: move these rest_query_vars filters into the rooftop-request-parser plugin
    add_filter( 'rest_query_vars', function( $valid_vars ) {
        $valid_vars = array_merge( $valid_vars, array( 'post__in', 'post__not_in' ) );

        return $valid_vars;
    });

    add_filter ( 'rest_query_vars', function( $valid_vars ) {
        $valid_vars = array_merge( $valid_vars, array( 'meta_key', 'meta_value', 'meta_query' ) );

        return $valid_vars;
    });

    add_action( 'wp_loaded', function() {
        define( 'DISALLOW_FILE_MODS', true );
    } );
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
    $locale = "locale-" . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

    return ["env-${env}", "login-action-login", "wp-core-ui", $locale];
});

function acf_load_navigation_menu_choices( $field ) {
    $field['choices'] = array();

    foreach( wp_get_nav_menus() as $menu ) {
        $field['choices'][$menu->term_id] = $menu->name;
    }

    return $field;
}
add_filter('acf/load_field/name=venue_navigation_menu_id', 'acf_load_navigation_menu_choices');

function make_relative(  $url, $post ) {
    $url = wp_make_link_relative($url);
    return $url;
}

// make links relative in the content editor
add_action( 'init', function() {
    $types = get_post_types( array( 'public' => true, 'show_in_graphql' => true ) );

    foreach( $types as $type ) {
        if( $type == "attachment") continue; // make_relative doesn't work with the 'attachment' content type

        add_filter( $type.'_link', 'make_relative', 10, 2 );
    }
}, 1000);

add_filter( 'post_type_link', 'make_relative', 10, 2);

add_filter( 'acf/location/rule_match/page_template', function($result, $rule, $screen, $field_group) {
    // if we're GET'ing, return the result from the acf helper
    if( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
        return $result;
    }
    return true;
}, 10, 4 );

function my_acf_init() {
	acf_update_setting('google_api_key', @$_ENV['GOOGLE_MAPS_API_KEY']);
}
add_action('acf/init', 'my_acf_init');

add_action( 'graphql_register_types', function() {
    register_graphql_field( 'RootQueryToEventConnectionWhereArgs', 'date', [
        'type' => 'String',
        'description' => 'Event Date'
    ] );

    register_graphql_field( 'RootQueryToEventConnectionWhereArgs', 'venue', [
        'type' => 'Int',
        'description' => 'Event Venue ID'
    ] );

    register_graphql_field( 'RootQueryToEventConnectionWhereArgs', 'audience_type', [
        'type' => 'Int',
        'description' => 'Audience Type'
    ] );

    register_graphql_field( 'RootQueryToEventConnectionWhereArgs', 'event_type', [
        'type' => 'Int',
        'description' => 'Event Type'
    ] );
}, 10 );

add_filter( 'graphql_post_object_connection_query_args', function( $query_args, $source, $args, $context, $info) {
    $meta_queries = [];
    $date_queries = [];
    $parent_condition = 'OR';

    if( isset($query_args['date']) ) {
        $parent_condition = 'AND';
        
        $date_queries = [
            [
                'relation' => 'OR', 
                [
                    'relation' => 'AND',
                    [
                        'key' => 'start_date',
                        'value' => $query_args['date'] . ' 00:00:00',
                        'type' => 'DATE',
                        'compare' => '<='
                    ],
                    [
                        'key' => 'end_date',
                        'value' => $query_args['date'] . ' 00:00:00',
                        'type' => 'DATE',
                        'compare' => '>='
                    ]
                ],
                [
                    'relation' => 'AND',
                    [
                        'key' => 'start_date',
                        'value' => $query_args['date'] . ' 00:00:00',
                        'type' => 'DATE',
                        'compare' => '>='
                    ], 
                ]
            ]
        ];
    }

    if( isset($query_args['audience_type']) ) {
        $meta_queries[] = [
            'key' => 'audience_type',
            'value' => $query_args['audience_type'],
            'compare' => 'LIKE'
        ];
    }
    
    if( isset($query_args['event_type']) ) {
        $meta_queries[] = [
            'key' => 'event_type',
            'value' => $query_args['event_type'],
            'compare' => 'LIKE'
        ];
    }

    if( isset($query_args['venue']) ) {
        $meta_queries[] = [
            'key' => 'venue',
            'value' => $query_args['venue'],
            'compare' => 'LIKE'
        ];
    }

    if( count( $meta_queries ) > 0 ) {
        $filter_queries = [
            'relation' => 'OR'
        ];

        array_map( function( $mq ) use (&$filter_queries) {
            $filter_queries[] = $mq;
        }, $meta_queries );

        $query_args['meta_query'][] = $filter_queries;

        $query_args['meta_query']['relation'] = $parent_condition;

        if( count( $date_queries ) > 0 ) {
            $query_args['meta_query'][] = $date_queries;
        }
    }else if( $date_queries ) {
        $query_args['meta_query'][] = $date_queries;
    }

        // wp_send_json( get_posts( $query_args ) );
        // wp_send_json( $query_args['meta_query'] );

    return $query_args;
}, 10, 5);

?>
