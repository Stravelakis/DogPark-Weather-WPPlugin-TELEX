<?php
/**
 * Plugin Name:       Dog Park Weather Rating
 * Plugin URI:        https://skilitsa.com
 * Description:       A beautiful hourly weather forecast block for dog parks with a 1-5 paw rating system, per-park mitigations, translatable strings, and custom Lottie icon support.
 * Version:           0.12.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Skilitsa.com
 * Author URI:        https://skilitsa.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       telex-dog-park-weather
 *
 * @package TelexDogParkWeather
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// GitHub-based update checker (MIT-licensed third-party library, vendored in
// vendor/plugin-update-checker/) - gives the normal wp-admin "Update
// available" notice and one-click update, without needing WordPress.org
// review or hosting. It works by comparing this file's Version: header
// against the same header on the repo's main branch, so a new version only
// needs the header bumped and pushed - no separate GitHub Release required
// (though PUC supports that too, if a more formal release process is ever
// wanted). See HANDOVER.md for the two-build-variant setup this pairs with.
if ( ! function_exists( 'telex_dpw_init_update_checker' ) ) {
	function telex_dpw_init_update_checker(): void {
		$lib_path = __DIR__ . '/vendor/plugin-update-checker/plugin-update-checker.php';
		if ( ! file_exists( $lib_path ) ) {
			return; // The "-repo" build variant may omit this; updates just won't be offered.
		}
		require_once $lib_path;
		if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}
		$update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			'https://github.com/Stravelakis/Skilitsa.com-Dog-Park-Weather--WordPress-plugin/',
			__FILE__,
			'telex-dog-park-weather'
		);
		$update_checker->setBranch( 'main' );
	}
	telex_dpw_init_update_checker();
}

if ( ! function_exists( 'telex_dog_park_weather_block_init' ) ) {
	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 *
	 * @return void
	 */
	function telex_dog_park_weather_block_init(): void {
		register_block_type( __DIR__ . '/build/' );
	}
	add_action( 'init', 'telex_dog_park_weather_block_init' );
}

if ( ! function_exists( 'telex_dpw_get_default_translations' ) ) {
	/**
	 * Returns default translation strings.
	 *
	 * @return array<string, string>
	 */
	function telex_dpw_get_default_translations(): array {
		return array(
			'now'              => 'Now',
			'today'            => 'Today',
			'tomorrow'         => 'Tomorrow',
			'hourly_forecast'  => 'Hourly Forecast',
			'top_hour'         => 'TOP HOUR',
			'rain'             => 'Rain',
			'wind'             => 'Wind',
			'uv'               => 'UV',
			'cloud'            => 'Cloud',
			'temp'             => 'Temp',
			'darkness'         => 'Darkness',
			'am'               => 'AM',
			'pm'               => 'PM',
			'rating_5'         => 'Pawfect weather! Time for a big sniffari.',
			'rating_4'         => 'Great conditions for the park!',
			'rating_3'         => 'Decent weather, keep an eye out.',
			'rating_2'         => 'Not ideal, a short visit maybe.',
			'rating_1'         => 'Best to stay home and cuddle.',
			'good_drainage'    => 'Good Drainage',
			'good_shade'       => 'Good Shade',
			'wind_shelter'     => 'Wind Shelter',
			'covered_areas'    => 'Covered Areas',
			'select_park'      => 'Select a park',
			'no_parks'         => 'No parks configured. Go to Settings → Dog Park Weather to add one.',
			'loading'          => 'Loading weather data...',
			'error'            => 'Could not load weather data.',
			'feels_like'       => 'Feels like',
			'powered_by'       => 'Powered by',
			'powered_by_and'   => 'and',
			'beaufort_0'       => 'Calm',
			'beaufort_1'       => 'Light air',
			'beaufort_2'       => 'Light breeze',
			'beaufort_3'       => 'Gentle breeze',
			'beaufort_4'       => 'Moderate breeze',
			'beaufort_5'       => 'Fresh breeze',
			'beaufort_6'       => 'Strong breeze',
			'beaufort_7'       => 'Near gale',
			'beaufort_8'       => 'Gale',
			'beaufort_9'       => 'Strong gale',
			'beaufort_10'      => 'Storm',
			'beaufort_11'      => 'Violent storm',
			'beaufort_12'      => 'Hurricane',
			// Shown in the hover tooltip on each hour's paw rating, explaining
			// why it got that score. Previously hardcoded in English in the
			// frontend JS with no way to override them - now real settings.
			'reason_cold_extreme' => 'Dangerously cold',
			'reason_hot_extreme'  => 'Dangerously hot',
			'reason_severe'       => 'Severe weather warning',
			'reason_rain'         => 'Rain chance',
			'reason_uv'           => 'High UV',
			'reason_wind'         => 'Windy',
			'reason_temp'         => 'Uncomfortable temperature',
			'reason_cloud'        => 'Overcast',
			'reason_dark'         => 'After dark',
			'reason_ideal'        => 'Ideal conditions',
			// Read by screen readers before the "why" explanation, since
			// the paw icons' fill color has no text equivalent on their
			// own - without this, a screen reader user would hear the
			// reasoning but never learn the actual 1-5 rating.
			'aria_rating_label'   => '%d out of 5 paws',
		);
	}
}

if ( ! function_exists( 'telex_dpw_get_default_appearance' ) ) {
	/**
	 * Returns default appearance settings.
	 *
	 * @return array<string, string>
	 */
	function telex_dpw_get_default_appearance(): array {
		return array(
			// Colors below were audited for WCAG contrast this session and
			// several were changed from their original values - see
			// HANDOVER.md for the specific numbers. This isn't just a visual
			// preference: white text on the old, lighter blue/pink measured
			// well under the 4.5:1 WCAG AA minimum for normal text.
			'header_gradient_start' => '#1976D2',
			'header_gradient_end'   => '#1565C0',
			'rating_gradient_start' => '#F48FB1',
			'rating_gradient_end'   => '#D8177F',
			'tab_active_bg'         => '#1976D2',
			'tab_active_text'       => '#FFFFFF',
			'tab_border'            => '#1976D2',
			'card_bg'               => '#ECEFF1',
			'row_bg'                => '#FFFFFF',
			'row_border_5'          => '#1976D2',
			'row_border_4'          => '#F48FB1',
			'row_border_3'          => '#F5A9C5',
			'row_border_2'          => '#FFCDD2',
			'row_border_1'          => '#EF9A9A',
			'now_row_bg_start'      => '#1976D2',
			'now_row_bg_end'        => '#1565C0',
			'text_color'            => '#37474F',
			'header_text'           => '#FFFFFF',
			'paw_active_color'      => '#E91E8F',
			'paw_inactive_color'    => '#949494',
			'icon_outline_color'    => '#000000',
			'icon_accent_color'     => '#33CCCC',
			'section_title_color'   => '#37474F',
			'powered_by_color'      => '#546E7A',
			'font_family'           => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, sans-serif',
			'block_height'          => '780',
		);
	}
}

