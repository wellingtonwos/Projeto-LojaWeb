<?php
/**
 * Let selected auto-applied Power Coupons be removed and stay dismissed for the current session.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the list of auto-applied coupon codes that customers may dismiss manually.
 *
 * @return string[]
 */
function lojaweb_get_removable_auto_coupon_codes() {
	$coupons = array(
		'welcome15',
	);

	$coupons = array_map( 'wc_format_coupon_code', $coupons );
	$coupons = array_filter( array_unique( $coupons ) );

	return apply_filters( 'lojaweb_removable_auto_coupon_codes', $coupons );
}

/**
 * Get the session key used to remember dismissed auto-applied coupons.
 *
 * @return string
 */
function lojaweb_get_dismissed_auto_coupon_session_key() {
	return 'lojaweb_dismissed_auto_coupons';
}

/**
 * Read the dismissed coupon codes from the WooCommerce session.
 *
 * @return string[]
 */
function lojaweb_get_dismissed_auto_coupon_codes() {
	if ( ! function_exists( 'WC' ) || ! WC()->session ) {
		return array();
	}

	$dismissed = WC()->session->get( lojaweb_get_dismissed_auto_coupon_session_key(), array() );

	if ( ! is_array( $dismissed ) ) {
		return array();
	}

	return array_map( 'wc_format_coupon_code', $dismissed );
}

/**
 * Persist the dismissed coupon code list into the WooCommerce session.
 *
 * @param string[] $codes Coupon codes.
 * @return void
 */
function lojaweb_set_dismissed_auto_coupon_codes( $codes ) {
	if ( ! function_exists( 'WC' ) || ! WC()->session ) {
		return;
	}

	$codes = array_map( 'wc_format_coupon_code', (array) $codes );
	$codes = array_values( array_filter( array_unique( $codes ) ) );

	WC()->session->set( lojaweb_get_dismissed_auto_coupon_session_key(), $codes );
}

/**
 * Add a coupon code to the dismissed list.
 *
 * @param string $coupon_code Coupon code.
 * @return void
 */
function lojaweb_mark_auto_coupon_as_dismissed( $coupon_code ) {
	$coupon_code = wc_format_coupon_code( $coupon_code );

	if ( ! $coupon_code ) {
		return;
	}

	$dismissed   = lojaweb_get_dismissed_auto_coupon_codes();
	$dismissed[] = $coupon_code;

	lojaweb_set_dismissed_auto_coupon_codes( $dismissed );
}

/**
 * Remove a coupon code from the dismissed list.
 *
 * @param string $coupon_code Coupon code.
 * @return void
 */
function lojaweb_unmark_auto_coupon_as_dismissed( $coupon_code ) {
	$coupon_code = wc_format_coupon_code( $coupon_code );
	$dismissed   = array_diff( lojaweb_get_dismissed_auto_coupon_codes(), array( $coupon_code ) );

	lojaweb_set_dismissed_auto_coupon_codes( $dismissed );
}

/**
 * Check whether the coupon is auto-applied by Power Coupons and allowed to be removed.
 *
 * @param string $coupon_code Coupon code.
 * @return bool
 */
function lojaweb_is_removable_auto_coupon( $coupon_code ) {
	$coupon_code = wc_format_coupon_code( $coupon_code );

	if ( ! $coupon_code || ! in_array( $coupon_code, lojaweb_get_removable_auto_coupon_codes(), true ) ) {
		return false;
	}

	$coupon = new WC_Coupon( $coupon_code );

	return $coupon->get_id() && 'yes' === get_post_meta( $coupon->get_id(), '_power_coupon_auto_apply', true );
}

/**
 * Get all Power Coupons auto-applied coupon codes.
 *
 * @return string[]
 */
