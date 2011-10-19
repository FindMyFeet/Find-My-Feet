// Global Namespace!

function isValidPostcode(p) {
	var postcodeRegEx = /(GIR 0AA)|(((A[BL]|B[ABDHLNRSTX]?|C[ABFHMORTVW]|D[ADEGHLNTY]|E[HNX]?|F[KY]|G[LUY]?|H[ADGPRSUX]|I[GMPV]|JE|K[ATWY]|L[ADELNSU]?|M[EKL]?|N[EGNPRW]?|O[LX]|P[AEHLOR]|R[GHM]|S[AEGKLMNOPRSTY]?|T[ADFNQRSW]|UB|W[ADFNRSV]|YO|ZE)[1-9]?[0-9]|((E|N|NW|SE|SW|W)1|EC[1-4]|WC[12])[A-HJKMNPR-Y]|(SW|W)([2-9]|[1-9][0-9])|EC[1-9][0-9]) [0-9][ABD-HJLNP-UW-Z]{2})/i;
	if (!postcodeRegEx.test(p)){
		return false;
	}
	return true;
}

function showError(html) {
	$('.error').html(html).slideDown();
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
		console.log($('.postcode', this));
		$('.postcode', this).each(function() {
			failed = validatePostcodeInput(this);
		});
		console.log(failed);
		if (failed) {
			return false;
		}
		else
			return true;
	});
	
	//Validate postcodes on input change
	$('.postcode').change(function() {
		validatePostcodeInput(this);
	});
	
	// Cache progress bar
	if (window.applicationCache) {
		
		var animate = true;

		var animfunc = function() {
			$('#cache-progress').css('background-position-x', "+=1");
			if (animate) {
				setTimeout(animfunc, 20);
			}
		}
		
		//Start cache download
		window.applicationCache.addEventListener('downloading', function() {
			$('.cache-box').css('display', 'block');
			animfunc();
		}, false);

		//Cache download progress
		window.applicationCache.addEventListener('progress', function(e) {
			document.getElementById('cache-progress').style.width = Math.round((e.loaded * 100.0) / e.total) + "%";
		}, false);

		//Cache succesfully downloaded
		window.applicationCache.addEventListener('cached', function(e) {
			$('#cache-progress > span').html('Successfully downloaded to cache! Bookmark this page to return while offline.');
			document.getElementById('cache-progress').style.width = "100%";
			setTimeout(function() {
				$('.cache-box').slideUp('slow');
			}, 10000);
			animate = false;
		}, false);

		//Cache download failed.
		window.applicationCache.addEventListener('error', function(e) {
			$('#cache-progress > span').html('An error occured when trying to cache this map.');
			setTimeout(function() {
				$('.cache-box').slideUp('slow');
			}, 10000);
			animate = false;
		});
	}
});
