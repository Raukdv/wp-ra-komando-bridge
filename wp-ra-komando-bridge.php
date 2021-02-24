<?php
/**
 * Plugin Name: RA:Komando | Bridge
 * Description: Serves as a bridge to connect Komando with Site. 
 * Author: RA Marketing Consulting
 * Author URI: https://ramarketingconsulting.com
 * Version: 1
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


function articlepage_schema() {  

$post = get_post( get_the_ID() );
$url_post = isset( $post->guid ) ? $post->guid : '';
$title_post = isset( $post->post_title ) ? $post->post_title : '';
$author_post = isset( $post->post_author ) ? $post->post_author : '';
$featured_image_post = "";
$published_post = isset( $post->post_date ) ? $post->post_date : '';
$modified_post = isset( $post->post_modified ) ? $post->post_modified : '';


echo <<<EXCERPT
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "$url_post"
  },
  "headline": "$title_post",
  "image": "https://image.com",  
  "author": {
    "@type": "Person",
    "name": "$author_post"
  },  
  "publisher": {
    "@type": "Organization",
    "name": "$author_post"
  },
  "datePublished": "$published_post",
  "dateModified": "$modified_post"
}
</script>
EXCERPT;



}
add_action( 'wp_head', 'articlepage_schema',100 );


