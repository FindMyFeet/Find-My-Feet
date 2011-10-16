<?php

require_once("model/email_parser.php");

class EmailsController extends Controller {
	public $template = "email";
	public $title = "Emails";
	public $serve_json = true;
	
	public function init($get) {
		$this->emails = parseEmails();
	}

}



?>