if ( ! function_exists( 'telex_dpw_get_appearance' ) ) {
	/**
	 * Returns merged appearance settings.
	 *
	 * @return array<string, string>
	 */
	function telex_dpw_get_appearance(): array {
		$defaults = telex_dpw_get_default_appearance();
		$saved    = get_option( 'telex_dpw_appearance', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return array_merge( $defaults, $saved );
	}
}

if ( ! function_exists( 'telex_dpw_get_default_weights' ) ) {
	/**
	 * Returns default condition weights.
	 *
	 * @return array<string, float>
	 */
	function telex_dpw_get_default_weights(): array {
		return array(
			'rain'       => 1.0,
			'uv'         => 1.0,
			'wind'       => 1.0,
			'temp'       => 1.0,
			'cloud'      => 1.0,
			'darkness'   => 1.0,
		);
	}
}

if ( ! function_exists( 'telex_dpw_get_weights' ) ) {
	/**
	 * Returns merged condition weights.
	 *
	 * @return array<string, float>
	 */
	function telex_dpw_get_weights(): array {
		$defaults = telex_dpw_get_default_weights();
		$saved    = get_option( 'telex_dpw_weights', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$merged = array();
		foreach ( $defaults as $key => $val ) {
			$merged[ $key ] = isset( $saved[ $key ] ) ? (float) $saved[ $key ] : $val;
		}
		return $merged;
	}
}

if ( ! function_exists( 'telex_dpw_get_translations' ) ) {
	/**
	 * Returns merged translations (defaults + user overrides).
	 *
	 * @return array<string, string>
	 */
	function telex_dpw_get_translations(): array {
		$defaults = telex_dpw_get_default_translations();
		$saved    = get_option( 'telex_dpw_translations', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return array_merge( $defaults, $saved );
	}
}

if ( ! function_exists( 'telex_dpw_get_parks' ) ) {
	/**
	 * Returns the list of configured parks.
	 *
	 * @return array<int, array{id: string, name: string, lat: string, lon: string, drainage: bool, shade: bool, wind_shelter: bool, covered: bool}>
	 */
	function telex_dpw_get_parks(): array {
		$parks = get_option( 'telex_dpw_parks', array() );
		if ( ! is_array( $parks ) ) {
			return array();
		}
		return $parks;
	}
}

if ( ! function_exists( 'telex_dpw_get_icons' ) ) {
	/**
	 * Returns custom icon URLs.
	 *
	 * @return array<string, string>
	 */
	function telex_dpw_get_icons(): array {
		$base_url  = plugins_url( 'assets/icons/', __FILE__ );
		$base_path = plugin_dir_path( __FILE__ ) . 'assets/icons/';

		// Emoji fallbacks - used automatically when the licensed Lottie/SVG
		// files aren't present in this build. This is what makes the public
		// "-repo" distribution safe: it's the exact same code, just packaged
		// without the assets/icons/*.json and *.svg files, and everything
		// falls back to plain emoji (needs no license at all) instead of
		// erroring or showing a broken icon. See HANDOVER.md for why the
		// bundled icons can't be redistributed publicly as-is.
		$emoji_fallbacks = array(
			'paw_active'    => "\xF0\x9F\x90\xBE", // 🐾
			'paw_inactive'  => "\xF0\x9F\x90\xBE", // 🐾 (shown at reduced opacity instead of a different symbol)
			'sun'           => "\xE2\x98\x80\xEF\xB8\x8F", // ☀️
			'partly_cloudy' => "\xE2\x9B\x85", // ⛅
			'cloudy'        => "\xE2\x98\x81\xEF\xB8\x8F", // ☁️
			'rain'          => "\xF0\x9F\x8C\xA7\xEF\xB8\x8F", // 🌧️
			'storm'         => "\xE2\x9B\x88\xEF\xB8\x8F", // ⛈️
			'snow'          => "\xF0\x9F\x8C\xA8\xEF\xB8\x8F", // 🌨️
			'moon'          => "\xF0\x9F\x8C\x99", // 🌙
			'moon_cloudy'   => "\xF0\x9F\x8C\x99", // 🌙 (reused - a two-symbol combo icon isn't worth the complexity for a fallback tier)
		);
		$asset_files = array(
			'paw_active'    => 'paw_active.json',
			'paw_inactive'  => 'paw_inactive.svg',
			'sun'           => 'sun.json',
			'partly_cloudy' => 'partly_cloudy.json',
			'cloudy'        => 'cloudy.json',
			'rain'          => 'rain.json',
			'storm'         => 'storm.json',
			'snow'          => 'snow.json',
			'moon'          => 'moon.json',
			'moon_cloudy'   => 'moon_cloudy.json',
		);

		$defaults = array();
		foreach ( $asset_files as $key => $filename ) {
			$defaults[ $key ] = file_exists( $base_path . $filename ) ? ( $base_url . $filename ) : $emoji_fallbacks[ $key ];
		}

		$saved = get_option( 'telex_dpw_icons', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		return array_merge( $defaults, $saved );
	}
}

if ( ! function_exists( 'telex_dpw_get_default_api_providers' ) ) {
	/**
	 * Returns default API provider settings.
	 *
	 * @return array{primary: string, retries: int, providers: array<string, array{enabled: bool, api_key: string}>}
	 */
	function telex_dpw_get_default_api_providers(): array {
		return array(
			'primary'   => 'open_meteo',
			'retries'   => 2,
			'providers' => array(
				'open_meteo'     => array(
					'enabled' => true,
					'api_key' => '',
				),
				'openweather'    => array(
					'enabled' => false,
					'api_key' => '',
				),
				'google_weather' => array(
					'enabled' => false,
					'api_key' => '',
				),
			),
		);
	}
}

if ( ! function_exists( 'telex_dpw_get_api_providers' ) ) {
	/**
	 * Returns merged API provider settings.
	 *
	 * @return array{primary: string, retries: int, providers: array<string, array{enabled: bool, api_key: string}>}
	 */
	function telex_dpw_get_api_providers(): array {
		$defaults = telex_dpw_get_default_api_providers();
		$saved    = get_option( 'telex_dpw_api_providers', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$merged = array(
			'primary' => sanitize_key( $saved['primary'] ?? $defaults['primary'] ),
			'retries' => absint( $saved['retries'] ?? $defaults['retries'] ),
			'providers' => array(),
		);
		foreach ( $defaults['providers'] as $key => $def ) {
			$s = $saved['providers'][ $key ] ?? array();
			$merged['providers'][ $key ] = array(
				'enabled' => isset( $s['enabled'] ) ? (bool) $s['enabled'] : $def['enabled'],
				'api_key' => $s['api_key'] ?? $def['api_key'],
			);
		}
		return $merged;
	}
}

if ( ! function_exists( 'telex_dpw_kmh_to_beaufort' ) ) {
	/**
	 * Converts km/h wind speed to Beaufort scale number.
	 *
	 * @param float $kmh Wind speed in km/h.
	 * @return int Beaufort number (0-12).
	 */
	function telex_dpw_kmh_to_beaufort( float $kmh ): int {
		if ( $kmh < 1 ) return 0;
		if ( $kmh < 6 ) return 1;
		if ( $kmh < 12 ) return 2;
		if ( $kmh < 20 ) return 3;
		if ( $kmh < 29 ) return 4;
		if ( $kmh < 39 ) return 5;
		if ( $kmh < 50 ) return 6;
		if ( $kmh < 62 ) return 7;
		if ( $kmh < 75 ) return 8;
		if ( $kmh < 89 ) return 9;
		if ( $kmh < 103 ) return 10;
		if ( $kmh < 118 ) return 11;
		return 12;
	}
}

if ( ! function_exists( 'telex_dpw_register_settings' ) ) {
	/**
	 * Registers plugin settings.
	 *
	 * @return void
	 */
	function telex_dpw_register_settings(): void {
		register_setting( 'telex_dpw_parks_settings', 'telex_dpw_parks', array(
			'type'              => 'array',
			'sanitize_callback' => 'telex_dpw_sanitize_parks',
			'default'           => array(),
			'show_in_rest'      => false,
		) );
		register_setting( 'telex_dpw_translations_settings', 'telex_dpw_translations', array(
			'type'              => 'array',
			'sanitize_callback' => 'telex_dpw_sanitize_translations',
			'default'           => array(),
			'show_in_rest'      => false,
		) );
		register_setting( 'telex_dpw_translations_settings', 'telex_dpw_deepl_key', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
			'show_in_rest'      => false,
		) );
		register_setting( 'telex_dpw_icons_settings', 'telex_dpw_icons', array(
			'type'              => 'array',
			'sanitize_callback' => 'telex_dpw_sanitize_icons',
			'default'           => array(),
			'show_in_rest'      => false,
		) );
		register_setting( 'telex_dpw_api_providers_settings', 'telex_dpw_api_providers', array(
			'type'              => 'array',
			'sanitize_callback' => 'telex_dpw_sanitize_api_providers',
			'default'           => array(),
			'show_in_rest'      => false,
		) );
		register_setting( 'telex_dpw_appearance_settings', 'telex_dpw_appearance', array(
			'type'              => 'array',
			'sanitize_callback' => 'telex_dpw_sanitize_appearance',
			'default'           => array(),
			'show_in_rest'      => false,
		) );
		register_setting( 'telex_dpw_weights_settings', 'telex_dpw_weights', array(
			'type'              => 'array',
			'sanitize_callback' => 'telex_dpw_sanitize_weights',
			'default'           => array(),
			'show_in_rest'      => false,
		) );
	}
	add_action( 'admin_init', 'telex_dpw_register_settings' );
}

if ( ! function_exists( 'telex_dpw_sanitize_appearance' ) ) {
	/**
	 * Sanitizes the appearance option.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string, string>
	 */
	function telex_dpw_sanitize_appearance( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$defaults   = telex_dpw_get_default_appearance();
		$valid_keys = array_keys( $defaults );
		$sanitized  = array();
		foreach ( $input as $key => $value ) {
			$key = sanitize_key( $key );
			if ( ! in_array( $key, $valid_keys, true ) ) {
				continue;
			}
			if ( '_color' === substr( $key, -6 ) ) {
				// Strict allowlist: only real #rgb/#rrggbb hex values are stored.
				// This value is later echoed straight into an inline <style> block,
				// so anything other than a validated hex color is rejected rather
				// than merely tag-stripped.
				$color = sanitize_hex_color( $value );
				$sanitized[ $key ] = $color ? $color : $defaults[ $key ];
			} elseif ( 'font_family' === $key ) {
				$sanitized[ $key ] = preg_replace( '/[^a-zA-Z0-9\s,\-\'"]/', '', (string) $value );
			} elseif ( 'block_height' === $key ) {
				// Keep the widget usable: don't allow it to be squashed to nothing
				// or stretched to something silly. 300-2000px covers everything
				// from a compact sidebar widget to a full-page embed.
				$height = (int) $value;
				$height = max( 300, min( 2000, $height ) );
				$sanitized[ $key ] = (string) $height;
			} else {
				$sanitized[ $key ] = sanitize_text_field( $value );
			}
		}
		return $sanitized;
	}
}

if ( ! function_exists( 'telex_dpw_sanitize_weights' ) ) {
	/**
	 * Sanitizes the weights option.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string, float>
	 */
	function telex_dpw_sanitize_weights( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$valid_keys = array_keys( telex_dpw_get_default_weights() );
		$sanitized  = array();
		foreach ( $input as $key => $value ) {
			$key = sanitize_key( $key );
			if ( in_array( $key, $valid_keys, true ) ) {
				$val = (float) $value;
				$sanitized[ $key ] = max( 0.0, min( 5.0, $val ) );
			}
		}
		return $sanitized;
	}
}

if ( ! function_exists( 'telex_dpw_sanitize_parks' ) ) {
	/**
	 * Sanitizes the parks option.
	 *
	 * @param mixed $input Raw input.
	 * @return array<int, array{id: string, name: string, lat: string, lon: string, drainage: bool, shade: bool, wind_shelter: bool, covered: bool}>
	 */
	function telex_dpw_sanitize_parks( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$sanitized = array();
		foreach ( $input as $park ) {
			if ( ! is_array( $park ) || empty( $park['name'] ) ) {
				continue;
			}
			$sanitized[] = array(
				'id'            => sanitize_key( $park['id'] ?? wp_generate_uuid4() ),
				'name'          => sanitize_text_field( $park['name'] ?? '' ),
				'lat'           => sanitize_text_field( $park['lat'] ?? '' ),
				'lon'           => sanitize_text_field( $park['lon'] ?? '' ),
				'drainage'      => ! empty( $park['drainage'] ),
				'shade'         => ! empty( $park['shade'] ),
				'wind_shelter'  => ! empty( $park['wind_shelter'] ),
				'covered'       => ! empty( $park['covered'] ),
			);
		}
		return $sanitized;
	}
}

if ( ! function_exists( 'telex_dpw_parse_parks_csv' ) ) {
	/**
	 * Parses CSV text into simple {name, lat, lon} rows.
	 *
	 * Understands two header styles: this plugin's own simple export
	 * (Name / Latitude / Longitude), and Ak's "Dog parks - Greece" Google
	 * Sheet naming ("Park Name / Area" plus a single "Municipality
	 * coordinates for weather reports" cell formatted as "lat, lon"). Only
	 * name + coordinates are pulled in — the sheet's other columns (fencing,
	 * benches, agility features, etc.) belong to Skilitsa Park Visits, not
	 * this weather widget, so they're intentionally ignored here.
	 *
	 * @param string $csv Raw CSV text.
	 * @return array<int, array{name: string, lat: string, lon: string}>
	 */
	function telex_dpw_parse_parks_csv( string $csv ): array {
		$csv   = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $csv ); // Strip a UTF-8 BOM if present.
		$lines = preg_split( '/\r\n|\r|\n/', trim( (string) $csv ) );
		if ( empty( $lines ) ) {
			return array();
		}

		$header = str_getcsv( (string) array_shift( $lines ) );
		$header = array_map(
			function ( $h ) {
				return strtolower( trim( (string) $h ) );
			},
			$header
		);

		$find_index = function ( array $candidates ) use ( $header ) {
			foreach ( $candidates as $c ) {
				$idx = array_search( $c, $header, true );
				if ( false !== $idx ) {
					return $idx;
				}
			}
			return false;
		};

		$name_idx  = $find_index( array( 'park name / area', 'park name', 'name' ) );
		$coord_idx = $find_index( array( 'municipality coordinates for weather reports', 'coordinates', 'coords' ) );
		$lat_idx   = $find_index( array( 'latitude', 'lat' ) );
		$lon_idx   = $find_index( array( 'longitude', 'lon', 'lng' ) );

		// Optional mitigation columns. Naming for these varies a lot more than
		// name/coordinates does, so a wider set of likely header phrasings is
		// matched (including Greek "yes/no" wording, since this is built for a
		// Greek-language sheet). Missing columns simply leave that mitigation
		// off, same as manually adding a park - nothing breaks if they're absent.
		$drainage_idx = $find_index( array( 'good drainage', 'drainage' ) );
		$shade_idx    = $find_index( array( 'good shade', 'shade' ) );
		$wind_idx     = $find_index( array( 'wind shelter', 'shelter', 'wind-sheltered', 'wind protection' ) );
		$covered_idx  = $find_index( array( 'covered areas', 'covered', 'covered area' ) );

		if ( false === $name_idx || ( false === $coord_idx && ( false === $lat_idx || false === $lon_idx ) ) ) {
			return array();
		}

		$is_truthy = function ( $value ): bool {
			$value = strtolower( trim( (string) $value ) );
			return in_array( $value, array( 'yes', 'y', 'true', '1', 'ναι' ), true );
		};

		$rows = array();
		foreach ( $lines as $line ) {
			if ( '' === trim( (string) $line ) ) {
				continue;
			}
			$cols = str_getcsv( (string) $line );
			$name = isset( $cols[ $name_idx ] ) ? trim( (string) $cols[ $name_idx ] ) : '';
			if ( '' === $name ) {
				continue;
			}

			$lat = '';
			$lon = '';
			if ( false !== $coord_idx && isset( $cols[ $coord_idx ] ) ) {
				$parts = array_map( 'trim', explode( ',', (string) $cols[ $coord_idx ] ) );
				if ( 2 === count( $parts ) ) {
					list( $lat, $lon ) = $parts;
				}
			}
			if ( ( '' === $lat || '' === $lon ) && false !== $lat_idx && false !== $lon_idx ) {
				$lat = isset( $cols[ $lat_idx ] ) ? trim( (string) $cols[ $lat_idx ] ) : $lat;
				$lon = isset( $cols[ $lon_idx ] ) ? trim( (string) $cols[ $lon_idx ] ) : $lon;
			}

			// Skips rows with missing or unusable coordinates (e.g. sheet cells
			// like "Pending" or "The language entered is not supported").
			if ( ! is_numeric( $lat ) || ! is_numeric( $lon ) ) {
				continue;
			}

			$rows[] = array(
				'name'         => $name,
				'lat'          => $lat,
				'lon'          => $lon,
				'drainage'     => ( false !== $drainage_idx && isset( $cols[ $drainage_idx ] ) ) ? $is_truthy( $cols[ $drainage_idx ] ) : false,
				'shade'        => ( false !== $shade_idx && isset( $cols[ $shade_idx ] ) ) ? $is_truthy( $cols[ $shade_idx ] ) : false,
				'wind_shelter' => ( false !== $wind_idx && isset( $cols[ $wind_idx ] ) ) ? $is_truthy( $cols[ $wind_idx ] ) : false,
				'covered'      => ( false !== $covered_idx && isset( $cols[ $covered_idx ] ) ) ? $is_truthy( $cols[ $covered_idx ] ) : false,
			);
		}
		return $rows;
	}
}

if ( ! function_exists( 'telex_dpw_sheet_url_to_csv_export' ) ) {
	/**
	 * Converts a Google Sheets URL (any /edit link) into its CSV export URL.
	 * Only works if the sheet is shared as "Anyone with the link" — this
	 * plugin runs server-side and can't sign in to Google on your behalf.
	 *
	 * @param string $url A Google Sheets URL.
	 * @return string|false CSV export URL, or false if the URL doesn't look like a Sheet.
	 */
	function telex_dpw_sheet_url_to_csv_export( string $url ) {
		// "Publish to web" URLs look like .../spreadsheets/d/e/{published-id}/pubhtml
		// - a different ID space from a normal share link, and the only kind of
		// Google Sheets URL that's fetchable without being signed in, since
		// publishing is a separate, explicit "make this public" action.
		if ( preg_match( '#docs\.google\.com/spreadsheets/d/e/([a-zA-Z0-9_-]+)#', $url, $pm ) ) {
			$gid = '0';
			if ( preg_match( '/[#&?]gid=([0-9]+)/', $url, $gm ) ) {
				$gid = $gm[1];
			}
			return 'https://docs.google.com/spreadsheets/d/e/' . $pm[1] . '/pub?gid=' . $gid . '&single=true&output=csv';
		}

		// A normal /d/{id}/edit link. This only succeeds if the sheet is
		// shared "Anyone with the link - Viewer" - anything requiring
		// sign-in returns Google's HTML login page instead of CSV, which is
		// caught by the caller before this string is treated as data.
		if ( preg_match( '#docs\.google\.com/spreadsheets/d/([a-zA-Z0-9_-]+)#', $url, $m ) ) {
			$gid = '0';
			if ( preg_match( '/[#&?]gid=([0-9]+)/', $url, $gm ) ) {
				$gid = $gm[1];
			}
			return 'https://docs.google.com/spreadsheets/d/' . $m[1] . '/export?format=csv&gid=' . $gid;
		}

		return false;
	}
}

if ( ! function_exists( 'telex_dpw_csv_headers_found' ) ) {
	/**
	 * Checks whether a CSV's header row contains recognizable name +
	 * coordinate columns, without fully parsing the rows. Used purely to
	 * give a more specific error message than "nothing was imported" when
	 * an import comes back empty.
	 *
	 * @param string $csv Raw CSV text.
	 * @return bool
	 */
	function telex_dpw_csv_headers_found( string $csv ): bool {
		$lines = preg_split( '/\r\n|\r|\n/', trim( (string) $csv ) );
		if ( empty( $lines ) ) {
			return false;
		}
		$header = str_getcsv( (string) $lines[0] );
		$header = array_map(
			function ( $h ) {
				return strtolower( trim( (string) $h ) );
			},
			$header
		);
		$has_name  = array_intersect( array( 'park name / area', 'park name', 'name' ), $header );
		$has_coord = array_intersect( array( 'municipality coordinates for weather reports', 'coordinates', 'coords' ), $header );
		$has_lat   = array_intersect( array( 'latitude', 'lat' ), $header );
		$has_lon   = array_intersect( array( 'longitude', 'lon', 'lng' ), $header );
		return ! empty( $has_name ) && ( ! empty( $has_coord ) || ( ! empty( $has_lat ) && ! empty( $has_lon ) ) );
	}
}

if ( ! function_exists( 'telex_dpw_handle_import_parks' ) ) {
	/**
	 * Handles the "Import Parks" form on the Parks tab. Adds new parks by
	 * name + coordinates; existing parks (matched by name, case-insensitive)
	 * are left untouched rather than duplicated or overwritten.
	 *
	 * @return void
	 */
	function telex_dpw_handle_import_parks(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'telex-dog-park-weather' ) );
		}
		check_admin_referer( 'telex_dpw_import_parks' );

		$csv = isset( $_POST['telex_dpw_import_csv'] ) ? wp_unslash( (string) $_POST['telex_dpw_import_csv'] ) : '';
		$tried_url = false;

		// If no CSV was pasted, try fetching it from a Google Sheet URL. This
		// only succeeds if the sheet is shared "Anyone with the link" - a
		// sign-in-required sheet (the default for a new sheet) can't be
		// fetched by the server, hence the paste-CSV box as the reliable path.
		if ( '' === trim( $csv ) && ! empty( $_POST['telex_dpw_import_sheet_url'] ) ) {
			$tried_url  = true;
			$sheet_url  = esc_url_raw( wp_unslash( (string) $_POST['telex_dpw_import_sheet_url'] ) );
			$export_url = telex_dpw_sheet_url_to_csv_export( $sheet_url );
			if ( $export_url ) {
				$response = wp_remote_get( $export_url, array( 'timeout' => 20 ) );
				if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
					$body = wp_remote_retrieve_body( $response );
					// A sign-in-walled sheet redirects to an HTML login page
					// instead of returning CSV - don't try to parse that as data.
					if ( 0 !== strpos( ltrim( $body ), '<' ) ) {
						$csv = $body;
					}
				}
			}
		}

		$imported     = 0;
		$skipped      = 0;
		$error_reason = '';

		if ( '' === trim( $csv ) ) {
			$error_reason = $tried_url ? 'fetch_failed' : 'empty';
		} else {
			$rows           = telex_dpw_parse_parks_csv( $csv );
			$existing       = telex_dpw_get_parks();
			$existing_names = array_map(
				function ( $p ) {
					return strtolower( trim( $p['name'] ) );
				},
				$existing
			);

			if ( empty( $rows ) ) {
				$error_reason = telex_dpw_csv_headers_found( $csv ) ? 'no_valid_rows' : 'no_columns';
			}

			foreach ( $rows as $row ) {
				if ( in_array( strtolower( $row['name'] ), $existing_names, true ) ) {
					++$skipped;
					continue;
				}
				$existing[]       = array(
					'id'           => sanitize_key( wp_generate_uuid4() ),
					'name'         => $row['name'],
					'lat'          => $row['lat'],
					'lon'          => $row['lon'],
					'drainage'     => ! empty( $row['drainage'] ),
					'shade'        => ! empty( $row['shade'] ),
					'wind_shelter' => ! empty( $row['wind_shelter'] ),
					'covered'      => ! empty( $row['covered'] ),
				);
				$existing_names[] = strtolower( $row['name'] );
				++$imported;
			}

			update_option( 'telex_dpw_parks', telex_dpw_sanitize_parks( $existing ) );
		}

		$redirect = add_query_arg(
			array(
				'page'         => 'telex-dpw-settings',
				'tab'          => 'parks',
				'imported'     => $imported,
				'skipped'      => $skipped,
				'importerror'  => $error_reason ? $error_reason : '0',
			),
			admin_url( 'options-general.php' )
		);
		wp_safe_redirect( $redirect );
		exit;
	}
	add_action( 'admin_post_telex_dpw_import_parks', 'telex_dpw_handle_import_parks' );
}

