<?php

	class HTML
	{
		public static function __callStatic($name, $args)
		{
			//echo p("calling $name");
			$s = "";
			if(count($args) == 0)
				$s = "<$name />";
			else
				foreach($args as $a)
					$s .= "<$name>$a</$name>";
				
			return $s;
		}
		
		public static function print_r($value)
		{
			/*switch(gettype($value))
			{
				case "array":
					break;
				case "
			}*/
			echo "<pre>".print_r($value, TRUE)."</pre>";
		}
	}

?>