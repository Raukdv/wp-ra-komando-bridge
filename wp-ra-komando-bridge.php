<?php
/**
 * Plugin Name: RA:Komando | Bridge | Schemas | Meta Tags
 * Description: Serves as a bridge to connect Komando with Site. 
 * Author: RA Marketing Consulting
 * Author URI: https://ramarketingconsulting.com
 * Version: 1.6
 * Plugin URI: https://ramarketingconsulting.com
 */

function json_basic_auth_handler( $user ) {
	global $wp_json_basic_auth_error;

	$wp_json_basic_auth_error = null;

	// Don't authenticate twice
	if ( ! empty( $user ) ) {
		return $user;
	}

	// Check that we're trying to authenticate
	if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
		return $user;
	}

	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];

	/**
	 * In multi-site, wp_authenticate_spam_check filter is run on authentication. This filter calls
	 * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
	 * recursion and a stack overflow unless the current function is removed from the determine_current_user
	 * filter during authentication.
	 */
	remove_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );

	$user = wp_authenticate( $username, $password );

	add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );

	if ( is_wp_error( $user ) ) {
		$wp_json_basic_auth_error = $user;
		return null;
	}

	$wp_json_basic_auth_error = true;

	return $user->ID;
}
add_filter( 'determine_current_user', 'json_basic_auth_handler', 20 );

function json_basic_auth_error( $error ) {
	// Passthrough other errors
	if ( ! empty( $error ) ) {
		return $error;
	}

	global $wp_json_basic_auth_error;

	return $wp_json_basic_auth_error;
}
add_filter( 'rest_authentication_errors', 'json_basic_auth_error' );

//Hardcode para komando
function post_or_page_schema() {  

$post = get_post( get_the_ID() );
$url_post = isset( $post->guid ) ? $post->guid : '';
$title_post = isset( $post->post_title ) ? $post->post_title : '';
$author_post = isset( $post->post_author ) ? $post->post_author : '';
$published_post = isset( $post->post_date ) ? $post->post_date : '';
$modified_post = isset( $post->post_modified ) ? $post->post_modified : '';
$featured_image_post = get_the_post_thumbnail_url(get_the_ID(),'full');
$author_name = get_the_author_meta('user_nicename', $author_post);

// $post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
// $featured_image_post = wp_get_attachment_image_url($post_thumbnail_id, 'full');
$meta_schema = get_post_meta( get_the_ID(), 'seo_schema', true);
if (empty($meta_schema)) {
//getting data arrays
echo <<<EXCERPT
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "$title_post",
  "image": "$featured_image_post",
  "mainEntityOfPage":{
  "@type": "WebPage",
  "@id": "$url_post"
  },
  "author": {
    "@type": "Person",
    "name": "$author_name"
  },  
  "publisher": {
    "@type": "Organization",
    "name": "$author_name",
    "logo": {
      "@type": "ImageObject",
      "url": "$featured_image_post"
    }
  },
  "datePublished": "$published_post",
  "dateModified":"$modified_post"
}
</script>

EXCERPT;

} else {
echo <<<EXCERPT
$meta_schema
EXCERPT;
}

}
add_action( 'wp_head', 'post_or_page_schema', 100);

//meta tags
function meta_tags_post_or_pages() {
//Con esto me permitira saber y validar entre post y page
$post_type = get_post_type( get_the_ID());

$meta_tags = get_post_meta( get_the_ID(), 'seo_meta_tag', true);
$tags = json_decode($meta_tags, true );
$meta_title = $tags['meta_title'];

$meta_description = $tags['meta_description'];
//echo "<pre>"; var_dump($tags); echo "</pre>";
echo <<<EXCERPT
<meta name="title" content="$meta_title"/>
<meta name="description" content="$meta_description"/>
EXCERPT;
}

add_action( 'wp_head', 'meta_tags_post_or_pages', 100);

#custom title para meta title
function change_to_custom_title($title) {
	$title_tag = get_post_meta( get_the_ID(), 'seo_meta_tag', true);
	$titletag = json_decode($title_tag, true );
	$metatitle = $titletag['meta_title'];
    if (is_page()) {
        return $metatitle;
    }
    return $title;
}
add_filter('pre_get_document_title', 'change_to_custom_title', 100);

