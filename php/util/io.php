<?php

function path_join($a, $b) {
	$paths = array(
		rtrim($a, "/ "),
		ltrim($b, "/ ")
	);

	return implode("/", $paths);
}

function rrmdir($dir) { 
	if (is_dir($dir)) {
		$objects = scandir($dir);

		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") {
					rrmdir($dir."/".$object); 
				}
				else {
					unlink($dir."/".$object);
				}
			}
		}

		reset($objects);
		rmdir($dir);
	}
}

?>