function lojaweb_get_power_coupons_auto_coupon_codes() {
	static $coupon_codes = null;

	if ( null !== $coupon_codes ) {
		return $coupon_codes;
	}

	$args = array(
		'post_type'      => 'shop_coupon',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'meta_query'     => array(
			array(
				'key'   => '_power_coupon_auto_apply',
				'value' => 'yes',
			),
		),
	);

	$query        = new WP_Query( $args );
	$coupon_codes = array();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$coupon         = new WC_Coupon( get_the_ID() );
			$coupon_codes[] = wc_format_coupon_code( $coupon->get_code() );
		}
		wp_reset_postdata();
	}

	return array_filter( array_unique( $coupon_codes ) );
}

/**
 * Respect Power Coupons guest visibility setting before auto-applying coupons.
 *
 * @return bool
 */
function lojaweb_can_auto_apply_power_coupons_for_current_visitor() {
	if ( is_user_logged_in() ) {
		return true;
	}

	if ( ! class_exists( '\Power_Coupons\Includes\Power_Coupons_Settings_Helper' ) ) {
		return false;
	}

	return \Power_Coupons\Includes\Power_Coupons_Settings_Helper::get_instance()->enable_for_guests();
}

/**
 * Determine whether the coupon may be auto-applied for the current cart state.
 *
 * @param string $coupon_code Coupon code.
 * @return bool
 */
function lojaweb_can_auto_apply_coupon( $coupon_code ) {
	$coupon = new WC_Coupon( $coupon_code );

	if ( ! $coupon->get_id() || ! $coupon->is_valid() ) {
		return false;
	}

	$start_date = get_post_meta( $coupon->get_id(), '_power_coupon_start_date', true );
	if ( ! empty( $start_date ) ) {
		$start_timestamp = strtotime( $start_date . ' 00:00:00' );
		if ( $start_timestamp && time() < $start_timestamp ) {
			return false;
		}
	}

	$cart = WC()->cart;
	if ( ! $cart instanceof WC_Cart ) {
		return false;
	}

	$minimum_amount = $coupon->get_minimum_amount();
	if ( $minimum_amount > 0 && $cart->get_subtotal() < $minimum_amount ) {
		return false;
	}

	$maximum_amount = $coupon->get_maximum_amount();
	if ( $maximum_amount > 0 && $cart->get_subtotal() > $maximum_amount ) {
		return false;
	}

	return true;
}

/**
 * Auto-apply Power Coupons coupons, but skip those dismissed by the customer in this session.
 *
 * @return void
 */
function lojaweb_auto_apply_power_coupons_with_dismissal_support() {
	static $already_applied = false;

	if ( $already_applied || ! lojaweb_can_auto_apply_power_coupons_for_current_visitor() ) {
		return;
	}

	$cart = WC()->cart;
	if ( ! $cart instanceof WC_Cart || $cart->is_empty() ) {
		return;
	}

	$already_applied = true;
	$dismissed       = lojaweb_get_dismissed_auto_coupon_codes();

	foreach ( lojaweb_get_power_coupons_auto_coupon_codes() as $coupon_code ) {
		if ( in_array( $coupon_code, $dismissed, true ) || $cart->has_discount( $coupon_code ) ) {
			continue;
		}

		if ( lojaweb_can_auto_apply_coupon( $coupon_code ) ) {
			$cart->apply_coupon( $coupon_code );
		}
	}
}

/**
 * Keep the remove link visible for coupons that customers are allowed to dismiss.
 *
 * @return void
 */
function lojaweb_allow_remove_links_for_selected_auto_coupons() {
	$selectors = array();

	foreach ( lojaweb_get_removable_auto_coupon_codes() as $coupon_code ) {
		$safe_code = preg_replace( '/[^a-zA-Z0-9_\-]/', '', $coupon_code );

		if ( ! $safe_code ) {
			continue;
		}

		$selectors[] = sprintf(
			'.woocommerce-remove-coupon[data-coupon="%1$s"], button.wc-block-components-chip__remove[aria-label*="%1$s"]',
			$safe_code
		);
	}

	if ( empty( $selectors ) ) {
		return;
	}

	wp_add_inline_style(
		'power-coupons-public',
		implode( ', ', $selectors ) . '{ display: inline !important; visibility: visible !important; }'
	);
}

/**
 * Intercept Power Coupons AJAX removal for selected auto-applied coupons.
 *
 * @return void
 */
