// Global Namespace!

function isValidPostcode(p) {
	var postcodeRegEx = /(GIR 0AA)|(((A[BL]|B[ABDHLNRSTX]?|C[ABFHMORTVW]|D[ADEGHLNTY]|E[HNX]?|F[KY]|G[LUY]?|H[ADGPRSUX]|I[GMPV]|JE|K[ATWY]|L[ADELNSU]?|M[EKL]?|N[EGNPRW]?|O[LX]|P[AEHLOR]|R[GHM]|S[AEGKLMNOPRSTY]?|T[ADFNQRSW]|UB|W[ADFNRSV]|YO|ZE)[1-9]?[0-9]|((E|N|NW|SE|SW|W)1|EC[1-4]|WC[12])[A-HJKMNPR-Y]|(SW|W)([2-9]|[1-9][0-9])|EC[1-9][0-9]) [0-9][ABD-HJLNP-UW-Z]{2})/i;
	if (!postcodeRegEx.test(p)){
		return false;
	}
	return true;
}

function showError(html) {
	$('.error').html(html).show();
}

/* This runs on page load */

$(function() {
	//Validate a postcode input, return true/false and show an error.
	var validatePostcodeInput = function(input) {
		if (!isValidPostcode(input.value)) {
			$(input).addClass('error');
			showError('Postcode is invalid');
			return false;
		}
		else {
			$(input).removeClass('error');
			return true;
		}
	}
	
	//Validate postcodes on form submit
	$('.validate-postcodes').submit(function() {
		var failed = false;
		$('.postcode', this).each(function() {
			failed = validatePostcodeInput(this);
		});
		if (failed)
			return false;
		else
			return true;
	});
	
	//Validate postcodes on input change
	$('.postcode').change(function() {
		validatePostcodeInput(this);
	});
});