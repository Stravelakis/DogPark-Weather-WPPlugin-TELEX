/* global jQuery, wp */
( function( $ ) {
	'use strict';

	let parkIndex = $( '.telex-dpw-park-row' ).length;

	$( '#telex-dpw-add-park' ).on( 'click', function() {
		const idx = parkIndex;
		const uid = 'park-' + Date.now() + '-' + idx;
		const html = '<div class="telex-dpw-park-row" data-index="' + idx + '">' +
			'<div class="telex-dpw-park-header">' +
			'<strong>New Park</strong>' +
			'<button type="button" class="button telex-dpw-remove-park">Remove</button>' +
			'</div>' +
			'<input type="hidden" name="telex_dpw_parks[' + idx + '][id]" value="' + uid + '" />' +
			'<table class="form-table">' +
			'<tr><th><label>Park Name</label></th>' +
			'<td><input type="text" name="telex_dpw_parks[' + idx + '][name]" value="" class="regular-text" required /></td></tr>' +
			'<tr><th><label>Latitude</label></th>' +
			'<td><input type="text" name="telex_dpw_parks[' + idx + '][lat]" value="" class="regular-text" placeholder="37.9838" required /></td></tr>' +
			'<tr><th><label>Longitude</label></th>' +
			'<td><input type="text" name="telex_dpw_parks[' + idx + '][lon]" value="" class="regular-text" placeholder="23.7275" required /></td></tr>' +
			'<tr><th>Conditions</th><td>' +
			'<label><input type="checkbox" name="telex_dpw_parks[' + idx + '][drainage]" value="1" /> Good Drainage (halves rain penalty)</label><br />' +
			'<label><input type="checkbox" name="telex_dpw_parks[' + idx + '][shade]" value="1" /> Good Shade (halves UV penalty)</label><br />' +
			'<label><input type="checkbox" name="telex_dpw_parks[' + idx + '][wind_shelter]" value="1" /> Wind Shelter (halves wind penalty)</label><br />' +
			'<label><input type="checkbox" name="telex_dpw_parks[' + idx + '][covered]" value="1" /> Covered Areas (halves cloud/cold penalty)</label>' +
			'</td></tr></table></div>';
		$( '#telex-dpw-parks-list' ).append( html );
		parkIndex++;
	} );

	$( document ).on( 'click', '.telex-dpw-remove-park', function() {
		$( this ).closest( '.telex-dpw-park-row' ).remove();
	} );

	$( document ).on( 'click', '.telex-dpw-upload-icon', function() {
		const button = $( this );
		const input = button.prev( '.telex-dpw-icon-url' );

		const frame = wp.media( {
			title: 'Select Lottie JSON File',
			button: { text: 'Use this file' },
			multiple: false,
			library: { type: 'application/json' },
		} );

		frame.on( 'select', function() {
			const attachment = frame.state().get( 'selection' ).first().toJSON();
			input.val( attachment.url );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.telex-dpw-clear-icon', function() {
		$( this ).siblings( '.telex-dpw-icon-url' ).val( '' );
	} );

	// Color picker sync.
	$( document ).on( 'input', '.telex-dpw-color-picker', function() {
		$( this ).prev( '.telex-dpw-color-field' ).val( $( this ).val() );
	} );
	$( document ).on( 'input', '.telex-dpw-color-field', function() {
		var val = $( this ).val();
		if ( /^#[0-9A-Fa-f]{6}$/.test( val ) ) {
			$( this ).next( '.telex-dpw-color-picker' ).val( val );
		}
	} );

	$( document ).on( 'input', '.telex-dpw-park-row input[type="text"]', function() {
		const row = $( this ).closest( '.telex-dpw-park-row' );
		const nameInput = row.find( 'input[name$="[name]"]' );
		if ( nameInput.length ) {
			row.find( '.telex-dpw-park-header strong' ).text( nameInput.val() || 'New Park' );
		}
	} );

	// --- Translations tab: Greek preset + auto-translate --------------------

	function fillTranslationFields( map ) {
		Object.keys( map ).forEach( function( key ) {
			const field = $( '#telex_dpw_trans_' + key );
			if ( field.length ) {
				field.val( map[ key ] );
			}
		} );
	}

	$( '#telex-dpw-use-greek-preset' ).on( 'click', function() {
		if ( typeof telexDpwAdmin === 'undefined' || ! telexDpwAdmin.greekPreset ) {
			return;
		}
		fillTranslationFields( telexDpwAdmin.greekPreset );
		$( '#telex-dpw-translate-status' )
			.text( 'Greek preset filled in below - review, then click Save Changes.' )
			.css( 'color', '#1a7f37' );
	} );

	$( '#telex-dpw-auto-translate' ).on( 'click', function() {
		if ( typeof telexDpwAdmin === 'undefined' ) {
			return;
		}
		const $btn = $( this );
		const $status = $( '#telex-dpw-translate-status' );
		const lang = $( '#telex-dpw-translate-lang' ).val();
		const style = $( '#telex-dpw-translate-style' ).val();
		const deeplKey = $( '#telex-dpw-deepl-key' ).val();

		$btn.prop( 'disabled', true );
		$status.css( 'color', '' ).text( 'Translating (this can take a few seconds for ~35 strings)...' );

		$.post( telexDpwAdmin.ajaxUrl, {
			action: 'telex_dpw_translate_strings',
			nonce: telexDpwAdmin.translateNonce,
			target_lang: lang,
			style: style,
			deepl_key: deeplKey
		} )
			.done( function( response ) {
				if ( response && response.success && response.data && response.data.translations ) {
					fillTranslationFields( response.data.translations );
					const failures = response.data.failures || 0;
					const usedDeepl = !! response.data.used_deepl;
					const deeplFailed = !! response.data.deepl_failed;
					let msg = usedDeepl ? 'Translated with DeepL and filled in below - review, then click Save Changes.' : 'Filled in below - review, then click Save Changes.';
					if ( deeplFailed ) {
						msg = 'DeepL key didn\'t work (wrong key, or Free/Pro mismatch) - used the free service instead. ' + msg;
					}
					if ( failures > 0 ) {
						$status
							.css( 'color', '#b32d2e' )
							.text( msg + ' ' + failures + ' of ' + response.data.total + ' strings failed to translate - check those by hand.' );
					} else {
						$status.css( 'color', deeplFailed ? '#b32d2e' : '#1a7f37' ).text( msg );
					}
				} else {
					const msg = ( response && response.data && response.data.message ) || 'Translation failed. Please try again.';
					$status.css( 'color', '#b32d2e' ).text( msg );
				}
			} )
			.fail( function() {
				$status.css( 'color', '#b32d2e' ).text( 'Translation request failed - check your connection and try again.' );
			} )
			.always( function() {
				$btn.prop( 'disabled', false );
			} );
	} );
}( jQuery ) );
