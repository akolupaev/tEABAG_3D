<?php

class FontLoader {

	static public function loadFont($fontdir){
		$fonts = array();
		$d = dir($fontdir);
		while($entry = $d->read()) {
			if ($entry != "." && $entry != "..") {
				if (!is_dir($fontdir.$entry) && substr($entry, -4) == '.ttf' )
				array_push($fonts, $entry);
			}
		}
		
		$d->close();
		return $fontdir.$fonts[array_rand($fonts)];
	}
}
?>