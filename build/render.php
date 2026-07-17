<?php
/**
 * Render callback for the Dog Park Weather Rating block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content (empty for this block).
 * @var WP_Block $block      Block instance.
 *
 * @package TelexDogParkWeather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$park_id = sanitize_key( $attributes['parkId'] ?? '' );

if ( empty( $park_id ) ) {
	return;
}

if ( ! function_exists( 'telex_dpw_get_parks' ) || ! function_exists( 'telex_dpw_get_translations' ) || ! function_exists( 'telex_dpw_get_icons' ) ) {
	return;
}

$parks         = telex_dpw_get_parks();
$translations  = telex_dpw_get_translations();
$icons         = telex_dpw_get_icons();
$appearance    = function_exists( 'telex_dpw_get_appearance' ) ? telex_dpw_get_appearance() : array();
$weights       = function_exists( 'telex_dpw_get_weights' ) ? telex_dpw_get_weights() : array();
$api_providers = function_exists( 'telex_dpw_get_api_providers' ) ? telex_dpw_get_api_providers() : array();

// Display name + link for whichever provider is actually active, for the
// "Powered by" attribution line. The CSS, color setting, and translation
// string for this have existed for a while with no markup ever using them.
$provider_info = array(
	'open_meteo'     => array(
		'name' => 'Open-Meteo',
		'url'  => 'https://open-meteo.com/',
	),
	'openweather'    => array(
		'name' => 'OpenWeather',
		'url'  => 'https://openweathermap.org/',
	),
	'google_weather' => array(
		'name' => 'Google Weather',
		'url'  => 'https://weather.google/',
	),
);
$active_provider_key = sanitize_key( $api_providers['primary'] ?? 'open_meteo' );
$active_provider      = $provider_info[ $active_provider_key ] ?? $provider_info['open_meteo'];

$selected_park = null;
foreach ( $parks as $park ) {
	if ( $park['id'] === $park_id ) {
		$selected_park = $park;
		break;
	}
}

if ( ! $selected_park ) {
	return;
}

$rest_url  = esc_url_raw( rest_url( 'telex-dpw/v1/weather' ) );
$nonce     = wp_create_nonce( 'wp_rest' );
$unique_id = wp_unique_id( 'telex-dpw-' );

$park_json         = wp_json_encode( $selected_park );
$translations_json = wp_json_encode( $translations );
$icons_json        = wp_json_encode( $icons );
$appearance_json   = wp_json_encode( $appearance );
$weights_json      = wp_json_encode( $weights );

// wp_json_encode() returns false (not an empty array) if ANY value anywhere in
// the array contains malformed UTF-8 - a single bad byte in one saved
// translation would otherwise silently blank out ALL translations on the
// frontend, with no visible error. Fall back to an empty object rather than
// letting `false` flow into esc_attr() as an empty string.
$park_json         = false === $park_json ? '{}' : $park_json;
$translations_json = false === $translations_json ? '{}' : $translations_json;
$icons_json        = false === $icons_json ? '{}' : $icons_json;
$appearance_json   = false === $appearance_json ? '{}' : $appearance_json;
$weights_json      = false === $weights_json ? '{}' : $weights_json;

// Maps appearance keys to the CSS custom properties declared in style.scss so
// the Appearance settings tab actually reaches the frontend (previously it didn't).
$css_var_map = array(
	'header_gradient_start' => '--dpw-header-start',
	'header_gradient_end'   => '--dpw-header-end',
	'rating_gradient_start' => '--dpw-rating-start',
	'rating_gradient_end'   => '--dpw-rating-end',
	'tab_active_bg'         => '--dpw-tab-active-bg',
	'tab_active_text'       => '--dpw-tab-active-text',
	'tab_border'            => '--dpw-tab-border',
	'card_bg'               => '--dpw-card-bg',
	'row_bg'                => '--dpw-row-bg',
	'row_border_5'          => '--dpw-border-5',
	'row_border_4'          => '--dpw-border-4',
	'row_border_3'          => '--dpw-border-3',
	'row_border_2'          => '--dpw-border-2',
	'row_border_1'          => '--dpw-border-1',
	'now_row_bg_start'      => '--dpw-now-start',
	'now_row_bg_end'        => '--dpw-now-end',
	'text_color'            => '--dpw-text',
	'header_text'           => '--dpw-header-text',
	'paw_active_color'      => '--dpw-paw-active',
	'paw_inactive_color'    => '--dpw-paw-inactive',
	'section_title_color'   => '--dpw-section-title',
	'powered_by_color'      => '--dpw-powered-by',
	'font_family'           => '--dpw-font',
);

$css_overrides = '';
foreach ( $css_var_map as $appearance_key => $css_var ) {
	$value = $appearance[ $appearance_key ] ?? '';
	if ( '_color' === substr( $appearance_key, -6 ) ) {
		// Only accept real #rgb/#rrggbb values here, in case this option was
		// saved by an older version of the plugin before hex validation existed.
		if ( ! preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value ) ) {
			continue;
		}
	} elseif ( '' === $value ) {
		continue;
	}
	$css_overrides .= $css_var . ':' . $value . ';';
}

// Block height is numeric (not part of the color/string map above) and always
// has a value (defaulted in telex_dpw_get_default_appearance()), so it's
// applied directly rather than through the generic loop.
$block_height = isset( $appearance['block_height'] ) ? (int) $appearance['block_height'] : 780;
$block_height = max( 300, min( 2000, $block_height ) );
$css_overrides .= '--dpw-block-height:' . $block_height . 'px;';

// Text alternative for screen readers, identifying this as a distinct
// landmark region rather than an unlabeled chunk of the page - important
// since the widget is visually dense (numbers, icons, a scrolling list)
// with no single heading a screen reader would otherwise land on.
$aria_label = sprintf(
	/* translators: %s: park name */
	__( 'Dog park weather forecast for %s', 'telex-dog-park-weather' ),
	$selected_park['name']
);
?>
<?php if ( $css_overrides ) : ?>
<style>#<?php echo esc_attr( $unique_id ); ?>{<?php echo wp_strip_all_tags( $css_overrides ); ?>}</style>
<?php endif; ?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'telex-dpw-block' ) ); ?>
	role="region"
	aria-label="<?php echo esc_attr( $aria_label ); ?>"
	id="<?php echo esc_attr( $unique_id ); ?>"
	data-rest-url="<?php echo esc_attr( $rest_url ); ?>"
	data-nonce="<?php echo esc_attr( $nonce ); ?>"
	data-park="<?php echo esc_attr( $park_json ); ?>"
	data-translations="<?php echo esc_attr( $translations_json ); ?>"
	data-icons="<?php echo esc_attr( $icons_json ); ?>"
	data-appearance="<?php echo esc_attr( $appearance_json ); ?>"
	data-weights="<?php echo esc_attr( $weights_json ); ?>"
