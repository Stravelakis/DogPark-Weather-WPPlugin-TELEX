<?php
/**
 * Fires only when the plugin is deleted from the Plugins screen (not on
 * deactivation). Removes every option, transient, and scheduled event this
 * plugin creates so nothing is left behind in wp_options after removal.
 *
 * @package Telex_Dog_Park_Weather
 */

// Safety check WordPress itself sets - blocks this file from running if it's
// ever loaded directly instead of through the real uninstall process.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * All options this plugin ever writes to wp_options. Kept as one list so
 * there's a single place to update if a new setting is ever added.
 */
$telex_dpw_options = array(
	'telex_dpw_parks',
	'telex_dpw_translations',
	'telex_dpw_icons',
	'telex_dpw_api_providers',
	'telex_dpw_appearance',
	'telex_dpw_weights',
	'telex_dpw_deepl_key',
);

foreach ( $telex_dpw_options as $telex_dpw_option ) {
	delete_option( $telex_dpw_option );
	// Multisite: a network activation would have per-site copies as regular
	// options anyway (this plugin isn't network-aware), but on a multisite
	// install run via delete-on-network-deactivate, also clear the
	// site-option variant just in case a future version adds one.
	delete_site_option( $telex_dpw_option );
}

// Per-park weather cache. Transient keys are 'telex_dpw_weather_' + md5(lat_lon)
// and aren't tracked in a registry anywhere, so rather than recompute a hash
// per saved park (which would miss transients left behind by parks that were
// since deleted), clean them up directly by key pattern.
global $wpdb;
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_telex_dpw_weather_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_telex_dpw_weather_' ) . '%'
	)
);

// Scheduled cron events. telex_dpw_deactivate() already clears these on
// deactivation, but deletion can in rare cases be triggered without that
// hook having fired cleanly (e.g. a plugin manager forcing removal), so
// clear them again here as a safety net.
wp_clear_scheduled_hook( 'telex_dpw_refresh_weather' );
wp_clear_scheduled_hook( 'telex_dpw_refresh_weather_now' );