// Campo adicional en api post
function post_meta_data() {
	register_rest_field( 'post', 
						  'seo_schema', 
						  array(
						  	'get_callback' => 'callback_leer_post_meta',
						  	'update_callback' => 'rest_update_post_meta',
							)
						);
}

add_action('rest_api_init', 'post_meta_data');

function callback_leer_post_meta( $object ) {
	$post_id = $object['id'];
	return get_post_meta( $post_id );
}

function rest_update_post_meta($value, $post, $field_name) {
  // Perform Validation of input
  if (!$value || !is_string($value)) {
    return;
  }
  // Update the field
  return update_post_meta($post->ID, $field_name, $value);
}

$args = array(
    'type'=>'string',
    'single'=>true,
    'show_in_rest'=>true
);

register_post_meta('post', 'seo_schema', $args);
// Final

//Para las pages
function page_meta_data() {
	register_rest_field( 'page', 
						  'seo_schema', 
						  array(
						  	'get_callback' => 'callback_leer_page_meta',
						  	'update_callback' => 'rest_update_page_meta',
							)
						);
}

add_action('rest_api_init', 'page_meta_data');

function callback_leer_page_meta( $object ) {
	$page_id = $object['id'];
	return get_post_meta( $page_id );
}

function rest_update_page_meta($value, $page, $field_name) {
  // Perform Validation of input
  if (!$value || !is_string($value)) {
    return;
  }
  // Update the field
  return update_post_meta($page->ID, $field_name, $value);
}

$args = array(
    'type'=>'string',
    'single'=>true,
    'show_in_rest'=>true
);

register_post_meta('page', 'seo_schema', $args);
//Final


//Campos para meta tags como custom fields - post
function post_meta_tag() {
	register_rest_field( 'post', 
						  'seo_meta_tag', 
						  array(
						  	'get_callback' => 'callback_leer_post_meta_tag',
						  	'update_callback' => 'rest_update_post_meta_tag',
							)
						);
}

add_action('rest_api_init', 'post_meta_tag');

function callback_leer_post_meta_tag( $object ) {
	$post_id = $object['id'];
	return get_post_meta( $post_id );
}

function rest_update_post_meta_tag($value, $post, $field_name) {
  // Perform Validation of input
  if (!$value || !is_string($value)) {
    return;
  }
  // Update the field
  return update_post_meta($post->ID, $field_name, $value);
}

$args = array(
    'type'=>'string',
    'single'=>true,
    'show_in_rest'=>true
);

register_post_meta('post', 'seo_meta_tag', $args);

//Para pages
function page_meta_tag() {
	register_rest_field( 'page', 
						  'seo_meta_tag', 
						  array(
						  	'get_callback' => 'callback_leer_page_meta_tag',
						  	'update_callback' => 'rest_update_page_meta_tag',
							)
						);
}

add_action('rest_api_init', 'page_meta_tag');

function callback_leer_page_meta_tag( $object ) {
	$page_id = $object['id'];
	return get_post_meta( $page_id );
}

function rest_update_page_meta_tag($value, $page, $field_name) {
  // Perform Validation of input
  if (!$value || !is_string($value)) {
    return;
  }
  // Update the field
  return update_post_meta($page->ID, $field_name, $value);
}

$args = array(
    'type'=>'string',
    'single'=>true,
    'show_in_rest'=>true
);

register_post_meta('page', 'seo_meta_tag', $args);

#Eliminar hentry schema
function themes_remove_hentry( $classes ) {
	if ( is_page() ) {
		$classes = array_diff( $classes, array( 'hentry' ) );
  	}
	return $classes;
}
add_filter( 'post_class','themes_remove_hentry' );

#Editor Content
function dawn_editor_filter( $content ) {
    return html_entity_decode( $content );
}
add_filter( 'the_editor_content', 'dawn_editor_filter', 100);

function update_data_func( $data ) {
	$my_post = array(
  	'ID' => $data['id'],
  	'post_content' => $data['content'],
  );
	wp_update_post( $my_post );
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'komando/v1', '/post/(?P<id>\d+)', array(
    'methods' => 'POST',
    'callback' => 'update_data_func',
    'args' => array(
      'id' => array(
        'validate_callback' => function($param, $request, $key) {
          return is_numeric( $param );
        }
      ),
    ),
  ) );
} );
