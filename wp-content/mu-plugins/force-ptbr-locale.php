<?php
/**
 * Force Brazilian Portuguese across the site.
 */

function lojaweb_force_ptbr_locale() {
	return 'pt_BR';
}

add_filter( 'locale', 'lojaweb_force_ptbr_locale' );
add_filter( 'determine_locale', 'lojaweb_force_ptbr_locale' );
