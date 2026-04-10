<?php
add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );
function enqueue_parent_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_uri(),
        array( 'parent-style' ),
        wp_get_theme()->get( 'Version' )
    );
}

/**
 * Return the checkout header/logo visibility styles used on CartFlows and WooCommerce checkout pages.
 *
 * @return string
 */
function lojaweb_get_checkout_logo_css() {
	return "
body.woocommerce-checkout .wcf-checkout-header-image,
body.cartflows-instant-checkout .main-header--wrapper,
body.woocommerce-checkout .ast-site-header-wrap,
body.woocommerce-checkout .site-header {
	background: linear-gradient(180deg, #ffffff 0%, #f7fafc 100%);
}

body.woocommerce-checkout .wcf-checkout-header-image {
	display: flex;
	justify-content: center;
	align-items: center;
	margin: 28px auto 24px;
	padding: 18px 28px;
	max-width: fit-content;
	border: 1px solid rgba(15, 23, 42, 0.08);
	border-radius: 18px;
	box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
	overflow: visible;
}

body.woocommerce-checkout .wcf-checkout-header-image img,
body.cartflows-instant-checkout .main-header--content .main-header--site-logo img,
body.woocommerce-checkout .custom-logo-link img {
	display: block;
	width: auto !important;
	height: auto !important;
	max-width: min(240px, 72vw);
	max-height: 72px;
	object-fit: contain;
	filter: drop-shadow(0 8px 18px rgba(15, 23, 42, 0.12));
}

body.cartflows-instant-checkout .main-header--wrapper {
	border-bottom: 1px solid rgba(15, 23, 42, 0.08);
	box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
}

body.cartflows-instant-checkout .main-header--content,
body.woocommerce-checkout .site-branding,
body.woocommerce-checkout .ast-site-identity {
	display: flex;
	align-items: center;
	gap: 14px;
	line-height: 0;
	overflow: visible;
}

body.cartflows-instant-checkout .main-header--content {
	padding-top: 18px;
	padding-bottom: 18px;
	min-height: 88px;
}

body.cartflows-instant-checkout .main-header--site-logo,
body.woocommerce-checkout .custom-logo-link {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	overflow: visible;
}

body.woocommerce-checkout .custom-logo-link {
	padding: 12px 18px;
	border-radius: 18px;
	background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
	box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
}

@media (max-width: 768px) {
	body.woocommerce-checkout .wcf-checkout-header-image {
		margin-top: 20px;
		padding: 14px 18px;
		border-radius: 14px;
	}

	body.woocommerce-checkout .wcf-checkout-header-image img,
	body.cartflows-instant-checkout .main-header--content .main-header--site-logo img,
	body.woocommerce-checkout .custom-logo-link img {
		max-width: min(200px, 78vw);
		max-height: 56px;
	}

	body.cartflows-instant-checkout .main-header--content {
		padding-top: 14px;
		padding-bottom: 14px;
		min-height: 72px;
	}
}";
}

/**
 * Print checkout-specific logo styling even when CartFlows bypasses the child theme stylesheet.
 *
 * @return void
 */
function lojaweb_output_checkout_logo_styles() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	if ( ! is_singular( 'cartflows_step' ) && ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) ) {
		return;
	}

	echo '<style id="lojaweb-checkout-logo-styles">' . lojaweb_get_checkout_logo_css() . '</style>';
}
add_action( 'wp_head', 'lojaweb_output_checkout_logo_styles', 99 );

/**
 * Return the CartFlows checkout step URL when Store Checkout is configured globally.
 */
function lojaweb_get_cartflows_store_checkout_url() {
	$flow_id = absint( get_option( '_cartflows_store_checkout' ) );

	if ( ! $flow_id ) {
		return '';
	}

	$steps = get_post_meta( $flow_id, 'wcf-steps', true );

	if ( ! is_array( $steps ) ) {
		return '';
	}

	foreach ( $steps as $step ) {
		if ( empty( $step['type'] ) || 'checkout' !== $step['type'] || empty( $step['id'] ) ) {
			continue;
		}

		$step_id = absint( $step['id'] );

		if ( $step_id && 'publish' === get_post_status( $step_id ) ) {
			return get_permalink( $step_id );
		}
	}

	return '';
}

/**
 * Make WooCommerce use the active CartFlows checkout step instead of the default checkout page.
 */
function lojaweb_use_cartflows_checkout_url( $checkout_url ) {
	$cartflows_checkout_url = lojaweb_get_cartflows_store_checkout_url();

	if ( $cartflows_checkout_url ) {
		return $cartflows_checkout_url;
	}

	return $checkout_url;
}
add_filter( 'woocommerce_get_checkout_url', 'lojaweb_use_cartflows_checkout_url' );

/**
 * Redirect direct visits to the default WooCommerce checkout page to the CartFlows checkout step.
 */
function lojaweb_redirect_default_checkout_to_cartflows() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
		$cartflows_checkout_url = lojaweb_get_cartflows_store_checkout_url();
		$default_checkout_id    = function_exists( 'wc_get_page_id' ) ? absint( wc_get_page_id( 'checkout' ) ) : 0;

		if ( $cartflows_checkout_url && $default_checkout_id && is_page( $default_checkout_id ) ) {
			wp_safe_redirect( $cartflows_checkout_url );
			exit;
		}
	}
}
add_action( 'template_redirect', 'lojaweb_redirect_default_checkout_to_cartflows' );
?>
