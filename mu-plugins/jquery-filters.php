<?php
/* Plugin Name: jQuery Filters
 * Description: Default filters, option values, and other tweaks.
 */

$live_domain = $_SERVER['HTTP_HOST'];
if ( JQUERY_STAGING )
        $live_domain = str_replace( JQUERY_STAGING_PREFIX, '', $live_domain );
$options = jquery_default_site_options();
$domains = jquery_domains();
$live_domain = str_replace( JQUERY_STAGING_PREFIX, '', $_SERVER['HTTP_HOST'] );
$options = array_merge( $options, $domains[ $live_domain ]['options'] );
foreach ( $options as $option => $value ) {
	if ( 'stylesheet' === $option || 'template' === $option )
		continue; // Don't mess with themes for now.
	add_filter( 'pre_option_' . $option, function( $null ) use ( $value ) {
		return $value;
	} );
}
unset( $domains, $live_domain, $options, $option, $value );

// Disable WordPress auto-paragraphing for posts.
remove_filter( 'the_content', 'wpautop' );

// Disable WordPress text transformations (smart quotes, etc.) for posts.
remove_filter( 'the_content', 'wptexturize' );

// Disable more restrictive multisite upload settings.
remove_filter( 'upload_mimes', 'check_upload_mimes' );
// Give unfiltered upload ability to super admins.
define( 'ALLOW_UNFILTERED_UPLOADS', true );

// Allow full HTML in term descriptions.
add_action( 'init', 'jquery_unfiltered_html_for_term_descriptions' );
add_action( 'set_current_user', 'jquery_unfiltered_html_for_term_descriptions' );
function jquery_unfiltered_html_for_term_descriptions() {
	remove_filter( 'pre_term_description', 'wp_filter_kses' );
	remove_filter( 'pre_term_description', 'wp_filter_post_kses' );
	if ( ! current_user_can( 'unfiltered_html' ) )
		add_filter( 'pre_term_description', 'wp_filter_post_kses' );
}