if ( ! function_exists( 'telex_dpw_sanitize_translations' ) ) {
	/**
	 * Sanitizes the translations option.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string, string>
	 */
	function telex_dpw_sanitize_translations( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$sanitized  = array();
		$valid_keys = array_keys( telex_dpw_get_default_translations() );
		foreach ( $input as $key => $value ) {
			$key = sanitize_key( $key );
			if ( in_array( $key, $valid_keys, true ) ) {
				// wp_json_encode() (used later, in render.php, to hand this whole
				// array to the frontend) returns false on ANY malformed-UTF-8 byte
				// anywhere in the array, which then silently blanks out *every*
				// translation on the block, not just the bad one. A stray byte
				// from a paste (e.g. from Word/Docs) is enough to trigger this, so
				// strip invalid UTF-8 here rather than letting it reach json_encode.
				$clean             = wp_check_invalid_utf8( (string) $value, true );
				$sanitized[ $key ] = sanitize_text_field( $clean );
			}
		}
		return $sanitized;
	}
}

if ( ! function_exists( 'telex_dpw_sanitize_icons' ) ) {
	/**
	 * Sanitizes the icons option.
	 *
	 * @param mixed $input Raw input.
	 * @return array<string, string>
	 */
	function telex_dpw_sanitize_icons( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$sanitized  = array();
		$valid_keys = array(
			'paw_active', 'paw_inactive',
			'sun', 'partly_cloudy', 'cloudy', 'rain', 'storm', 'snow', 'moon', 'moon_cloudy',
		);
		foreach ( $input as $key => $value ) {
			$key = sanitize_key( $key );
			if ( in_array( $key, $valid_keys, true ) ) {
				$sanitized[ $key ] = esc_url_raw( $value );
			}
		}
		return $sanitized;
	}
}

if ( ! function_exists( 'telex_dpw_sanitize_api_providers' ) ) {
	/**
	 * Sanitizes the API providers option.
	 *
	 * @param mixed $input Raw input.
	 * @return array{primary: string, retries: int, providers: array<string, array{enabled: bool, api_key: string}>}
	 */
	function telex_dpw_sanitize_api_providers( $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$valid_providers = array( 'open_meteo', 'openweather', 'google_weather' );
		$primary         = sanitize_key( $input['primary'] ?? 'open_meteo' );
		if ( ! in_array( $primary, $valid_providers, true ) ) {
			$primary = 'open_meteo';
		}
		$retries = absint( $input['retries'] ?? 2 );
		if ( $retries > 10 ) {
			$retries = 10;
		}
		$providers = array();
		foreach ( $valid_providers as $key ) {
			$p = $input['providers'][ $key ] ?? array();
			$providers[ $key ] = array(
				'enabled' => ! empty( $p['enabled'] ),
				'api_key' => sanitize_text_field( $p['api_key'] ?? '' ),
			);
		}
		return array(
			'primary'   => $primary,
			'retries'   => $retries,
			'providers' => $providers,
		);
	}
}

if ( ! function_exists( 'telex_dpw_add_admin_menu' ) ) {
	/**
	 * Adds the settings page to the admin menu.
	 *
	 * @return void
	 */
	function telex_dpw_add_admin_menu(): void {
		add_options_page(
			__( 'Dog Park Weather', 'telex-dog-park-weather' ),
			__( 'Dog Park Weather', 'telex-dog-park-weather' ),
			'manage_options',
			'telex-dpw-settings',
			'telex_dpw_render_settings_page'
		);
	}
	add_action( 'admin_menu', 'telex_dpw_add_admin_menu' );
}

