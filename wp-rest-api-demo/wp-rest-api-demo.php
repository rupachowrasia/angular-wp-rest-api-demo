<?php
/*
Plugin Name: WP Rest API Demo
Plugin URI: https://crowdfavorite.com/
Version: 1.0.0
Description: Rest API Demo Test.
Author: Crowd Favorite
Author URI: https://crowdfavorite.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: cfwprapi
*/

/**
 * Add REST API support to an already registered post type(tribe_events).
 *
 * @since 1.0.0
 */
function cfwprapi_custom_post_type_rest_support() {
    global $wp_post_types;

    $post_type_name = 'tribe_events';

    if( isset( $wp_post_types[ $post_type_name ] ) ) {
        $wp_post_types[$post_type_name]->show_in_rest = true;
        $wp_post_types[$post_type_name]->rest_base = $post_type_name;
        $wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
    }

}
add_action( 'init', 'cfwprapi_custom_post_type_rest_support', 25 );

/**
 * Add REST API support to an already registered taxonomy(tribe_events_cat).
 *
 * @since 1.0.0
 */
function cfwprapi_custom_taxonomy_rest_support() {
    global $wp_taxonomies;

    $taxonomy_name = 'tribe_events_cat';

    if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
        $wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
        $wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
        $wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
    }

}
add_action( 'init', 'cfwprapi_custom_taxonomy_rest_support', 25 );

/**
 * Get all events.
 *
 * @since 1.0.0
 */
function cfwprapi_get_all_events() {
    $posts = get_posts( array(
        'post_type' => 'tribe_events',
    ) );

    if ( empty( $posts ) ) {
        return new WP_Error( 'no_event_found', 'Invalid event', array( 'status' => 404 ) );
    }

    return $posts;
}

/**
 * Get all event categories.
 *
 * @since 1.0.0
 */
function cfwprapi_get_all_categories( $data ) {

    $categories = get_terms( 'tribe_events_cat', 'orderby=count&hide_empty=0' );

    if ( empty( $categories ) ) {
        return new WP_Error( 'no_category_found', 'Invalid Category', array( 'status' => 404 ) );
    }

    return $categories;
}

/**
 * Get event by event-id.
 *
 * @since 1.0.0
 */
function cfwprapi_get_event_by_id( $data ) {
    $posts = get_post( $data['id'] );

    if ( empty( $posts ) ) {
        return new WP_Error( 'no_event_found', 'Invalid event', array( 'status' => 404 ) );
    }

    return $posts;
}

/**
 * Creating custom routes and endpoints for already registered post-type and taxonomy.
 *
 * @since 1.0.0
 */
function cfwprapi_create_custom_routes() {

    register_rest_route( 'wp-rest-api-demo/v2', '/events', array(
        'methods' => 'GET',
        'callback' => 'cfwprapi_get_all_events',
    ) );

    register_rest_route( 'wp-rest-api-demo/v2', '/event-categories', array(
        'methods' => 'GET',
        'callback' => 'cfwprapi_get_all_categories',
    ) );

    register_rest_route( 'wp-rest-api-demo/v2', '/event/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'cfwprapi_get_event_by_id',
    ) );

}
add_action( 'rest_api_init', 'cfwprapi_create_custom_routes' );

/**
 * Add event url field for already registerd custom post type
 */
function cfwprapi_add_event_url_field() {

    $schema = array(
        'type' => 'int',
        'description' => 'hggvhgvh',
        'context' => array( 'edit', 'view' ),
        );

    register_api_field( 'tribe_events',
        'event_url',
        array(
            'schema' => $schema,
            'get_callback' => 'cfwprapi_event_url_get_callback',
            'update_callback' => 'cfwprapi_event_url_update_callback',
        )
    );

}
add_action('rest_api_init', 'cfwprapi_add_event_url_field' );

/**
 * Get the value of the added field(event_url)
 */
function cfwprapi_event_url_get_callback( $post_data ) {
    $event_url = get_post_meta( $post_data[ 'id' ], '_EventURL', true );

    return esc_url( $event_url );
}

/**
 * Updating custom field(event_url) data.
 */
function cfwprapi_event_url_update_callback( $value, $post ) {
    if ( ! $value ) {
        return;
    }

    $value = esc_url_raw( $value );

    return update_post_meta( $post->id, '_EventURL', $value );
}

/**
 *  Show a post meta field in post responses
 */
