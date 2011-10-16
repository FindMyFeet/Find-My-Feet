<h2>Email</h2>

<?php foreach ($this->emails['files'] as $file) { 

	echo "<p><a href='email.php?file=".$file."'>$file</a></p>";
	echo "<iframe src='email.php?file=".$file."'></iframe>";
} ?>
