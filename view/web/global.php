<?php
	function selected_if($tmp, $curr) {
		if ($curr == $tmp) {
			echo 'class="selected"';
		}
	}
	
	//header('Cache-Control: no-cache, must-revalidate');
	//header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	
?>
<!DOCTYPE html>
<html <?= ((isset($this->manifest) && ($this->manifest)) ? "manifest=\"{$this->manifest}\"" : "manifest = \"manifest.php\""); ?>>
    <head>
        <title><?php echo $this->title; ?> - Find My Feet</title>
        
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
		<base href="http://www.findmyfeet.co.uk/" />
		<script src="static/js/global.js" ></script>
		<?php foreach ($this->jscripts as $h): ?>
			<script src="<?= $h ?>" type="text/javascript"></script>
		<?php endforeach ?> 
		
		<link rel="stylesheet" type="text/css" href="static/css/styles.css" />
		<link rel="shortcut icon" href="static/img/favicon.ico" />
    </head>
    <body>
        <header>
            <a href="?page=home"> <h1> Find My Feet </h1> </a>
            <nav>
				<a <?php selected_if('home', $this->template); ?> href="?page=home">Home</a>
				<!-- <a <?php selected_if('emails', $this->template); ?>href="?page=emails">Emails</a> -->
				<a <?php selected_if('about', $this->template); ?> href="?page=about">About</a>
            </nav>
            <div style="clear: both;"></div>
        </header>
        <div class="cache-box" style="display: none">
        	<div id="cache-progress" class="progress-bar"><span>Caching...</span></div>
        </div>
        <div class="main" id="maind">
		<div class="error" <?= (isset($this->error) && ($this->error) ? "" : 'style="display: none"') ?> >
			 <?= (isset($this->error) ? $this->error : "") ?>
		</div>
            <?php include $this->body ?>
        </div>
        
    </body>
</html>
