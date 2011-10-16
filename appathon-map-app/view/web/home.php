<h2 align="center"> Find a route </h2>

<form class="niceForm" method="get" action="index.php"> <!-- Change when URL rewriting works.  -->
	<input type="hidden" name="page" value="routes" />
	<label for="from">From: </label><input id="from" onblur="isValidPostcode(this.value)" type="text" name="from" placeholder="Postcode" required />
	<label for="to">To: </label><input id="from" onblur="isValidPostcode(this.value)" type="text" name="to" placeholder="Postcode" required />
	<input type="submit" value="Search" />
</form>
