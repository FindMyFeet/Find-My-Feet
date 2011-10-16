Appathon Map App/Find My Feet
================

The idea
---------
Plan routes to places, get info about the surrounding area, store them offline with the HTML5 offline storage cache manifest.
... Maybe include social elements? Probably not, though.

Todo
-----

- Make caching work
- Clean URLs (a.k.a. make .htaccess not break everything)
- Routing, esp. bus routes
- Use more interesting data
- Actually create routes from the email section, ***if*** we still want to keep it at all.
- Calendar stuff?
- User accounts?
- Make things shiny!

Basically, how/where to add new stuff:
--------------------------------------

Add new bits of business logic to the *model* folder.
Add new pages by adding a *controller* and a corresponding *view*, probably with the same file names.

How does the 'framework' work?
-------------------------------
### Components ###

- **Model**: Databases, calculations, all the bits of the code which actually do things. The features.
- **View**: The HTML templates. They describe how we show our data.
- **Controller**: Glues the View and Model together. Sends data from Model to View.

### What actually happens ###

1. **index.php** intercepts *all* page requests.
2. $page = $_GET['page']
3. Looks for a class called *$pageController* in a file called *controller/$page.php* (this is done in the Controller::load() static function)
4. Creates and instance of the class.
5. See below

### The Controller ###

1. require_once() any data/model components you need from the *model/* directory. 
2. Runs method *init()*. **This is the main method you should override**.
3. Runs method *post()* or *get()* depending on the type of request. Override *post()* if you want pages to accept post requests.

Use these methods in the controller to pass data from the model to the view. Here is an example:

```php
class GeoController {
	public $template = 'geo'; //Tells the controller to loog in view/web/geo.php for the template
	public $title = 'Lookup location';
	
	public function init($get) { //$get is provided here for convenience. You could also check $_GET.
		$postcode = $get['postcode']; //Variable NOT accessible by the view.
		$point = Geo::LatLongFromPostCode($postcode); //Call method from a model
	
		$this->lat = $point['lat']; //These variables are accessible in the view.
		$this->long = $point['long'];
	
		$this->data['point'] = $point; //If the page is ever requested as JSON, the controller runs encode_json($this->data) and echos it.
	}
}
```

After the init() functions are run, the specified template file is loaded and rendered.