>
	<div class="telex-dpw-card">
		<div class="telex-dpw-header">
			<span class="telex-dpw-park-name"><?php echo esc_html( $selected_park['name'] ); ?></span>
		</div>

		<div class="telex-dpw-rating-banner">
			<div class="telex-dpw-rating-text" data-ref="rating-text">
				<?php echo esc_html( $translations['loading'] ?? 'Loading weather data...' ); ?>
			</div>
			<div class="telex-dpw-best-hour" data-ref="best-hour"></div>
		</div>

		<div class="telex-dpw-tabs" data-ref="tabs">
			<button class="telex-dpw-tab is-active" data-day="today" aria-pressed="true"><?php echo esc_html( $translations['today'] ?? 'Today' ); ?></button>
			<button class="telex-dpw-tab" data-day="tomorrow" aria-pressed="false"><?php echo esc_html( $translations['tomorrow'] ?? 'Tomorrow' ); ?></button>
		</div>

		<div class="telex-dpw-section-title"><?php echo esc_html( $translations['hourly_forecast'] ?? 'Hourly Forecast' ); ?></div>

		<div class="telex-dpw-hourly-list" data-ref="hourly-list">
			<div class="telex-dpw-loading">
				<p><?php echo esc_html( $translations['loading'] ?? 'Loading weather data...' ); ?></p>
			</div>
		</div>

		<div class="telex-dpw-powered-by">
			<?php echo esc_html( $translations['powered_by'] ?? 'Powered by' ); ?>
			<a href="https://skilitsa.com" target="_blank" rel="noopener noreferrer">Skilitsa.com</a>
			<?php echo esc_html( $translations['powered_by_and'] ?? 'and' ); ?>
			<a href="<?php echo esc_url( $active_provider['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $active_provider['name'] ); ?></a>
		</div>
	</div>
</div>
