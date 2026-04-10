<?php
/**
 * Local-only performance tweaks for the development environment.
 */

if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) || 'local' !== WP_ENVIRONMENT_TYPE ) {
	return;
}

/**
 * Remove features that add extra requests or processing but do not help local development.
 */
function lojaweb_disable_frontend_bloat() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );

	if ( ! is_admin() ) {
		wp_deregister_script( 'wp-embed' );
	}
}
add_action( 'init', 'lojaweb_disable_frontend_bloat' );

/**
 * Reduce heartbeat polling to make the admin feel lighter on slower local machines.
 */
function lojaweb_limit_heartbeat( $settings ) {
	$settings['interval'] = 60;

	return $settings;
}
add_filter( 'heartbeat_settings', 'lojaweb_limit_heartbeat' );

/**
 * Avoid loading dashicons for anonymous visitors.
 */
function lojaweb_maybe_dequeue_dashicons() {
	if ( ! is_user_logged_in() ) {
		wp_deregister_style( 'dashicons' );
	}
}
add_action( 'wp_enqueue_scripts', 'lojaweb_maybe_dequeue_dashicons', 100 );