if ( ! function_exists( 'telex_dpw_plugin_action_links' ) ) {
	/**
	 * Adds a "Settings" link on the Plugins list screen, next to
	 * Activate/Deactivate.
	 *
	 * @param array<int, string> $links Existing action links.
	 * @return array<int, string>
	 */
	function telex_dpw_plugin_action_links( array $links ): array {
		$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=telex-dpw-settings' ) ) . '">' . esc_html__( 'Settings', 'telex-dog-park-weather' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'telex_dpw_plugin_action_links' );
}

if ( ! function_exists( 'telex_dpw_admin_enqueue' ) ) {
	/**
	 * Enqueues admin settings page assets.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	function telex_dpw_admin_enqueue( string $hook ): void {
		if ( 'settings_page_telex-dpw-settings' !== $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style(
			'telex-dpw-admin',
			plugin_dir_url( __FILE__ ) . 'admin-style.css',
			array(),
			'0.12.0'
		);
		wp_enqueue_script(
			'telex-dpw-admin',
			plugin_dir_url( __FILE__ ) . 'admin-script.js',
			array( 'jquery' ),
			'0.12.0',
			true
		);
		wp_localize_script(
			'telex-dpw-admin',
			'telexDpwAdmin',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'translateNonce' => wp_create_nonce( 'telex_dpw_translate_strings' ),
				'greekPreset'  => telex_dpw_get_greek_preset(),
			)
		);
	}
	add_action( 'admin_enqueue_scripts', 'telex_dpw_admin_enqueue' );
}

if ( ! function_exists( 'telex_dpw_get_greek_preset' ) ) {
	/**
	 * A hand-written Greek translation of every default UI string, in the
	 * same playful/informal voice as the English defaults. Offered as a
	 * one-click starting point on the Translations tab - still fully
	 * editable afterward, and nothing is saved until "Save Changes" is
	 * pressed.
	 *
	 * @return array<string, string>
	 */
	function telex_dpw_get_greek_preset(): array {
		return array(
			'now'             => 'Τώρα',
			'today'           => 'Σήμερα',
			'tomorrow'        => 'Αύριο',
			'hourly_forecast' => 'Πρόγνωση ανά ώρα',
			'top_hour'        => 'ΚΑΛΥΤΕΡΗ ΩΡΑ',
			'rain'            => 'Βροχή',
			'wind'            => 'Άνεμος',
			'uv'              => 'UV',
			'cloud'           => 'Συννεφιά',
			'temp'            => 'Θερμοκρασία',
			'darkness'        => 'Σκοτάδι',
			'am'              => 'πμ',
			'pm'              => 'μμ',
			'rating_5'        => 'Υπέροχος καιρός για σκυλίτσα! Ώρα για μεγάλη βόλτα-σαφάρι.',
			'rating_4'        => 'Πολύ καλές συνθήκες για το πάρκο!',
			'rating_3'        => 'Αξιοπρεπής καιρός, κράτα το μάτι σου ανοιχτό.',
			'rating_2'        => 'Όχι ιδανικά, ίσως μια σύντομη βόλτα.',
			'rating_1'        => 'Καλύτερα να μείνετε σπίτι για αγκαλίτσες.',
			'good_drainage'   => 'Καλή Αποστράγγιση',
			'good_shade'      => 'Καλή Σκιά',
			'wind_shelter'    => 'Προστασία από Άνεμο',
			'covered_areas'   => 'Σκεπασμένοι Χώροι',
			'select_park'     => 'Επίλεξε πάρκο',
			'no_parks'        => 'Δεν έχουν ρυθμιστεί πάρκα. Πήγαινε στις Ρυθμίσεις → Dog Park Weather για να προσθέσεις ένα.',
			'loading'         => 'Φόρτωση δεδομένων καιρού...',
			'error'           => 'Δεν ήταν δυνατή η φόρτωση των δεδομένων καιρού.',
			'feels_like'      => 'Αίσθηση σαν',
			'powered_by'      => 'Powered by',
			'powered_by_and'  => 'και',
			'beaufort_0'      => 'Άπνοια',
			'beaufort_1'      => 'Ασθενής άνεμος',
			'beaufort_2'      => 'Ελαφρύ αεράκι',
			'beaufort_3'      => 'Ήπιο αεράκι',
			'beaufort_4'      => 'Μέτριος άνεμος',
			'beaufort_5'      => 'Φρέσκος άνεμος',
			'beaufort_6'      => 'Ισχυρός άνεμος',
			'beaufort_7'      => 'Σχεδόν θύελλα',
			'beaufort_8'      => 'Θύελλα',
			'beaufort_9'      => 'Ισχυρή θύελλα',
			'beaufort_10'     => 'Καταιγίδα',
			'beaufort_11'     => 'Σφοδρή καταιγίδα',
			'beaufort_12'     => 'Τυφώνας',
			'reason_cold_extreme' => 'Επικίνδυνο κρύο',
			'reason_hot_extreme'  => 'Επικίνδυνη ζέστη',
			'reason_severe'       => 'Προειδοποίηση έντονων καιρικών φαινομένων',
			'reason_rain'         => 'Πιθανότητα βροχής',
			'reason_uv'           => 'Υψηλή υπεριώδης ακτινοβολία',
			'reason_wind'         => 'Δυνατός άνεμος',
			'reason_temp'         => 'Δυσάρεστη θερμοκρασία',
			'reason_cloud'        => 'Συννεφιά',
			'reason_dark'         => 'Έχει σκοτεινιάσει',
			'reason_ideal'        => 'Ιδανικές συνθήκες',
			'aria_rating_label'   => '%d από 5 πατούσες',
		);
	}
}

if ( ! function_exists( 'telex_dpw_translate_via_deepl' ) ) {
	/**
	 * Translates every string in $defaults to $target using DeepL, in a
	 * single batched request (DeepL accepts multiple repeated `text=`
	 * params per call, unlike MyMemory which needs one request per string).
	 * Tries both the free and Pro API hosts, since a key only works against
	 * one of the two and there's no way to tell which from the key itself.
	 *
	 * @param array<string, string> $defaults English source strings, keyed.
	 * @param string                $target   Two-letter target language code (lowercase).
	 * @param string                $deepl_key DeepL API key.
	 * @return array<string, string>|null Translated strings keyed the same as $defaults, or null on failure.
	 */
	function telex_dpw_translate_via_deepl( array $defaults, string $target, string $deepl_key ) {
		$lang_map = array(
			'el' => 'EL',
			'es' => 'ES',
			'fr' => 'FR',
			'de' => 'DE',
			'it' => 'IT',
			'pt' => 'PT-PT',
			'nl' => 'NL',
			'tr' => 'TR',
			'ro' => 'RO',
			'bg' => 'BG',
		);
		$deepl_target = $lang_map[ $target ] ?? strtoupper( $target );

		$keys  = array_keys( $defaults );
		$texts = array_values( $defaults );

		// Built as a raw string rather than passed as a PHP array: WP's HTTP
		// API serializes array 'body' values as text[0]=...&text[1]=..., but
		// DeepL expects plain repeated text=...&text=... params, so an array
		// here would silently translate nothing.
		$body_parts = array();
		foreach ( $texts as $t ) {
			$body_parts[] = 'text=' . rawurlencode( $t );
		}
		$body_parts[] = 'target_lang=' . rawurlencode( $deepl_target );
		$raw_body     = implode( '&', $body_parts );

		$endpoints = array( 'https://api-free.deepl.com/v2/translate', 'https://api.deepl.com/v2/translate' );

		foreach ( $endpoints as $endpoint ) {
			$response = wp_remote_post(
				$endpoint,
				array(
					'timeout' => 20,
					'headers' => array(
						'Authorization' => 'DeepL-Auth-Key ' . $deepl_key,
						'Content-Type'  => 'application/x-www-form-urlencoded',
					),
					'body'    => $raw_body,
				)
			);

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				continue; // Wrong host for this key (free vs. Pro) - try the other one.
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( empty( $data['translations'] ) || count( $data['translations'] ) !== count( $keys ) ) {
				continue;
			}

			$out = array();
			foreach ( $keys as $i => $key ) {
				$out[ $key ] = html_entity_decode( $data['translations'][ $i ]['text'] ?? '', ENT_QUOTES, 'UTF-8' );
			}
			return $out;
		}

		return null;
	}
}

if ( ! function_exists( 'telex_dpw_translate_via_mymemory' ) ) {
	/**
	 * Translates every string in $defaults to $target using MyMemory's free
	 * API - one request per string, since MyMemory has no batch endpoint.
	 *
	 * @param array<string, string> $defaults English source strings, keyed.
	 * @param string                $target   Two-letter target language code.
	 * @return array{translations: array<string, string>, failures: int}
	 */
	function telex_dpw_translate_via_mymemory( array $defaults, string $target ): array {
		$translated = array();
		$failures   = 0;

		foreach ( $defaults as $key => $text ) {
			$url = add_query_arg(
				array(
					'q'        => rawurlencode( $text ),
					'langpair' => rawurlencode( 'en|' . $target ),
				),
				'https://api.mymemory.translated.net/get'
			);

			$response = wp_remote_get( $url, array( 'timeout' => 8 ) );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				++$failures;
				continue;
			}

			$body = json_decode( wp_remote_retrieve_body( $response ), true );
			$out  = $body['responseData']['translatedText'] ?? '';

			if ( '' === $out || ! is_string( $out ) ) {
				++$failures;
				continue;
			}

			$translated[ $key ] = html_entity_decode( $out, ENT_QUOTES, 'UTF-8' );
		}

		return array(
			'translations' => $translated,
			'failures'     => $failures,
		);
	}
}

if ( ! function_exists( 'telex_dpw_ajax_translate_strings' ) ) {
	/**
	 * AJAX handler for the "Auto-translate" tool on the Translations tab.
	 * Uses DeepL (much better quality) when an API key is supplied, falling
	 * back to MyMemory's free service otherwise or if DeepL fails. Returns
	 * the translated strings for JS to drop into the form fields - nothing
	 * is saved server-side here, so a person can review before hitting the
	 * real Save Changes button.
	 *
	 * @return void
	 */
	function telex_dpw_ajax_translate_strings(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'telex-dog-park-weather' ) ), 403 );
		}
		check_ajax_referer( 'telex_dpw_translate_strings', 'nonce' );

		$target = isset( $_POST['target_lang'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['target_lang'] ) ) : '';
		$style  = isset( $_POST['style'] ) ? sanitize_key( wp_unslash( (string) $_POST['style'] ) ) : 'neutral';
		$target = preg_replace( '/[^a-zA-Z-]/', '', $target );

		// The key can arrive either from the field on this page (not yet
		// saved) or from what was previously saved via Save Changes - the
		// field just submitted takes priority since it's what's on screen.
		$deepl_key = isset( $_POST['deepl_key'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['deepl_key'] ) ) : '';
		if ( '' === $deepl_key ) {
			$deepl_key = get_option( 'telex_dpw_deepl_key', '' );
		}

		if ( '' === $target ) {
			wp_send_json_error( array( 'message' => __( 'Pick a language first.', 'telex-dog-park-weather' ) ) );
		}

		$defaults   = telex_dpw_get_default_translations();
		$used_deepl = false;
		$failures   = 0;

		$translated = null;
		if ( '' !== $deepl_key ) {
			$translated = telex_dpw_translate_via_deepl( $defaults, $target, $deepl_key );
			if ( null !== $translated ) {
				$used_deepl = true;
			}
		}

		if ( null === $translated ) {
			$mymemory_result = telex_dpw_translate_via_mymemory( $defaults, $target );
			$translated       = $mymemory_result['translations'];
			$failures         = $mymemory_result['failures'];
		}

		// "Playful" style is a light touch, not a rewrite: neither service has
		// a style parameter, so this just adds a little warmth to the two
		// most visible, emotionally-toned strings rather than pretending to
		// restyle all 35 of them.
		if ( 'playful' === $style ) {
			foreach ( array( 'rating_5', 'rating_1' ) as $rk ) {
				if ( ! empty( $translated[ $rk ] ) ) {
					$translated[ $rk ] = trim( $translated[ $rk ] ) . ' 🐾';
				}
			}
		}

		if ( empty( $translated ) ) {
			wp_send_json_error( array( 'message' => __( 'The translation service is not responding right now. Try again in a bit, or fill translations in by hand.', 'telex-dog-park-weather' ) ) );
		}

		wp_send_json_success(
			array(
				'translations' => $translated,
				'failures'     => $failures,
				'total'        => count( $defaults ),
				'used_deepl'   => $used_deepl,
				'deepl_failed' => ( '' !== $deepl_key && ! $used_deepl ),
			)
		);
	}
	add_action( 'wp_ajax_telex_dpw_translate_strings', 'telex_dpw_ajax_translate_strings' );
}

