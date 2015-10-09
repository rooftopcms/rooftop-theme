<?php
function setup() {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 825, 510, true );

    register_nav_menus( array(
        'main' => __('Main Menu',      'rooftopcms'),
        'footer'  => __('Footer Menu', 'rooftopcms'),
    ));
}
setup();

class RooftopCMS {
    static function addContentType($type, $args = null) {
        $default_args = array(
            'hierarchical' => false,
            'labels' => array(
                'name' => "${type}s",
                'singular_name' => $type,
                'menu_name' => "${type}s",
                'name_admin_bar' => $type,
                'all_items' => "All ${type}s",
                'add_new' => "New $type",
                'add_new_item' => "New $type",
                'edit_item' => "Edit $type",
                'new_item' => "New $type",
                'view_item' => "View $type",
                'search_items' => "Search $type",
                'not_found' => "No $type found",
                'not_found_in_trash' => "No $type found in trash",
                'parent_item_colon' => "Parent $type:"
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
            'include_taxonomies_in_response' => true
        );

        if(is_null($args) || !is_array($args)){
            $args = $default_args;
        }else {
            $args = array_merge($default_args, $args);
        }

        register_post_type($type.'s', $args);
    }

    static function addTaxonomy($name, $content_type, $args = null) {
        $default_args = array(
            'name' => $name,
            'singular_name' => $name,
            'labels' => array(
                'name' => $name,
                'singular_name' => $name,
                'menu_name' => $name,
                'all_items' => "All $name",
                'edit_item' => "Edit $name",
                'view_item' => "View $name",
                'update_item' => "Update $name",
                'add_new_item' => "Add new $name",
                'new_item_name' => "New $name name",
                'parent_item' => "Parent $name",
                'parent_item_colon' => "Parent $name:",
                'search_items' => "Search $name",
                'popular_items' => "Popular $name",
                'separate_items_with_commas' => "Separate ${name}s with commas",
                'add_or_remove_items' => "Add or remove ${name}s",
                'choose_from_most_used' => "Most used ${name}s",
                'not_found' => "No $name found"
            ),
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
            register_taxonomy($name, $type, $args);
        }
    }
}
?>
