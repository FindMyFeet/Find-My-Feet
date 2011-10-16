<?php

error_reporting(E_ALL | E_STRICT);
require('controller/controller.php');


define('DOMAIN_URL', 'http://spacerat.meteornet.net');

// Set up the Locale
/*
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', 'en_GB');
require('lib/php-gettext-1.0.11/gettext.inc');
$encoding = 'UTF-8';
$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;
T_setlocale(LC_ALL, $locale);
$domain = 'messages';
bindtextdomain($domain, LOCALE_DIR);
if (function_exists('bind_textdomain_codeset'))
  bind_textdomain_codeset($domain, $encoding);
textdomain($domain);
header("Content-type: text/html; charset=$encoding");
*/

// Deal with request
$rpage = "home";
if (isset($_GET['page'])) {
    $rpage = $_GET['page'];
}


if (isset($_REQUEST['method']))
	$method = strtolower($_REQUEST['method']);
else
	$method = strtolower($_SERVER['REQUEST_METHOD']);

$h = Controller::load($rpage, true);
if ($h) {
	$h->handleRequest($method, $_GET, $_POST);
}
else {
	header('HTTP/1.0 404 Not Found');
	$h = Controller::load('notfound', true);
	if ($h) {
		$h->handleRequest($method, $_GET, $_POST);
	}
	else {
		echo "Page not found.";
	}
}?>
