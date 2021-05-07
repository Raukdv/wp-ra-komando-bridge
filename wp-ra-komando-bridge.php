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
if ($schema_type == "local_business") {
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
$areasserved_2 = implode(", ", $areasserved);

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
"areaServed":["$areasserved_2"],
"description":"$description",
"paymentAccepted":"$paymentaccepted",
"sameAs":["$sameas"],
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
"address":{
"@type": "PostalAddress",
"addressLocality": "$addresslocality",
"addressRegion": "$addressregion",
"postalCode":"$postalcode",
"streetAddress": "$streetaddress"
},
"contactPoint":{
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

} elseif ($schema_type == "service_schema") {
//getting data arrays
$service_type = $schema["@type"];
$servicetype = $schema["servicetype"];
$service_areaserved = $schema["Areasserved"];
$service_areaserved_2 = implode(", ", $service_areaserved);
$audience = $schema["audience"];
$description = $schema["description"];
$name = $schema["name"];
$url = $schema["url"];
$service_id = $schema["@id"];
$provider_type= $schema["provider_type"];
$provider_id= $schema["provider_id"];
$provider_name= $schema["provider_name"];
$provider_additionaltype= $schema["provider_additionaltype"]; 
$provider_altername= $schema["provider_altername"];
$provider_disambiguatingdescription= $schema["provider_disambiguatingdescription"];
$provider_mainentityofpage= $schema["provider_mainentityofpage"];
$provider_paymentaccepted= $schema["provider_paymentaccepted"];
$provider_sameas= $schema["provider_sameas"];
$provider_image= $schema["provider_image"];
$provider_pricerange= $schema["provider_pricerange"];
$provider_hasmap= $schema["provider_hasmap"];
$provider_email= $schema["provider_email"];
$provider_telephone= $schema["provider_telephone"];
$provider_aggrating_type= $schema["provider_aggrating_type"];
$provider_aggrating_ratingvalue= $schema["provider_aggrating_ratingvalue"];
$provider_aggrating_reviewcount= $schema["provider_aggrating_reviewcount"];
$provider_address_type= $schema["provider_address_type"];
$provider_address_addresslocality= $schema["provider_address_addresslocality"];
$provider_address_addressregion= $schema["provider_address_addressregion"];
$provider_address_postalcode= $schema["provider_address_postalcode"];
$provider_address_streetaddress= $schema["provider_address_streetaddress"];
$provider_contactpointp_type= $schema["provider_contactpointp_type"];
$provider_contactpointp_contacttype= $schema["provider_contactpointp_contacttype"];
$provider_contactpointp_telephone= $schema["provider_contactpointp_telephone"];
$provider_contactpointp_email= $schema["provider_contactpointp_email"];
$provider_geo_type= $schema["provider_geo_type"];
$provider_geo_latitude= $schema["provider_geo_latitude"];
$provider_geo_longitude= $schema["provider_geo_longitude"];
$availablechannel_type= $schema["availablechannel_type"];
$availablechannel_id= $schema["availablechannel_id"];
$availablechannel_url= $schema["availablechannel_url"];
$availablechannel_location= $schema["availablechannel_location"];
$availablechannel_name= $schema["availablechannel_name"];
$availablechannel_description= $schema["availablechannel_description"];
$availablechannel_servicephone_type= $schema["availablechannel_servicephone_type"];
$availablechannel_servicephone_id= $schema["availablechannel_servicephone_id"];
$availablechannel_servicephone_telephone= $schema["availablechannel_servicephone_telephone"];
$availablechannel_servicephone_name= $schema["availablechannel_servicephone_name"];
$availablechannel_servicephone_description= $schema["availablechannel_servicephone_description"];
$availablechannel_servicephone_contactype= $schema["availablechannel_servicephone_contactype"];
$availablechannel_servicephone_availablelanguage= $schema["availablechannel_servicephone_availablelanguage"];
$image_type= $schema["image_type"];
$image_id= $schema["image_id"];
$image_url= $schema["image_url"];
$image_width= $schema["image_width"];
$image_height= $schema["image_height"];
$serviceoutput_type= $schema["serviceoutput_type"];
$serviceoutput_id= $schema["serviceoutput_id"];
$serviceoutput_name= $schema["serviceoutput_name"];
$serviceoutput_description= $schema["serviceoutput_description"];
$serviceoutput_brand= $schema["serviceoutput_brand"];
$serviceoutput_sku= $schema["serviceoutput_sku"];
$serviceoutput_gtin8= $schema["serviceoutput_gtin8"];
$serviceoutput_image= $schema["serviceoutput_image"];
$serviceoutput_offer_type= $schema["serviceoutput_offer_type"];
$serviceoutput_offer_availability= $schema["serviceoutput_offer_availability"];
$serviceoutput_offer_pricevaliduntil= $schema["serviceoutput_offer_pricevaliduntil"];
$serviceoutput_offer_name= $schema["serviceoutput_offer_name"];
$serviceoutput_offer_price= $schema["serviceoutput_offer_price"];
$serviceoutput_offer_pricecurrency= $schema["serviceoutput_offer_pricecurrency"];
$serviceoutput_offer_url= $schema["serviceoutput_offer_url"];
$serviceoutput_aggrating_type= $schema["serviceoutput_aggrating_type"];
$serviceoutput_aggrating_ratingvalue= $schema["serviceoutput_aggrating_ratingvalue"];
$serviceoutput_aggrating_reviewcount= $schema["serviceoutput_aggrating_reviewcount"];
$review_type= $schema["review_type"];
$review_mainentity= $schema["review_mainentity"];
$review_provider= $schema["review_provider"];
$review_reviewbody= $schema["review_reviewbody"];
$review_author_type= $schema["review_author_type"];
$review_author_name= $schema["review_author_name"];
$review_author_sameas= $schema["review_author_sameas"];
$review_datepublished= $schema["review_datepublished"];
$review_reviewrating_type= $schema["review_reviewrating_type"];
$review_reviewrating_bestrating= $schema["review_reviewrating_bestrating"];
$review_reviewrating_ratingvalue= $schema["review_reviewrating_ratingvalue"];

echo <<<EXCERPT
<script type="application/ld+json">
{
"@context": "https://schema.org",
"@type": "$service_type",
"serviceType": "$servicetype",
"areaServed":["$service_areaserved_2"],
"audience": "$audience",
"provider":{
"@type": "$provider_type",
"@id": "$provider_id",
"name": "$provider_name",
"additionalType":["$provider_additionaltype"],
"alternatename":"$provider_altername",
"disambiguatingdescription":"$provider_disambiguatingdescription",
"mainEntityOfPage":"$provider_mainentityofpage",
"paymentAccepted":"$provider_paymentaccepted", 
"sameAs":["$provider_sameas"], 
"image":"$provider_image", 
"priceRange":"$provider_pricerange", 
"hasMap":"$provider_hasmap", 
"email":"$provider_email", 
"telephone":"$provider_telephone", 
"aggregateRating":{ 
"@type":"$provider_aggrating_type", 
"ratingValue":"$provider_aggrating_ratingvalue", 
"reviewCount":"$provider_aggrating_reviewcount"
}, 
"address":{ 
"@type": "$provider_address_type", 
"addressLocality":"$provider_address_addresslocality", 
"addressRegion":"$provider_address_addressregion", 
"postalCode":"$provider_address_postalcode", 
"streetAddress":"$provider_address_streetaddress" 
}, 
"contactPoint":{ 
"@type":"$provider_contactpointp_type", 
"contactType":"$provider_contactpointp_contacttype", 
"telephone":"$provider_contactpointp_telephone", 
"email":"$provider_contactpointp_email" 
}, 
"geo":{ 
"@type":"$provider_geo_type", 
"latitude":"$provider_geo_latitude", 
"longitude":"$provider_geo_longitude" 
} 
},
"availableChannel": { 
"@type": "$availablechannel_type",
"@id": "$availablechannel_id",
"name": "$availablechannel_name",
"description": "$availablechannel_description",
"serviceUrl": "$availablechannel_url",
"servicePhone":{
"@type": "$availablechannel_servicephone_type",
"@id": "$availablechannel_servicephone_id",
"telephone": "$availablechannel_servicephone_telephone",
"name": "$availablechannel_servicephone_name",
"description": "$availablechannel_servicephone_description",
"contactType": "$availablechannel_servicephone_contactype",			
"availableLanguage": "$availablechannel_servicephone_availablelanguage"
},
"serviceLocation":"$availablechannel_location"
},
"description": "$description",
"image":["$image_url",
{
"@type": "$image_type",
"@id": "$image_id",
"url": "$image_url",
"width": "$image_width",
"height": "$image_height"	
}
],
"name": "$name",
"serviceOutput": {
"@type": "$serviceoutput_type",
"name": "$serviceoutput_name",
"description":"$serviceoutput_description",
"brand":"$serviceoutput_brand",
"gtin8":"$serviceoutput_gtin8",
"sku":"$serviceoutput_sku",
"image":"$serviceoutput_image",
"@id": "$serviceoutput_id",
"offers" :{
"@type" : "$serviceoutput_offer_type",
"availability" :"$serviceoutput_offer_availability",
"priceValidUntil":"$serviceoutput_offer_pricevaliduntil",
"name" :"$serviceoutput_offer_name",
"price" :"$serviceoutput_offer_price",
"priceCurrency" :"$serviceoutput_offer_pricecurrency",
"url" :"$serviceoutput_offer_url"
},
"aggregateRating":{
"@type":"$serviceoutput_aggrating_type",
"ratingValue":"$serviceoutput_aggrating_ratingvalue",
"reviewCount":"$serviceoutput_aggrating_reviewcount"
},
"review":{
"@type" : "$review_type",
"mainEntity":"$review_mainentity",
"provider":"$review_provider",
"author":{
"@type" : "$review_author_type",
"name" : "$review_author_name"
},
"datePublished":"$review_datepublished",
"reviewRating": {
"@type" : "$review_reviewrating_type",
"bestRating": "$review_reviewrating_bestrating",
"ratingValue" : "$review_reviewrating_ratingvalue"
},
"reviewBody" : "$review_reviewbody"
}
},
"url": "$url",
"@id": "$service_id"
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
