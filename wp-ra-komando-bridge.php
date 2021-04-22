<?php
/**
 * Plugin Name: RA:Komando | Bridge
 * Description: Serves as a bridge to connect Komando with Site. 
 * Author: RA Marketing Consulting
 * Author URI: https://ramarketingconsulting.com
 * Version: 1.5
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
$schema = json_decode( $meta_schema, true );
//echo "<pre>"; var_dump($schema); echo "</pre>";

$schema_type = $schema['schema_type'];
if ($schema_type == "local_business"){

//getting data arrays
$type = $schema['@type'];
$id = $schema['@id'];
$additionaltype = $schema['additionalType'];
$url = $schema['url'];
$name = $schema['name'];
$altername = $schema['altername'];
$disambiguatingdescription = $schema['disambiguatingdescription'];
$mainentityofpage = $schema['mainEntityOfPage'];
$areasserved = $schema['Areasserved'];
$description = $schema['description'];

$paymentaccepted = $schema['paymentAccepted'];
$sameas = $schema['sameAs'];
$image = $schema['image'];
$pricerange = $schema['priceRange'];
$hasmap = $schema['hasMap'];
$email = $schema['email'];
$telephone = $schema['telephone'];
$ratingvalue = $schema['ratingValue'];
$reviewcount = $schema['reviewCount'];
$foundingdate = $schema['foundingDate'];

$founders = $schema['founders'];
$addresslocality = $schema['addressLocality'];
$addressregion = $schema['addressRegion'];
$postalcode = $schema['postalCode'];
$streetaddress = $schema['streetAddress'];
$contacttype = $schema['contactType'];
$another_telephone = $schema['another_telephone'];
$another_email = $schema['another_email'];
$latitude = $schema['latitude'];
$longitude = $schema['longitude'];

echo <<<EXCERPT
<script type="application/ld+json">
{
"@context":"https://schema.org",
"@type":"$type",
"@id":"$id",
"additionalType":["$additionaltype"],
"url":"$url",
"name":"$name",
"alternatename":"$altername",
"disambiguatingdescription":"$disambiguatingdescription",
"mainEntityOfPage":"$mainentityofpage",
"areaServed":{"$areasserved"},
"description":"$description",
"paymentAccepted":"$paymentaccepted",
"sameAs":[$sameas],
"image":"$image",
"priceRange":"$pricerange",
"hasMap":"$hasmap",
"email":"$email",
"telephone":"$telephone",
"aggregateRating":
{"@type":"AggregateRating",
"ratingValue":"$ratingvalue",
"reviewCount":"$reviewcount"
},
"foundingDate": "$foundingdate",
"founders": [
{
"@type": "Person",
"name": "$founders"
} 
],
"address":
{
"@type": "PostalAddress",
"addressLocality": "$addresslocality",
"addressRegion": "$addressregion",
"postalCode":"$postalcode",
"streetAddress": "$streetaddress"
},
"contactPoint": {
"@type": "ContactPoint",
"contactType": "$contacttype",
"telephone": "$another_telephone",
"email": "$another_email"
},
"geo":{
"@type":"GeoCoordinates",
"latitude":"$latitude",
"longitude":"$longitude"}
}
</script>
EXCERPT;

} else {
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
  "image": "$featured_image_post",  
  "author": {
    "@type": "Person",
    "name": "$author_name"
  },  
  "publisher": {
    "@type": "Organization",
    "name": "$author_name"
  },
  "datePublished": "$published_post",
  "dateModified": "$modified_post",
  "Dato de prueba":$schema_type,
}
</script>
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
