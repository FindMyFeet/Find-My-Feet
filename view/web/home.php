<div class="centered">
	<form class="postcodes-form validate-postcodes" method="get" action="index.php">
		<h2> Enter destinations </h2>
		<input type="hidden" name="page" value="routes" />
		<div>
			<label for="from">
				<img src = "http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hotel_0star.png" alt="" />
				<span>Hotel: </span>
			</label>
			<input class="postcode" id="from" onblur="isValidPostcode(this.value)" type="text" name="from" placeholder="Postcode" required />
		</div>
		<div>
			<label for="to">
				<img src = "http://data.southampton.ac.uk/map-icons/Restaurants-and-Hotels/hostel_0star.png" alt="" />
				<span>Venue: </span>
			</label>
			<input class="postcode" id="to" onblur="isValidPostcode(this.value)" type="text" name="to" placeholder="Postcode" required />
		</div>
		<input type="submit" value="Search" />
	</form>

	<div class="front-about">
		<h2>Find your route, use it offline</h2>
	<p>Find My Feet is your <em>offline</em> companion for traveling to new cities. <br> You can: </p>
		<ul>
			<li>Enter your travel destinations</li>
			<li>Pre-plan routes between them</li>
			<li>Get routes to nearby transport</li>
			<li>Bookmark to view the website <strong>offline!</strong></li>
		</ul>
	</div>
</div>
