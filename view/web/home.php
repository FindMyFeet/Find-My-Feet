<h2 align="center"> Find a route </h2>

<form class="postcodes-form" class="validate-postcodes" method="get" action="index.php"> <!-- Change when URL rewriting works.  -->
	<input type="hidden" name="page" value="routes" />
	<div><img src = "http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hotel_0star.png" /><label for="from">Hotel: </label><input class="postcode" id="from" onblur="isValidPostcode(this.value)" type="text" name="from" placeholder="Postcode" required /></div>
	<div><img src = "http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hostel_0star.png" /><label for="to">Venue: </label><input class="postcode" id="to" onblur="isValidPostcode(this.value)" type="text" name="to" placeholder="Postcode" required /></div>
	<input type="submit" value="Search" />
</form>
