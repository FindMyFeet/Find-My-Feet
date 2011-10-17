<h2 align="center"> Find a route </h2>

<form class="niceForm" class="validate-postcodes" method="get" action="index.php"> <!-- Change when URL rewriting works.  -->
	<input type="hidden" name="page" value="routes" />
	<label for="from">From: </label><input class="postcode" id="from" onblur="isValidPostcode(this.value)" type="text" name="from" placeholder="Postcode" required />
	<label for="to">To: </label><input class="postcode" id="to" onblur="isValidPostcode(this.value)" type="text" name="to" placeholder="Postcode" required />
	<input type="submit" value="Search" />
</form>
