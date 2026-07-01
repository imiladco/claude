/**
 * بهبود تجربهٔ ابزارک «جستجوی هوشمند محصولات».
 * فرم بدون جاوااسکریپت هم با GET کار می‌کند؛ این اسکریپت فقط اعتبارسنجیِ آنیِ
 * فیلدهای اجباری را اضافه می‌کند (Progressive Enhancement). وانیلا، بدون jQuery.
 */
( function () {
	'use strict';

	function initForm( form ) {
		var selects = form.querySelectorAll( '.anw-ss-select[data-required="1"]' );

		function validateField( select ) {
			var field = select.closest( '.anw-ss-field' );
			var error = field ? field.querySelector( '.anw-ss-error' ) : null;
			var valid = '' !== select.value;

			if ( error ) {
				error.hidden = valid;
			}
			select.setAttribute( 'aria-invalid', valid ? 'false' : 'true' );
			if ( field ) {
				field.classList.toggle( 'anw-ss-field--invalid', ! valid );
			}
			return valid;
		}

		form.addEventListener( 'submit', function ( event ) {
			var allValid = true;
			var firstInvalid = null;

			selects.forEach( function ( select ) {
				if ( ! validateField( select ) ) {
					allValid = false;
					if ( ! firstInvalid ) {
						firstInvalid = select;
					}
				}
			} );

			if ( ! allValid ) {
				event.preventDefault();
				if ( firstInvalid ) {
					firstInvalid.focus();
				}
			}
		} );

		selects.forEach( function ( select ) {
			select.addEventListener( 'change', function () {
				validateField( select );
			} );
		} );
	}

	function init() {
		var forms = document.querySelectorAll( '.anw-ss' );
		forms.forEach( function ( form ) {
			initForm( form );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