function lojaweb_handle_removable_auto_coupon_ajax() {
	if ( ! check_ajax_referer( 'power-coupons-nonce', 'nonce', false ) ) {
		return;
	}

	$coupon_code = isset( $_POST['coupon_code'] ) ? wc_format_coupon_code( wp_unslash( $_POST['coupon_code'] ) ) : '';

	if ( ! lojaweb_is_removable_auto_coupon( $coupon_code ) ) {
		return;
	}

	$cart = WC()->cart;
	if ( ! $cart instanceof WC_Cart ) {
		wp_send_json_error( array( 'message' => __( 'Cart not found.', 'woocommerce' ) ) );
	}

	lojaweb_mark_auto_coupon_as_dismissed( $coupon_code );
	$result = $cart->remove_coupon( $coupon_code );

	if ( $result ) {
		$cart->calculate_totals();
		wp_send_json_success( array( 'message' => __( 'Coupon removed successfully.', 'woocommerce' ) ) );
	}

	wp_send_json_error( array( 'message' => __( 'Failed to remove coupon.', 'woocommerce' ) ) );
}

/**
 * Track coupons removed through the regular WooCommerce flow so they stay dismissed.
 *
 * @param string $coupon_code Coupon code.
 * @return void
 */
function lojaweb_track_removed_auto_coupon( $coupon_code ) {
	if ( lojaweb_is_removable_auto_coupon( $coupon_code ) ) {
		lojaweb_mark_auto_coupon_as_dismissed( $coupon_code );
	}
}
add_action( 'woocommerce_removed_coupon', 'lojaweb_track_removed_auto_coupon' );

/**
 * If the customer adds the coupon again manually, allow it to auto-apply later in the session.
 *
 * @param string $coupon_code Coupon code.
 * @return void
 */
function lojaweb_clear_dismissed_auto_coupon_when_reapplied( $coupon_code ) {
	if ( lojaweb_is_removable_auto_coupon( $coupon_code ) ) {
		lojaweb_unmark_auto_coupon_as_dismissed( $coupon_code );
	}
}
add_action( 'woocommerce_applied_coupon', 'lojaweb_clear_dismissed_auto_coupon_when_reapplied' );

/**
 * Replace the Power Coupons auto-apply behavior with a session-aware version.
 *
 * @return void
 */
function lojaweb_replace_power_coupons_auto_apply_behavior() {
	if ( ! class_exists( '\Power_Coupons\Controllers\Auto_Apply_Controller' ) ) {
		return;
	}

	$controller = \Power_Coupons\Controllers\Auto_Apply_Controller::get_instance();

	remove_action( 'woocommerce_cart_loaded_from_session', array( $controller, 'auto_apply_coupons' ) );
	remove_action( 'woocommerce_after_calculate_totals', array( $controller, 'auto_apply_coupons' ), 1000 );
	remove_action( 'woocommerce_check_cart_items', array( $controller, 'auto_apply_coupons' ), 0 );
	remove_action( 'wp_enqueue_scripts', array( $controller, 'hide_auto_applied_coupons_wc_remove_btn' ), 20 );

	add_action( 'woocommerce_cart_loaded_from_session', 'lojaweb_auto_apply_power_coupons_with_dismissal_support' );
	add_action( 'woocommerce_after_calculate_totals', 'lojaweb_auto_apply_power_coupons_with_dismissal_support', 1000 );
	add_action( 'woocommerce_check_cart_items', 'lojaweb_auto_apply_power_coupons_with_dismissal_support', 0 );
	add_action( 'wp_enqueue_scripts', 'lojaweb_allow_remove_links_for_selected_auto_coupons', 21 );
}
add_action( 'plugins_loaded', 'lojaweb_replace_power_coupons_auto_apply_behavior', 30 );

add_action( 'wp_ajax_power_coupons_remove_coupon', 'lojaweb_handle_removable_auto_coupon_ajax', 0 );
add_action( 'wp_ajax_nopriv_power_coupons_remove_coupon', 'lojaweb_handle_removable_auto_coupon_ajax', 0 );