function cfwprapi_testing_func() {
    register_rest_field( 'post',
        'test_field',
        array(
            'get_callback'    => 'cfwprapi_get_test_field',
            'update_callback' => 'cfwprapi_update_test_field',
            'schema'          => null,
        )
    );
}
add_action( 'rest_api_init', 'cfwprapi_testing_func' );

/**
 * Get the value of the added field
 *
 * @since 1.0.0
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function cfwprapi_get_test_field( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name, true );
}

/**
 * Updating custom field data.
 *
 * @since 1.0.0
 *
 * @param mixed $value The value of the field
 * @param object $object The object from the response
 * @param string $field_name Name of field
 *
 * @return bool|int
 */
function cfwprapi_update_test_field( $value, $object, $field_name ) {
    if ( ! $value || ! is_string( $value ) ) {
        return;
    }

    return update_post_meta( $object->id, $field_name, strip_tags( $value ) );
}



/**
 * Register Custom Post Types News
 *
 * @since 1.0.0
 */
function cfwprapi_add_cpt_news() {
    register_post_type( 'news', array(
        'labels'              => array(
            'name'                => _x( 'News', 'Post Type General Name', 'cfwprapi' ),
            'singular_name'       => _x( 'News', 'Post Type Singular Name', 'cfwprapi' ),
            'menu_name'           => __( 'News', 'cfwprapi' ),
            'name_admin_bar'      => __( 'News', 'cfwprapi' ),
            'parent_item_colon'   => __( 'Parent News:', 'cfwprapi' ),
            'all_items'           => __( 'All News', 'cfwprapi' ),
            'add_new_item'        => __( 'Add New News', 'cfwprapi' ),
            'add_new'             => __( 'Add New', 'cfwprapi' ),
            'new_item'            => __( 'New News', 'cfwprapi' ),
            'edit_item'           => __( 'Edit News', 'cfwprapi' ),
            'update_item'         => __( 'Update News', 'cfwprapi' ),
            'view_item'           => __( 'View News', 'cfwprapi' ),
            'search_items'        => __( 'Search News', 'cfwprapi' ),
            'not_found'           => __( 'Not found', 'cfwprapi' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'cfwprapi' ),
        ),
        'supports'            => array(
            'title',
            'editor',
            'thumbnail',
            'author',
            'excerpt',
            'page-attributes',
            'revisions',
        ),
        'taxonomies'          => array( 'post_tag', 'category' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-media-text',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'has_archive' => 'news',
        'rewrite' => array(
            'with_front' => false,
            'slug' => 'news',
        ),
        'show_in_rest' => true,
        'rest_base' => 'news',
        'rest_controller_class' => 'WP_REST_Posts_Controller'
    ) );
}
add_action( 'init', 'cfwprapi_add_cpt_news', 0 );

/**
*  Add custom metabox for "news" custom post type.
*/
function cfwprapi_news_custom_fields() {

    $prefix = '_cfwprapi_';

    $cfwprapi_metabox = new_cmb2_box( array(
        'id'            => 'cfwprapi_news_details',
        'title'         => __( 'News Author Details', 'cfwprapi' ),
        'object_types'  => array( 'news' ),
        'context'       => 'normal',
        'priority'      => 'high',
        'show_names'    => true,

    ) );

    // Name
    $cfwprapi_metabox->add_field( array(
        'name' => __( 'Author Name', 'cfwprapi' ),
        'desc' => __( 'Input Author Name.', 'cfwprapi' ),
        'id'   => $prefix . 'author_name',
        'type' => 'text',
    ) );

    // Desc
    $cfwprapi_metabox->add_field( array(
        'name' => __( 'Author Description', 'cfwprapi' ),
        'desc' => __( 'Input Author Description.', 'cfwprapi' ),
        'id'   => $prefix . 'author_desc',
        'type' => 'wysiwyg',
    ) );

    // URL
    $cfwprapi_metabox->add_field( array(
        'name' => __( 'Author URL', 'cfwprapi' ),
        'desc' => __( 'Input Author URL.', 'cfwprapi' ),
        'id'   => $prefix . 'author_url',
        'type' => 'text_url',
    ) );

}
add_filter( 'cmb2_init', 'cfwprapi_news_custom_fields' );

/**
 * Creating custom routes and endpoints for news post-type.
 *
 * @since 1.0.0
 */
function cfwprapi_create_custom_routes_cpt() {

    register_rest_route( 'wp-rest-api-demo/v2', '/' . 'news', array(
        array(
            'methods'         => WP_REST_Server::READABLE,
            'callback'        => 'cfwprapi_get_news_items',
            'permission_callback' => 'get_items_permissions_check',
            'args'            => array(),
        ),
        array(
            'methods'         => WP_REST_Server::CREATABLE,
            'callback'        => 'create_news_item',
            'permission_callback' => 'create_item_permissions_check',
            //'args'            => get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
        ),
        'schema' => 'get_public_item_schema',
    ) );

    register_rest_route( 'wp-rest-api-demo/v2', '/' . 'news' . '/(?P<id>[\d]+)', array(
        array(
            'methods'         => WP_REST_Server::READABLE,
            'callback'        => 'cfwprapi_get_news_item',
            //'permission_callback' => 'get_item_permissions_check',
            'args'            => array(
                'context'          => array(
                        'default'      => 'view',
                 ),
            ),
        ),
        array(
            'methods'         => WP_REST_Server::EDITABLE,
            'callback'        => 'update_news_item',
            //'permission_callback' => 'update_item_permissions_check',
            //'args'            => get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
        ),
        array(
            'methods'  => WP_REST_Server::DELETABLE,
            'callback' => 'delete_news_item',
            //'permission_callback' => 'delete_item_permissions_check',
            'args'     => array(
                'force'    => array(
                    'default'      => false,
                ),
            ),
        ),
        'schema' => 'get_public_item_schema',
    ) );
}
add_action( 'rest_api_init', 'cfwprapi_create_custom_routes_cpt' );

/**
 * Get all news.
 *
 * @since 1.0.0
 */
function cfwprapi_get_news_items() {
    $posts = get_posts( array(
        'post_type' => 'news',
    ) );

    if ( empty( $posts ) ) {
        return new WP_Error( 'no_news_found', 'Invalid news', array( 'status' => 404 ) );
    }

    return $posts;
}

/**
 * Get single news.
 *
 * @since 1.0.0
 */
function cfwprapi_get_news_item( $data ) {
    $posts = get_post( $data['id'] );

    if ( empty( $posts ) ) {
        return new WP_Error( 'no_news_found', 'Invalid news', array( 'status' => 404 ) );
    }

    return $posts;
}


/**
 * Check if a given request has access to get items
 *
 * @param WP_REST_Request $request Full data about the request.
 * @return WP_Error|bool
 */
function get_items_permissions_check( $request ) {

    $post_type = get_post_type_object( 'news' );

    if ( 'edit' === $request['context'] && ! current_user_can( $post_type->cap->edit_posts ) ) {
        return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit these posts in this post type' ), array( 'status' => rest_authorization_required_code() ) );
    }

    return true;
}

/**
 * Check if a given request has access to create news post
 *
 * @param WP_REST_Request $request Full data about the request.
 * @return WP_Error|bool
 */
function create_item_permissions_check( $request ) {

    $post_type = get_post_type_object( 'news' );

    if ( ! empty( $request['password'] ) && ! current_user_can( $post_type->cap->publish_posts ) ) {
        return new WP_Error( 'rest_cannot_publish', __( 'Sorry, you are not allowed to create password protected posts in this post type' ), array( 'status' => rest_authorization_required_code() ) );
    }

    if ( ! empty( $request['author'] ) && get_current_user_id() !== $request['author'] && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
        return new WP_Error( 'rest_cannot_edit_others', __( 'You are not allowed to create posts as this user.' ), array( 'status' => rest_authorization_required_code() ) );
    }

    if ( ! empty( $request['sticky'] ) && ! current_user_can( $post_type->cap->edit_others_posts ) ) {
        return new WP_Error( 'rest_cannot_assign_sticky', __( 'You do not have permission to make posts sticky.' ), array( 'status' => rest_authorization_required_code() ) );
    }

    if ( ! current_user_can( $post_type->cap->create_posts ) ) {
        return new WP_Error( 'rest_cannot_create', __( 'Sorry, you are not allowed to create new posts.' ), array( 'status' => rest_authorization_required_code() ) );
    }
    return true;

}

function get_news_item( $request ) {
    $id = (int) $request['id'];
    $post = get_post( $id );
    $post_type = 'news';

    if ( empty( $id ) || empty( $post->ID ) || $post_type !== $post->post_type ) {
        return new WP_Error( 'rest_post_invalid_id', __( 'Invalid post id.' ), array( 'status' => 404 ) );
    }

    $data = prepare_item_for_response( $post, $request );
    $response = rest_ensure_response( $data );

    /*if ( is_post_type_viewable( get_post_type_object( $post->post_type ) ) ) {
        $response->link_header( 'alternate',  get_permalink( $id ), array( 'type' => 'text/html' ) );
    }

    return $response;*/
}