if ( ! function_exists( 'telex_dpw_render_settings_page' ) ) {
	/**
	 * Renders the settings page.
	 *
	 * @return void
	 */
	function telex_dpw_render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$active_tab     = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'parks';

		// Each tab now saves through its own settings group instead of one
		// shared group. This matters: WordPress's options.php wipes any
		// registered option to null if that option's fields weren't part of
		// the submitted form - and since each tab only renders its own
		// fields (this is a full-page-reload tab UI, not a JS show/hide
		// one), a single shared group meant saving Translations silently
		// blanked Parks/Weights/Appearance/Icons/API Providers, and vice
		// versa. Splitting into one group per tab means a save can only ever
		// touch the option(s) that tab actually has fields for.
		$settings_groups = array(
			'parks'         => 'telex_dpw_parks_settings',
			'api_providers' => 'telex_dpw_api_providers_settings',
			'translations'  => 'telex_dpw_translations_settings',
			'icons'         => 'telex_dpw_icons_settings',
			'appearance'    => 'telex_dpw_appearance_settings',
			'weights'       => 'telex_dpw_weights_settings',
		);
		$current_settings_group = $settings_groups[ $active_tab ] ?? $settings_groups['parks'];
		$parks          = telex_dpw_get_parks();
		$translations   = telex_dpw_get_translations();
		$defaults       = telex_dpw_get_default_translations();
		$icons          = telex_dpw_get_icons();
		$api_providers  = telex_dpw_get_api_providers();
		$appearance     = telex_dpw_get_appearance();
		$app_defaults   = telex_dpw_get_default_appearance();
		$weights        = telex_dpw_get_weights();
		$weight_defaults = telex_dpw_get_default_weights();

		?>
		<div class="wrap telex-dpw-settings-wrap">
			<h1><?php echo esc_html__( 'Dog Park Weather Settings', 'telex-dog-park-weather' ); ?></h1>

			<nav class="nav-tab-wrapper">
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-dpw-settings&tab=parks' ) ); ?>"
				   class="nav-tab <?php echo 'parks' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html__( 'Parks', 'telex-dog-park-weather' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-dpw-settings&tab=api_providers' ) ); ?>"
				   class="nav-tab <?php echo 'api_providers' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html__( 'API Providers', 'telex-dog-park-weather' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-dpw-settings&tab=translations' ) ); ?>"
				   class="nav-tab <?php echo 'translations' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html__( 'Translations', 'telex-dog-park-weather' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-dpw-settings&tab=icons' ) ); ?>"
				   class="nav-tab <?php echo 'icons' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html__( 'Icons', 'telex-dog-park-weather' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-dpw-settings&tab=appearance' ) ); ?>"
				   class="nav-tab <?php echo 'appearance' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html__( 'Appearance', 'telex-dog-park-weather' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=telex-dpw-settings&tab=weights' ) ); ?>"
				   class="nav-tab <?php echo 'weights' === $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html__( 'Weights', 'telex-dog-park-weather' ); ?>
				</a>
			</nav>

			<?php if ( 'parks' === $active_tab ) : ?>
				<?php
				// This whole block - notices plus the CSV importer's own <form> -
				// deliberately sits OUTSIDE the main settings <form> below. A
				// <form> can never be nested inside another <form> in valid
				// HTML; browsers "fix" that by silently breaking one of the two
				// forms, which is exactly what was making the main Save Changes
				// button stop working on this tab.
				if ( isset( $_GET['imported'] ) ) {
					$imported_n   = (int) $_GET['imported'];
					$skipped_n    = isset( $_GET['skipped'] ) ? (int) $_GET['skipped'] : 0;
					$error_reason = isset( $_GET['importerror'] ) ? sanitize_key( wp_unslash( (string) $_GET['importerror'] ) ) : '0';
					$error_reason = ( '0' === $error_reason ) ? '' : $error_reason;

					$error_messages = array(
						'empty'         => __( 'Nothing to import: both the link and the paste box were empty. Fill in one of them and try again.', 'telex-dog-park-weather' ),
						'fetch_failed'  => __( "Couldn't reach that Google Sheet link - it likely needs a Google sign-in, which this can't do for you. Use Option B (paste CSV) instead.", 'telex-dog-park-weather' ),
						'no_columns'    => __( 'Imported nothing: the header row doesn\'t have columns this can recognize. It needs a name column ("Park Name / Area" or "Name") and a coordinates column ("Municipality coordinates for weather reports", or separate "Latitude"/"Longitude" columns).', 'telex-dog-park-weather' ),
						'no_valid_rows' => __( "The columns looked right, but no row had usable coordinates (e.g. cells that say \"Pending\" instead of numbers). Double-check the coordinate cells and try again.", 'telex-dog-park-weather' ),
					);

					if ( $error_reason && 0 === $imported_n ) :
						?>
						<div class="notice notice-error"><p>
							<?php echo esc_html( $error_messages[ $error_reason ] ?? __( "Couldn't import anything - please try Option B (paste CSV) directly.", 'telex-dog-park-weather' ) ); ?>
						</p></div>
						<?php
					else :
						?>
						<div class="notice notice-success"><p>
							<?php
							echo esc_html(
								sprintf(
									/* translators: 1: number of parks added, 2: number of parks skipped as duplicates */
									_n( 'Added %1$d park (%2$d already existed and were skipped).', 'Added %1$d parks (%2$d already existed and were skipped).', $imported_n, 'telex-dog-park-weather' ),
									$imported_n,
									$skipped_n
								)
							);
							?>
						</p></div>
						<?php
					endif;
				}
				?>

				<details class="telex-dpw-import-box">
					<summary><strong><?php echo esc_html__( 'Import Parks from a spreadsheet', 'telex-dog-park-weather' ); ?></strong></summary>
					<div class="telex-dpw-import-box-inner">
						<p class="description">
							<?php echo esc_html__( 'Adds new parks by name + coordinates. Parks that already exist (matched by name) are left as-is, never overwritten or duplicated.', 'telex-dog-park-weather' ); ?>
						</p>
						<p class="description">
							<?php echo esc_html__( 'Expected columns: "Park Name / Area" and "Municipality coordinates for weather reports" (as "lat, lon"). Plain "Name", "Latitude", "Longitude" also works.', 'telex-dog-park-weather' ); ?>
						</p>
						<p class="description">
							<?php echo esc_html__( 'Optional columns, if present, also set the four park mitigations: "Good Drainage", "Good Shade", "Wind Shelter", "Covered Areas" (or just "Drainage"/"Shade"/"Shelter"/"Covered"). Any of Yes / Y / True / 1 / Ναι counts as checked; anything else, including a blank cell, leaves it unchecked. Columns your sheet doesn\'t have simply stay unchecked, same as adding a park by hand.', 'telex-dog-park-weather' ); ?>
						</p>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<?php wp_nonce_field( 'telex_dpw_import_parks' ); ?>
							<input type="hidden" name="action" value="telex_dpw_import_parks" />

							<p>
								<label for="telex_dpw_import_sheet_url"><strong><?php echo esc_html__( 'Option A: A public Google Sheet link', 'telex-dog-park-weather' ); ?></strong></label><br />
								<input type="url" id="telex_dpw_import_sheet_url" name="telex_dpw_import_sheet_url" class="large-text" placeholder="https://docs.google.com/spreadsheets/d/.../edit" />
								<span class="description">
									<?php echo esc_html__( 'Works with two kinds of link:', 'telex-dog-park-weather' ); ?>
									<br />1) <?php echo esc_html__( 'Share → General access → "Anyone with the link" → Viewer, then paste that link.', 'telex-dog-park-weather' ); ?>
									<br />2) <?php echo esc_html__( 'File → Share → Publish to web → CSV → Publish, then paste that link. This one works even if your organization blocks "Anyone with the link" sharing.', 'telex-dog-park-weather' ); ?>
									<br /><?php echo esc_html__( "If your Sheet needs a Google sign-in either way, this option can't reach it - use Option B instead.", 'telex-dog-park-weather' ); ?>
								</span>
							</p>

							<p>
								<label for="telex_dpw_import_csv"><strong><?php echo esc_html__( 'Option B: Paste CSV directly (always works)', 'telex-dog-park-weather' ); ?></strong></label><br />
								<span class="description"><?php echo esc_html__( 'In Google Sheets: File → Download → Comma Separated Values (.csv). Open that downloaded file in a text editor (not Excel/Sheets), select all, copy, and paste it into the box below.', 'telex-dog-park-weather' ); ?></span><br />
								<textarea id="telex_dpw_import_csv" name="telex_dpw_import_csv" rows="6" class="large-text code" placeholder="Park Name / Area,Municipality coordinates for weather reports&#10;Serafeio Flagship Park (144 Piraeus St),&quot;37.997200, 23.738100&quot;"></textarea>
							</p>

							<p class="submit">
								<button type="submit" class="button button-primary"><?php echo esc_html__( 'Import Parks', 'telex-dog-park-weather' ); ?></button>
							</p>
						</form>
					</div>
				</details>
			<?php endif; ?>

			<form method="post" action="options.php" id="telex-dpw-form">
				<?php settings_fields( $current_settings_group ); ?>

				<?php if ( 'parks' === $active_tab ) : ?>
					<div class="telex-dpw-tab-content">
						<h2><?php echo esc_html__( 'Manage Parks', 'telex-dog-park-weather' ); ?></h2>
						<p class="description"><?php echo esc_html__( 'Add your dog parks with their coordinates and conditions.', 'telex-dog-park-weather' ); ?></p>

						<div id="telex-dpw-parks-list">
							<?php if ( ! empty( $parks ) ) : ?>
								<?php foreach ( $parks as $index => $park ) : ?>
									<div class="telex-dpw-park-row" data-index="<?php echo esc_attr( (string) $index ); ?>">
										<div class="telex-dpw-park-header">
											<strong><?php echo esc_html( $park['name'] ); ?></strong>
											<button type="button" class="button telex-dpw-remove-park"><?php echo esc_html__( 'Remove', 'telex-dog-park-weather' ); ?></button>
										</div>
										<input type="hidden" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][id]" value="<?php echo esc_attr( $park['id'] ); ?>" />
										<table class="form-table">
											<tr>
												<th><label><?php echo esc_html__( 'Park Name', 'telex-dog-park-weather' ); ?></label></th>
												<td><input type="text" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][name]" value="<?php echo esc_attr( $park['name'] ); ?>" class="regular-text" required /></td>
											</tr>
											<tr>
												<th><label><?php echo esc_html__( 'Latitude', 'telex-dog-park-weather' ); ?></label></th>
												<td><input type="text" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][lat]" value="<?php echo esc_attr( $park['lat'] ); ?>" class="regular-text" placeholder="37.9838" required /></td>
											</tr>
											<tr>
												<th><label><?php echo esc_html__( 'Longitude', 'telex-dog-park-weather' ); ?></label></th>
												<td><input type="text" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][lon]" value="<?php echo esc_attr( $park['lon'] ); ?>" class="regular-text" placeholder="23.7275" required /></td>
											</tr>
											<tr>
												<th><?php echo esc_html__( 'Conditions', 'telex-dog-park-weather' ); ?></th>
												<td>
													<label><input type="checkbox" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][drainage]" value="1" <?php checked( ! empty( $park['drainage'] ) ); ?> /> <?php echo esc_html__( 'Good Drainage (halves rain penalty)', 'telex-dog-park-weather' ); ?></label><br />
													<label><input type="checkbox" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][shade]" value="1" <?php checked( ! empty( $park['shade'] ) ); ?> /> <?php echo esc_html__( 'Good Shade (halves UV penalty)', 'telex-dog-park-weather' ); ?></label><br />
													<label><input type="checkbox" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][wind_shelter]" value="1" <?php checked( ! empty( $park['wind_shelter'] ) ); ?> /> <?php echo esc_html__( 'Wind Shelter (halves wind penalty)', 'telex-dog-park-weather' ); ?></label><br />
													<label><input type="checkbox" name="telex_dpw_parks[<?php echo esc_attr( (string) $index ); ?>][covered]" value="1" <?php checked( ! empty( $park['covered'] ) ); ?> /> <?php echo esc_html__( 'Covered Areas (halves cloud/cold penalty)', 'telex-dog-park-weather' ); ?></label>
												</td>
											</tr>
										</table>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>

						<p>
							<button type="button" class="button button-secondary" id="telex-dpw-add-park">
								<?php echo esc_html__( '+ Add Park', 'telex-dog-park-weather' ); ?>
							</button>
						</p>
					</div>

				<?php elseif ( 'api_providers' === $active_tab ) : ?>
					<div class="telex-dpw-tab-content">
						<h2><?php echo esc_html__( 'API Providers', 'telex-dog-park-weather' ); ?></h2>
						<p class="description"><?php echo esc_html__( 'Configure weather API providers. The primary provider is tried first. If it fails after the configured number of retries, the next enabled provider is used as a fallback.', 'telex-dog-park-weather' ); ?></p>

						<table class="form-table">
							<tr>
								<th><label for="telex_dpw_api_primary"><?php echo esc_html__( 'Primary Provider', 'telex-dog-park-weather' ); ?></label></th>
								<td>
									<select name="telex_dpw_api_providers[primary]" id="telex_dpw_api_primary">
										<option value="open_meteo" <?php selected( $api_providers['primary'], 'open_meteo' ); ?>><?php echo esc_html__( 'Open-Meteo (free, no key required)', 'telex-dog-park-weather' ); ?></option>
										<option value="openweather" <?php selected( $api_providers['primary'], 'openweather' ); ?>><?php echo esc_html__( 'OpenWeatherMap', 'telex-dog-park-weather' ); ?></option>
										<option value="google_weather" <?php selected( $api_providers['primary'], 'google_weather' ); ?>><?php echo esc_html__( 'Google Weather API', 'telex-dog-park-weather' ); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th><label for="telex_dpw_api_retries"><?php echo esc_html__( 'Retries Before Fallback', 'telex-dog-park-weather' ); ?></label></th>
								<td>
									<input type="number" name="telex_dpw_api_providers[retries]" id="telex_dpw_api_retries" value="<?php echo esc_attr( (string) $api_providers['retries'] ); ?>" min="1" max="10" class="small-text" />
									<p class="description"><?php echo esc_html__( 'Number of attempts on the primary provider before switching to the fallback.', 'telex-dog-park-weather' ); ?></p>
								</td>
							</tr>
						</table>

						<hr />

						<h3><?php echo esc_html__( 'Open-Meteo', 'telex-dog-park-weather' ); ?></h3>
						<table class="form-table">
							<tr>
								<th><?php echo esc_html__( 'Enabled', 'telex-dog-park-weather' ); ?></th>
								<td>
									<label><input type="checkbox" name="telex_dpw_api_providers[providers][open_meteo][enabled]" value="1" <?php checked( ! empty( $api_providers['providers']['open_meteo']['enabled'] ) ); ?> /> <?php echo esc_html__( 'Enable Open-Meteo', 'telex-dog-park-weather' ); ?></label>
									<p class="description"><?php echo esc_html__( 'Free API, no key required. Ideal as a no-cost fallback.', 'telex-dog-park-weather' ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label><?php echo esc_html__( 'API Key (optional)', 'telex-dog-park-weather' ); ?></label></th>
								<td>
									<input type="text" name="telex_dpw_api_providers[providers][open_meteo][api_key]" value="<?php echo esc_attr( $api_providers['providers']['open_meteo']['api_key'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr__( 'Not required for free tier', 'telex-dog-park-weather' ); ?>" />
								</td>
							</tr>
						</table>

						<hr />

						<h3><?php echo esc_html__( 'OpenWeatherMap', 'telex-dog-park-weather' ); ?></h3>
						<table class="form-table">
							<tr>
								<th><?php echo esc_html__( 'Enabled', 'telex-dog-park-weather' ); ?></th>
								<td>
									<label><input type="checkbox" name="telex_dpw_api_providers[providers][openweather][enabled]" value="1" <?php checked( ! empty( $api_providers['providers']['openweather']['enabled'] ) ); ?> /> <?php echo esc_html__( 'Enable OpenWeatherMap', 'telex-dog-park-weather' ); ?></label>
									<p class="description"><?php echo esc_html__( 'Requires a free or paid API key from openweathermap.org.', 'telex-dog-park-weather' ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label><?php echo esc_html__( 'API Key', 'telex-dog-park-weather' ); ?></label></th>
								<td>
									<input type="text" name="telex_dpw_api_providers[providers][openweather][api_key]" value="<?php echo esc_attr( $api_providers['providers']['openweather']['api_key'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr__( 'Enter your OpenWeatherMap API key', 'telex-dog-park-weather' ); ?>" />
								</td>
							</tr>
						</table>

						<hr />

						<h3><?php echo esc_html__( 'Google Weather API', 'telex-dog-park-weather' ); ?></h3>
						<table class="form-table">
							<tr>
								<th><?php echo esc_html__( 'Enabled', 'telex-dog-park-weather' ); ?></th>
								<td>
									<label><input type="checkbox" name="telex_dpw_api_providers[providers][google_weather][enabled]" value="1" <?php checked( ! empty( $api_providers['providers']['google_weather']['enabled'] ) ); ?> /> <?php echo esc_html__( 'Enable Google Weather API', 'telex-dog-park-weather' ); ?></label>
									<p class="description"><?php echo esc_html__( 'Requires a Google Cloud API key with the Weather API enabled.', 'telex-dog-park-weather' ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label><?php echo esc_html__( 'API Key', 'telex-dog-park-weather' ); ?></label></th>
								<td>
									<input type="text" name="telex_dpw_api_providers[providers][google_weather][api_key]" value="<?php echo esc_attr( $api_providers['providers']['google_weather']['api_key'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr__( 'Enter your Google Weather API key', 'telex-dog-park-weather' ); ?>" />
								</td>
							</tr>
						</table>
					</div>

				<?php elseif ( 'translations' === $active_tab ) : ?>
					<div class="telex-dpw-tab-content">
						<h2><?php echo esc_html__( 'Translations', 'telex-dog-park-weather' ); ?></h2>
						<p class="description"><?php echo esc_html__( 'Override the default English strings with your own wording (e.g., Greek). Nothing below is saved until you click Save Changes at the bottom.', 'telex-dog-park-weather' ); ?></p>

						<div class="telex-dpw-translate-tools">
							<div class="telex-dpw-translate-tools-row">
								<button type="button" class="button" id="telex-dpw-use-greek-preset">
									🇬🇷 <?php echo esc_html__( 'Use Greek preset', 'telex-dog-park-weather' ); ?>
								</button>
								<span class="description"><?php echo esc_html__( 'Hand-written Greek in the same playful tone as the English defaults.', 'telex-dog-park-weather' ); ?></span>
							</div>

							<div class="telex-dpw-translate-tools-row">
								<label for="telex-dpw-translate-lang"><strong><?php echo esc_html__( 'Auto-translate to:', 'telex-dog-park-weather' ); ?></strong></label>
								<select id="telex-dpw-translate-lang">
									<option value="el"><?php echo esc_html__( 'Greek', 'telex-dog-park-weather' ); ?></option>
									<option value="es"><?php echo esc_html__( 'Spanish', 'telex-dog-park-weather' ); ?></option>
									<option value="fr"><?php echo esc_html__( 'French', 'telex-dog-park-weather' ); ?></option>
									<option value="de"><?php echo esc_html__( 'German', 'telex-dog-park-weather' ); ?></option>
									<option value="it"><?php echo esc_html__( 'Italian', 'telex-dog-park-weather' ); ?></option>
									<option value="pt"><?php echo esc_html__( 'Portuguese', 'telex-dog-park-weather' ); ?></option>
									<option value="nl"><?php echo esc_html__( 'Dutch', 'telex-dog-park-weather' ); ?></option>
									<option value="tr"><?php echo esc_html__( 'Turkish', 'telex-dog-park-weather' ); ?></option>
									<option value="ro"><?php echo esc_html__( 'Romanian', 'telex-dog-park-weather' ); ?></option>
									<option value="bg"><?php echo esc_html__( 'Bulgarian', 'telex-dog-park-weather' ); ?></option>
								</select>
								<select id="telex-dpw-translate-style">
									<option value="neutral"><?php echo esc_html__( 'Neutral style', 'telex-dog-park-weather' ); ?></option>
									<option value="playful"><?php echo esc_html__( 'Playful style', 'telex-dog-park-weather' ); ?></option>
								</select>
								<button type="button" class="button button-primary" id="telex-dpw-auto-translate">
									<?php echo esc_html__( 'Translate All', 'telex-dog-park-weather' ); ?>
								</button>
								<span id="telex-dpw-translate-status" class="description"></span>
							</div>
							<div class="telex-dpw-translate-tools-row">
								<label for="telex-dpw-deepl-key"><strong><?php echo esc_html__( 'DeepL API key (optional):', 'telex-dog-park-weather' ); ?></strong></label>
								<input type="text"
									   id="telex-dpw-deepl-key"
									   name="telex_dpw_deepl_key"
									   value="<?php echo esc_attr( get_option( 'telex_dpw_deepl_key', '' ) ); ?>"
									   class="regular-text"
									   placeholder="<?php echo esc_attr__( 'Leave blank to use the free service above', 'telex-dog-park-weather' ); ?>" />
							</div>
							<p class="description">
								<?php echo esc_html__( 'If you have a DeepL API key (free or Pro), paste it here - "Translate All" will use DeepL instead of the free service, and quality is noticeably better. The key is saved with the rest of your settings when you click Save Changes at the bottom.', 'telex-dog-park-weather' ); ?>
							</p>
							<p class="description">
								<?php echo esc_html__( 'Uses a free machine translation service (MyMemory) when no DeepL key is set - good for a fast starting point, not a substitute for a native speaker\'s review. "Playful style" only lightly touches the two rating messages; everything else is a straight translation.', 'telex-dog-park-weather' ); ?>
							</p>
						</div>

						<table class="form-table">
							<?php foreach ( $defaults as $key => $default_value ) : ?>
								<tr>
									<th>
										<label for="telex_dpw_trans_<?php echo esc_attr( $key ); ?>">
											<?php echo esc_html( $key ); ?>
										</label>
										<p class="description" style="font-weight:normal;font-style:italic;"><?php echo esc_html( $default_value ); ?></p>
									</th>
									<td>
										<input type="text"
											   id="telex_dpw_trans_<?php echo esc_attr( $key ); ?>"
											   name="telex_dpw_translations[<?php echo esc_attr( $key ); ?>]"
											   value="<?php echo esc_attr( $translations[ $key ] ?? '' ); ?>"
											   class="regular-text"
											   placeholder="<?php echo esc_attr( $default_value ); ?>" />
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>

				<?php elseif ( 'icons' === $active_tab ) : ?>
					<div class="telex-dpw-tab-content">
						<h2><?php echo esc_html__( 'Custom Icons (Lottie JSON)', 'telex-dog-park-weather' ); ?></h2>
						<p class="description"><?php echo esc_html__( 'The plugin ships with a full set of animated icons out of the box (colors follow the Icon Outline / Icon Accent colors on the Appearance tab). WordPress blocks .json uploads through the Media Library by default, so the fields below point at the bundled files rather than an upload; paste a different URL here only if you want to self-host a replacement icon.', 'telex-dog-park-weather' ); ?></p>

						<table class="form-table">
							<?php
							$icon_labels = array(
								'paw_active'    => __( 'Paw (Active/Filled)', 'telex-dog-park-weather' ),
								'paw_inactive'  => __( 'Paw (Inactive/Greyed)', 'telex-dog-park-weather' ),
								'sun'           => __( 'Sun (Clear Sky)', 'telex-dog-park-weather' ),
								'partly_cloudy' => __( 'Partly Cloudy', 'telex-dog-park-weather' ),
								'cloudy'        => __( 'Cloudy', 'telex-dog-park-weather' ),
								'rain'          => __( 'Rain', 'telex-dog-park-weather' ),
								'storm'         => __( 'Storm / Thunderstorm', 'telex-dog-park-weather' ),
								'snow'          => __( 'Snow', 'telex-dog-park-weather' ),
								'moon'          => __( 'Moon (Clear Night)', 'telex-dog-park-weather' ),
								'moon_cloudy'   => __( 'Moon Cloudy (Night Cloudy)', 'telex-dog-park-weather' ),
							);
							foreach ( $icon_labels as $icon_key => $icon_label ) : ?>
								<tr>
									<th><label><?php echo esc_html( $icon_label ); ?></label></th>
									<td>
										<input type="url"
											   name="telex_dpw_icons[<?php echo esc_attr( $icon_key ); ?>]"
											   value="<?php echo esc_url( $icons[ $icon_key ] ?? '' ); ?>"
											   class="regular-text telex-dpw-icon-url"
											   placeholder="https://..." />
										<button type="button" class="button telex-dpw-upload-icon" data-target="<?php echo esc_attr( $icon_key ); ?>">
											<?php echo esc_html__( 'Upload', 'telex-dog-park-weather' ); ?>
										</button>
										<button type="button" class="button telex-dpw-clear-icon" data-target="<?php echo esc_attr( $icon_key ); ?>">
											<?php echo esc_html__( 'Clear', 'telex-dog-park-weather' ); ?>
										</button>
										<?php if ( ! empty( $icons[ $icon_key ] ) ) : ?>
											<p class="description"><?php echo esc_html__( 'Current:', 'telex-dog-park-weather' ); ?> <?php echo esc_html( basename( $icons[ $icon_key ] ) ); ?></p>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				<?php elseif ( 'appearance' === $active_tab ) : ?>
					<div class="telex-dpw-tab-content">
						<h2><?php echo esc_html__( 'Appearance', 'telex-dog-park-weather' ); ?></h2>
						<p class="description"><?php echo esc_html__( 'Customize all colors and the font used by the block.', 'telex-dog-park-weather' ); ?></p>

						<table class="form-table">
							<?php
							$appearance_labels = array(
								'header_gradient_start' => __( 'Header Gradient Start', 'telex-dog-park-weather' ),
								'header_gradient_end'   => __( 'Header Gradient End', 'telex-dog-park-weather' ),
								'rating_gradient_start' => __( 'Rating Banner Gradient Start', 'telex-dog-park-weather' ),
								'rating_gradient_end'   => __( 'Rating Banner Gradient End', 'telex-dog-park-weather' ),
								'tab_active_bg'         => __( 'Tab Active Background', 'telex-dog-park-weather' ),
								'tab_active_text'       => __( 'Tab Active Text', 'telex-dog-park-weather' ),
								'tab_border'            => __( 'Tab Border', 'telex-dog-park-weather' ),
								'card_bg'               => __( 'Card Background', 'telex-dog-park-weather' ),
								'row_bg'                => __( 'Row Background', 'telex-dog-park-weather' ),
								'row_border_5'          => __( 'Row Border (5 paws)', 'telex-dog-park-weather' ),
								'row_border_4'          => __( 'Row Border (4 paws)', 'telex-dog-park-weather' ),
								'row_border_3'          => __( 'Row Border (3 paws)', 'telex-dog-park-weather' ),
								'row_border_2'          => __( 'Row Border (2 paws)', 'telex-dog-park-weather' ),
								'row_border_1'          => __( 'Row Border (1 paw)', 'telex-dog-park-weather' ),
								'now_row_bg_start'      => __( 'Now Row Gradient Start', 'telex-dog-park-weather' ),
								'now_row_bg_end'        => __( 'Now Row Gradient End', 'telex-dog-park-weather' ),
								'text_color'            => __( 'Text Color', 'telex-dog-park-weather' ),
								'header_text'           => __( 'Header Text Color', 'telex-dog-park-weather' ),
								'paw_active_color'      => __( 'Paw Active Color', 'telex-dog-park-weather' ),
								'paw_inactive_color'    => __( 'Paw Inactive Color', 'telex-dog-park-weather' ),
								'icon_outline_color'    => __( 'Weather Icon Outline Color', 'telex-dog-park-weather' ),
								'icon_accent_color'     => __( 'Weather Icon Accent Color', 'telex-dog-park-weather' ),
								'section_title_color'   => __( 'Section Title Color', 'telex-dog-park-weather' ),
								'powered_by_color'      => __( 'Powered By Color', 'telex-dog-park-weather' ),
							);
							foreach ( $appearance_labels as $akey => $alabel ) : ?>
								<tr>
									<th><label for="telex_dpw_app_<?php echo esc_attr( $akey ); ?>"><?php echo esc_html( $alabel ); ?></label></th>
									<td>
										<input type="text"
											   id="telex_dpw_app_<?php echo esc_attr( $akey ); ?>"
											   name="telex_dpw_appearance[<?php echo esc_attr( $akey ); ?>]"
											   value="<?php echo esc_attr( $appearance[ $akey ] ?? '' ); ?>"
											   class="regular-text telex-dpw-color-field"
											   placeholder="<?php echo esc_attr( $app_defaults[ $akey ] ?? '' ); ?>" />
										<input type="color" class="telex-dpw-color-picker" value="<?php echo esc_attr( $appearance[ $akey ] ?? $app_defaults[ $akey ] ); ?>" />
									</td>
								</tr>
							<?php endforeach; ?>
							<tr>
								<th><label for="telex_dpw_app_font_family"><?php echo esc_html__( 'Font Family', 'telex-dog-park-weather' ); ?></label></th>
								<td>
									<input type="text"
										   id="telex_dpw_app_font_family"
										   name="telex_dpw_appearance[font_family]"
										   value="<?php echo esc_attr( $appearance['font_family'] ?? '' ); ?>"
										   class="large-text"
										   placeholder="<?php echo esc_attr( $app_defaults['font_family'] ); ?>" />
									<p class="description"><?php echo esc_html__( 'CSS font-family value. Use commas to separate fallbacks.', 'telex-dog-park-weather' ); ?></p>
								</td>
							</tr>
							<tr>
								<th><label for="telex_dpw_app_block_height"><?php echo esc_html__( 'Block Height', 'telex-dog-park-weather' ); ?></label></th>
								<td>
									<input type="number"
										   id="telex_dpw_app_block_height"
										   name="telex_dpw_appearance[block_height]"
										   value="<?php echo esc_attr( $appearance['block_height'] ?? $app_defaults['block_height'] ); ?>"
										   min="300" max="2000" step="10"
										   class="small-text" /> px
									<p class="description"><?php echo esc_html__( 'Total height of the widget. The header, rating banner, and Today/Tomorrow tabs stay fixed; the hourly forecast list scrolls inside the remaining space. Default: 780px.', 'telex-dog-park-weather' ); ?></p>
								</td>
							</tr>
						</table>
					</div>

				<?php elseif ( 'weights' === $active_tab ) : ?>
					<div class="telex-dpw-tab-content">
						<h2><?php echo esc_html__( 'Condition Weights', 'telex-dog-park-weather' ); ?></h2>

						<div class="telex-dpw-weights-guide">
							<p><strong><?php echo esc_html__( 'What this page does:', 'telex-dog-park-weather' ); ?></strong> <?php echo esc_html__( 'each slider below controls how much ONE thing (rain, wind, etc.) can pull the paw rating down.', 'telex-dog-park-weather' ); ?></p>
							<ul>
								<li><strong>0</strong> = <?php echo esc_html__( 'turn it off. This condition never lowers the rating.', 'telex-dog-park-weather' ); ?></li>
								<li><strong>1</strong> = <?php echo esc_html__( 'normal effect. This is the default.', 'telex-dog-park-weather' ); ?></li>
								<li><strong>5</strong> = <?php echo esc_html__( 'big effect. This condition drops the rating fast.', 'telex-dog-park-weather' ); ?></li>
							</ul>
							<p><strong><?php echo esc_html__( 'Two safety rules that ALWAYS apply', 'telex-dog-park-weather' ); ?></strong> (<?php echo esc_html__( 'the sliders below cannot turn these off', 'telex-dog-park-weather' ); ?>):</p>
							<ul>
								<li><?php echo esc_html__( 'Feels-like below 5°C, above 35°C, or a thunderstorm in the forecast → the hour is always shown as 1 paw. "Feels-like" combines temperature, humidity, and wind, so this reacts to real conditions (e.g. muggy heat, or wind chill) rather than just the thermometer.', 'telex-dog-park-weather' ); ?></li>
								<li><?php echo esc_html__( 'Darkness is based on the real sunrise/sunset time for each park, not a fixed clock time.', 'telex-dog-park-weather' ); ?></li>
							</ul>
						</div>

						<table class="form-table">
							<?php
							$weight_labels = array(
								'rain'     => __( 'Rain', 'telex-dog-park-weather' ),
								'uv'       => __( 'UV', 'telex-dog-park-weather' ),
								'wind'     => __( 'Wind', 'telex-dog-park-weather' ),
								'temp'     => __( 'Temperature', 'telex-dog-park-weather' ),
								'cloud'    => __( 'Cloud Cover', 'telex-dog-park-weather' ),
								'darkness' => __( 'Darkness (nighttime)', 'telex-dog-park-weather' ),
							);
							// Short, plain-language "what this actually means" line for each
							// slider, written for a reader who wants the point fast (ADHD) and
							// benefits from plain wording over dense sentences (dyslexia): one
							// short idea per line, concrete rather than abstract.
							$weight_help = array(
								'rain'     => __( 'Rain in the forecast. More rain chance = lower rating.', 'telex-dog-park-weather' ),
								'uv'       => __( 'How strong the sun is. High UV can be rough on paws, noses, and ears. Softened automatically under cloud cover.', 'telex-dog-park-weather' ),
								'wind'     => __( 'Only STRONG wind (above Beaufort 5) counts here. Light and moderate wind is not penalized on its own - see "Temperature" below for how it actually affects comfort.', 'telex-dog-park-weather' ),
								'temp'     => __( 'How uncomfortable it actually feels - not just the thermometer reading. This blends temperature with humidity and wind together (a proper "feels like" number), so a breeze counts as relief on a hot day and as a chill on a cold one, instead of always being treated as bad.', 'telex-dog-park-weather' ),
								'cloud'    => __( 'On a hot day, cloud cover is shade and a small bonus. On a cold day, it just adds to the gloom and is a small penalty. Near room temperature it does not matter either way.', 'telex-dog-park-weather' ),
								'darkness' => __( 'Whether it is dark out, using the real sunrise/sunset time for this park.', 'telex-dog-park-weather' ),
							);
							foreach ( $weight_labels as $wkey => $wlabel ) : ?>
								<tr>
									<th><label for="telex_dpw_w_<?php echo esc_attr( $wkey ); ?>"><?php echo esc_html( $wlabel ); ?></label></th>
									<td>
										<input type="number"
											   id="telex_dpw_w_<?php echo esc_attr( $wkey ); ?>"
											   name="telex_dpw_weights[<?php echo esc_attr( $wkey ); ?>]"
											   value="<?php echo esc_attr( (string) ( $weights[ $wkey ] ?? $weight_defaults[ $wkey ] ) ); ?>"
											   min="0" max="5" step="0.1"
											   class="small-text" />
										<span class="description"><?php echo esc_html( sprintf( __( 'Default: %s', 'telex-dog-park-weather' ), (string) $weight_defaults[ $wkey ] ) ); ?></span>
										<p class="description"><?php echo esc_html( $weight_help[ $wkey ] ); ?></p>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				<?php endif; ?>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}

if ( ! function_exists( 'telex_dpw_register_rest_routes' ) ) {
	/**
	 * Registers REST API routes.
	 *
	 * @return void
	 */
	function telex_dpw_register_rest_routes(): void {
		register_rest_route( 'telex-dpw/v1', '/parks', array(
			'methods'             => 'GET',
			'callback'            => 'telex_dpw_rest_get_parks',
			'permission_callback' => '__return_true',
		) );

		register_rest_route( 'telex-dpw/v1', '/settings', array(
			'methods'             => 'GET',
			'callback'            => 'telex_dpw_rest_get_settings',
			'permission_callback' => '__return_true',
		) );

		register_rest_route( 'telex-dpw/v1', '/weather', array(
			'methods'             => 'GET',
			'callback'            => 'telex_dpw_rest_get_weather',
			'permission_callback' => '__return_true',
			'args'                => array(
				'lat' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'lon' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		) );
	}
	add_action( 'rest_api_init', 'telex_dpw_register_rest_routes' );
}

if ( ! function_exists( 'telex_dpw_rest_get_parks' ) ) {
	/**
	 * REST callback: returns all parks.
	 *
	 * @return WP_REST_Response
	 */
	function telex_dpw_rest_get_parks(): WP_REST_Response {
		return rest_ensure_response( telex_dpw_get_parks() );
	}
}

if ( ! function_exists( 'telex_dpw_rest_get_settings' ) ) {
	/**
	 * REST callback: returns translations, icons, appearance, and weights.
	 *
	 * @return WP_REST_Response
	 */
	function telex_dpw_rest_get_settings(): WP_REST_Response {
		return rest_ensure_response( array(
			'translations'  => telex_dpw_get_translations(),
			'icons'         => telex_dpw_get_icons(),
			'appearance'    => telex_dpw_get_appearance(),
			'weights'       => telex_dpw_get_weights(),
			'api_providers' => telex_dpw_get_api_providers(),
		) );
	}
}

if ( ! function_exists( 'telex_dpw_fetch_open_meteo' ) ) {
	/**
	 * Fetches weather from Open-Meteo.
	 *
	 * @param string $lat Latitude.
	 * @param string $lon Longitude.
	 * @param string $api_key Optional API key.
	 * @return array|WP_Error Parsed body or error.
	 */
	function telex_dpw_fetch_open_meteo( string $lat, string $lon, string $api_key = '' ) {
		$params = array(
			'latitude'          => $lat,
			'longitude'         => $lon,
			'hourly'            => 'temperature_2m,relative_humidity_2m,precipitation_probability,weather_code,cloud_cover,wind_speed_10m,uv_index',
			'daily'             => 'sunrise,sunset',
			'timezone'          => 'auto',
			'forecast_days'     => '2',
			'temperature_unit'  => 'celsius',
			'wind_speed_unit'   => 'kmh',
		);
		if ( ! empty( $api_key ) ) {
			$params['apikey'] = $api_key;
		}
		$url      = add_query_arg( $params, 'https://api.open-meteo.com/v1/forecast' );
		$response = wp_remote_get( $url, array( 'timeout' => 10, 'sslverify' => true ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'api_http_error', __( 'Open-Meteo returned a non-200 status.', 'telex-dog-park-weather' ) );
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || empty( $body['hourly'] ) ) {
			return new WP_Error( 'api_parse_error', __( 'Invalid data from Open-Meteo.', 'telex-dog-park-weather' ) );
		}
		return $body;
	}
}

if ( ! function_exists( 'telex_dpw_fetch_openweather' ) ) {
	/**
	 * Fetches weather from OpenWeatherMap and normalizes to Open-Meteo format.
	 *
	 * @param string $lat Latitude.
	 * @param string $lon Longitude.
	 * @param string $api_key API key.
	 * @return array|WP_Error Normalized body or error.
	 */
	function telex_dpw_fetch_openweather( string $lat, string $lon, string $api_key ) {
		if ( empty( $api_key ) ) {
			return new WP_Error( 'no_api_key', __( 'OpenWeatherMap API key is missing.', 'telex-dog-park-weather' ) );
		}
		$url = add_query_arg( array(
			'lat'   => $lat,
			'lon'   => $lon,
			'appid' => $api_key,
			'units' => 'metric',
			'cnt'   => '48',
		), 'https://pro.openweathermap.org/data/2.5/forecast/hourly' );

		$response = wp_remote_get( $url, array( 'timeout' => 10, 'sslverify' => true ) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'api_http_error', __( 'OpenWeatherMap returned a non-200 status.', 'telex-dog-park-weather' ) );
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || empty( $body['list'] ) ) {
			return new WP_Error( 'api_parse_error', __( 'Invalid data from OpenWeatherMap.', 'telex-dog-park-weather' ) );
		}

		$times   = array();
		$temps   = array();
		$rains   = array();
		$codes   = array();
		$clouds  = array();
		$winds   = array();
		$uvs     = array();

		foreach ( $body['list'] as $entry ) {
			$dt = (int) ( $entry['dt'] ?? 0 );
			if ( ! $dt ) continue;
			$times[]  = gmdate( 'Y-m-d\TH:i', $dt );
			$temps[]  = (float) ( $entry['main']['temp'] ?? 0 );
			$rains[]  = (int) ( ( $entry['pop'] ?? 0 ) * 100 );
			$codes[]  = telex_dpw_owm_to_wmo( (int) ( $entry['weather'][0]['id'] ?? 800 ) );
			$clouds[] = (int) ( $entry['clouds']['all'] ?? 0 );
			$winds[]  = round( ( (float) ( $entry['wind']['speed'] ?? 0 ) ) * 3.6, 1 );
			$uvs[]    = 0;
		}

		return array(
			'hourly' => array(
				'time'                       => $times,
				'temperature_2m'             => $temps,
				'precipitation_probability'  => $rains,
				'weather_code'               => $codes,
				'cloud_cover'                => $clouds,
				'wind_speed_10m'             => $winds,
				'uv_index'                   => $uvs,
			),
		);
	}
}

if ( ! function_exists( 'telex_dpw_owm_to_wmo' ) ) {
	/**
	 * Maps an OpenWeatherMap condition ID to a WMO weather code.
	 *
	 * @param int $owm_id OWM condition ID.
	 * @return int WMO code.
	 */
	function telex_dpw_owm_to_wmo( int $owm_id ): int {
		if ( $owm_id >= 200 && $owm_id < 300 ) return 95;
		if ( $owm_id >= 300 && $owm_id < 400 ) return 51;
		if ( $owm_id >= 500 && $owm_id < 600 ) return 61;
		if ( $owm_id >= 600 && $owm_id < 700 ) return 71;
		if ( $owm_id >= 700 && $owm_id < 800 ) return 45;
		if ( $owm_id === 800 ) return 0;
		if ( $owm_id === 801 ) return 1;
		if ( $owm_id === 802 ) return 2;
		if ( $owm_id === 803 || $owm_id === 804 ) return 3;
		return 0;
	}
}

if ( ! function_exists( 'telex_dpw_fetch_google_weather' ) ) {
	/**
	 * Fetches weather from Google Weather API and normalizes to Open-Meteo format.
	 *
	 * @param string $lat Latitude.
	 * @param string $lon Longitude.
	 * @param string $api_key API key.
	 * @return array|WP_Error Normalized body or error.
	 */
	function telex_dpw_fetch_google_weather( string $lat, string $lon, string $api_key ) {
		if ( empty( $api_key ) ) {
			return new WP_Error( 'no_api_key', __( 'Google Weather API key is missing.', 'telex-dog-park-weather' ) );
		}

		$url = add_query_arg(
			array( 'key' => $api_key ),
			'https://weather.googleapis.com/v1/forecast/hours'
		);

		$request_body = wp_json_encode( array(
			'location' => array(
				'latitude'  => (float) $lat,
				'longitude' => (float) $lon,
			),
			'hours'   => 48,
			'unitsSystem' => 'METRIC',
		) );

		$response = wp_remote_post( $url, array(
			'timeout'   => 10,
			'sslverify' => true,
			'headers'   => array( 'Content-Type' => 'application/json' ),
			'body'      => $request_body,
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'api_http_error', __( 'Google Weather API returned a non-200 status.', 'telex-dog-park-weather' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || empty( $body['forecastHours'] ) ) {
			return new WP_Error( 'api_parse_error', __( 'Invalid data from Google Weather API.', 'telex-dog-park-weather' ) );
		}

		$times  = array();
		$temps  = array();
		$rains  = array();
		$codes  = array();
		$clouds = array();
		$winds  = array();
		$uvs    = array();

		foreach ( $body['forecastHours'] as $hour ) {
			$times[]  = $hour['interval']['startTime'] ?? '';
			$temps[]  = (float) ( $hour['temperature']['degrees'] ?? 0 );
			$rains[]  = (int) ( ( $hour['precipitation']['probability']['percent'] ?? 0 ) );
			$codes[]  = telex_dpw_google_to_wmo( $hour['weatherCondition']['type'] ?? 'CLEAR' );
			$clouds[] = (int) ( $hour['cloudCover'] ?? 0 );
			$w_kmh    = (float) ( $hour['wind']['speed']['value'] ?? 0 );
			if ( ( $hour['wind']['speed']['unit'] ?? '' ) === 'METER_PER_SECOND' ) {
				$w_kmh = $w_kmh * 3.6;
			}
			$winds[] = round( $w_kmh, 1 );
			$uvs[]   = (float) ( $hour['uvIndex'] ?? 0 );
		}

		return array(
			'hourly' => array(
				'time'                      => $times,
				'temperature_2m'            => $temps,
				'precipitation_probability' => $rains,
				'weather_code'              => $codes,
				'cloud_cover'               => $clouds,
				'wind_speed_10m'            => $winds,
				'uv_index'                  => $uvs,
			),
		);
	}
}

if ( ! function_exists( 'telex_dpw_google_to_wmo' ) ) {
	/**
	 * Maps a Google Weather condition type to a WMO weather code.
	 *
	 * @param string $type Google weather condition type.
	 * @return int WMO code.
	 */
	function telex_dpw_google_to_wmo( string $type ): int {
		$map = array(
			'CLEAR'                  => 0,
			'MOSTLY_CLEAR'           => 1,
			'PARTLY_CLOUDY'          => 2,
			'CLOUDY'                 => 3,
			'LIGHT_RAIN'             => 61,
			'MODERATE_RAIN'          => 63,
			'HEAVY_RAIN'             => 65,
			'LIGHT_SNOW'             => 71,
			'MODERATE_SNOW'          => 73,
			'HEAVY_SNOW'             => 75,
			'THUNDERSTORM'           => 95,
			'HAIL'                   => 99,
			'FOG'                    => 45,
			'LIGHT_FREEZING_RAIN'    => 66,
			'HEAVY_FREEZING_RAIN'    => 67,
		);
		return $map[ $type ] ?? 0;
	}
}

if ( ! function_exists( 'telex_dpw_is_known_park_coordinate' ) ) {
	/**
	 * Checks whether lat/lon match one of the registered parks, within a small
	 * tolerance for floating point / rounding differences.
	 *
	 * @param float $lat Latitude.
	 * @param float $lon Longitude.
	 * @return bool
	 */
	function telex_dpw_is_known_park_coordinate( float $lat, float $lon ): bool {
		foreach ( telex_dpw_get_parks() as $park ) {
			$park_lat = (float) ( $park['lat'] ?? 0 );
			$park_lon = (float) ( $park['lon'] ?? 0 );
			if ( abs( $park_lat - $lat ) < 0.001 && abs( $park_lon - $lon ) < 0.001 ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'telex_dpw_fetch_weather_for_coords' ) ) {
	/**
	 * Fetches weather for a coordinate, trying providers in configured order
	 * with retries. Used by both the scheduled refresh and the REST callback's
	 * cache-miss fallback, so there's exactly one place this logic lives.
	 *
	 * @param string $lat Latitude.
	 * @param string $lon Longitude.
	 * @return array<string, mixed>|WP_Error
	 */
	function telex_dpw_fetch_weather_for_coords( $lat, $lon ) {
		$api_config = telex_dpw_get_api_providers();
		$primary    = $api_config['primary'];
		$retries    = max( 1, $api_config['retries'] );
		$providers  = $api_config['providers'];

		$order = array( $primary );
		foreach ( array_keys( $providers ) as $key ) {
			if ( $key !== $primary && ! empty( $providers[ $key ]['enabled'] ) ) {
				$order[] = $key;
			}
		}

		$last_error = null;

		foreach ( $order as $provider_key ) {
			if ( empty( $providers[ $provider_key ]['enabled'] ) ) {
				continue;
			}
			$api_key = $providers[ $provider_key ]['api_key'] ?? '';

			for ( $attempt = 1; $attempt <= $retries; $attempt++ ) {
				$result = null;

				switch ( $provider_key ) {
					case 'open_meteo':
						$result = telex_dpw_fetch_open_meteo( $lat, $lon, $api_key );
						break;
					case 'openweather':
						$result = telex_dpw_fetch_openweather( $lat, $lon, $api_key );
						break;
					case 'google_weather':
						$result = telex_dpw_fetch_google_weather( $lat, $lon, $api_key );
						break;
				}

				if ( ! is_wp_error( $result ) && is_array( $result ) && ! empty( $result['hourly'] ) ) {
					$result['_provider'] = $provider_key;
					return $result;
				}

				$last_error = $result;

				if ( $attempt < $retries ) {
					usleep( 500000 );
				}
			}
		}

		if ( is_wp_error( $last_error ) ) {
			return $last_error;
		}
		return new WP_Error( 'api_error', __( 'All weather providers failed.', 'telex-dog-park-weather' ), array( 'status' => 502 ) );
	}
}

// How long a fetched forecast stays cached. Set well above the 12-hour gap
// between scheduled refreshes so a late/missed WP-Cron run (WP-Cron only
// fires on page load, so a quiet site can run a few minutes to hours behind
// the intended 2 AM / 2 PM time) doesn't cause visitors to hit a cache miss.
if ( ! defined( 'TELEX_DPW_WEATHER_CACHE_TTL' ) ) {
	define( 'TELEX_DPW_WEATHER_CACHE_TTL', 15 * HOUR_IN_SECONDS );
}

if ( ! function_exists( 'telex_dpw_refresh_all_parks_weather' ) ) {
	/**
	 * Scheduled (WP-Cron) job: fetches and caches weather for every registered
	 * park, twice a day. This is what keeps the REST endpoint from making a
	 * live provider call on every visitor request.
	 *
	 * @return void
	 */
	function telex_dpw_refresh_all_parks_weather(): void {
		foreach ( telex_dpw_get_parks() as $park ) {
			$lat = isset( $park['lat'] ) ? (string) $park['lat'] : '';
			$lon = isset( $park['lon'] ) ? (string) $park['lon'] : '';
			if ( '' === $lat || '' === $lon ) {
				continue;
			}
			$result = telex_dpw_fetch_weather_for_coords( $lat, $lon );
			if ( ! is_wp_error( $result ) ) {
				$cache_key = 'telex_dpw_weather_' . md5( $lat . '_' . $lon );
				set_transient( $cache_key, $result, TELEX_DPW_WEATHER_CACHE_TTL );
			}
			// Be polite to the provider when there are many parks in one run.
			usleep( 250000 );
		}
	}
	add_action( 'telex_dpw_refresh_weather', 'telex_dpw_refresh_all_parks_weather' );
}

if ( ! function_exists( 'telex_dpw_next_clock_time' ) ) {
	/**
	 * Returns the timestamp of the next occurrence of a given hour, in the
	 * site's configured timezone.
	 *
	 * @param int $hour Hour of day, 0-23.
	 * @return int Unix timestamp.
	 */
	function telex_dpw_next_clock_time( int $hour ): int {
		$tz     = wp_timezone();
		$now    = new DateTime( 'now', $tz );
		$target = new DateTime( 'now', $tz );
		$target->setTime( $hour, 0, 0 );
		if ( $target <= $now ) {
			$target->modify( '+1 day' );
		}
		return $target->getTimestamp();
	}
}

if ( ! function_exists( 'telex_dpw_register_cron_schedule' ) ) {
	/**
	 * Registers the "every 12 hours" cron interval used for the 2 AM / 2 PM refresh.
	 *
	 * @param array<string, array<string, mixed>> $schedules Existing schedules.
	 * @return array<string, array<string, mixed>>
	 */
	function telex_dpw_register_cron_schedule( array $schedules ): array {
		if ( ! isset( $schedules['telex_dpw_twicedaily'] ) ) {
			$schedules['telex_dpw_twicedaily'] = array(
				'interval' => 12 * HOUR_IN_SECONDS,
				'display'  => __( 'Twice Daily (2 AM / 2 PM) - Telex Dog Park Weather', 'telex-dog-park-weather' ),
			);
		}
		return $schedules;
	}
	add_filter( 'cron_schedules', 'telex_dpw_register_cron_schedule' ); // phpcs:ignore WordPress.WP.CronInterval.CronSchedulesInterval
}

if ( ! function_exists( 'telex_dpw_activate' ) ) {
	/**
	 * Activation hook: schedules the twice-daily weather refresh, anchored to
	 * the next 2 AM or 2 PM (whichever comes first), and does an immediate
	 * first fetch so the widget has data right away instead of waiting.
	 *
	 * @return void
	 */
	function telex_dpw_activate(): void {
		if ( ! wp_next_scheduled( 'telex_dpw_refresh_weather' ) ) {
			$next_2am = telex_dpw_next_clock_time( 2 );
			$next_2pm = telex_dpw_next_clock_time( 14 );
			$first_run = min( $next_2am, $next_2pm );
			wp_schedule_event( $first_run, 'telex_dpw_twicedaily', 'telex_dpw_refresh_weather' );
		}
		if ( ! wp_next_scheduled( 'telex_dpw_refresh_weather_now' ) ) {
			wp_schedule_single_event( time() + 30, 'telex_dpw_refresh_weather_now' );
		}
	}
	register_activation_hook( __FILE__, 'telex_dpw_activate' );
	add_action( 'telex_dpw_refresh_weather_now', 'telex_dpw_refresh_all_parks_weather' );
}

if ( ! function_exists( 'telex_dpw_deactivate' ) ) {
	/**
	 * Deactivation hook: clears the scheduled refresh.
	 *
	 * @return void
	 */
	function telex_dpw_deactivate(): void {
		wp_clear_scheduled_hook( 'telex_dpw_refresh_weather' );
		wp_clear_scheduled_hook( 'telex_dpw_refresh_weather_now' );
	}
	register_deactivation_hook( __FILE__, 'telex_dpw_deactivate' );
}

if ( ! function_exists( 'telex_dpw_ensure_cron_scheduled' ) ) {
	/**
	 * Activation hooks only fire on an explicit (re)activate click in wp-admin,
	 * not on a plain file overwrite/update - so an in-place plugin update alone
	 * would silently never schedule the refresh. This is a cheap safety net
	 * that checks on every load and (re)schedules if it's ever missing.
	 *
	 * @return void
	 */
	function telex_dpw_ensure_cron_scheduled(): void {
		if ( ! wp_next_scheduled( 'telex_dpw_refresh_weather' ) ) {
			telex_dpw_activate();
		}
	}
	add_action( 'init', 'telex_dpw_ensure_cron_scheduled' );
}

if ( ! function_exists( 'telex_dpw_rest_get_weather' ) ) {
	/**
	 * REST callback: serves the cached forecast populated by the twice-daily
	 * cron job. Only makes a live provider call on a cache miss (e.g. a park
	 * added since the last refresh, or right after activation) — normal
	 * traffic never triggers a live API call itself.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	function telex_dpw_rest_get_weather( WP_REST_Request $request ) {
		$lat = $request->get_param( 'lat' );
		$lon = $request->get_param( 'lon' );

		if ( ! is_numeric( $lat ) || ! is_numeric( $lon ) ) {
			return new WP_Error( 'invalid_coords', __( 'Invalid coordinates.', 'telex-dog-park-weather' ), array( 'status' => 400 ) );
		}

		// This endpoint is unauthenticated by design (the widget can be viewed by
		// anyone), but that means it doubles as an open proxy to the configured
		// weather provider(s) unless we bound it to real parks. Without this check
		// anyone can call it with arbitrary/near-arbitrary coordinates to bypass
		// the cache on every request and burn through provider quota.
		if ( ! telex_dpw_is_known_park_coordinate( (float) $lat, (float) $lon ) ) {
			return new WP_Error( 'unknown_location', __( 'Unrecognized park location.', 'telex-dog-park-weather' ), array( 'status' => 403 ) );
		}

		$cache_key = 'telex_dpw_weather_' . md5( $lat . '_' . $lon );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return rest_ensure_response( $cached );
		}

		// Cache miss (new park, first activation, or a very unlucky expiry) -
		// do a one-off live fetch so the visitor still gets a forecast, and
		// backfill the cache so the next request doesn't need to.
		$result = telex_dpw_fetch_weather_for_coords( $lat, $lon );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		set_transient( $cache_key, $result, TELEX_DPW_WEATHER_CACHE_TTL );
		return rest_ensure_response( $result );
	}
}

if ( ! function_exists( 'telex_dpw_allow_json_upload' ) ) {
	/**
	 * Allow JSON file uploads in the media library.
	 *
	 * @param array<string, string> $mimes Existing mime types.
	 * @return array<string, string>
	 */
	function telex_dpw_allow_json_upload( array $mimes ): array {
		$mimes['json'] = 'application/json';
		return $mimes;
	}
	add_filter( 'upload_mimes', 'telex_dpw_allow_json_upload' );
}