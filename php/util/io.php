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


function walk_dir($directory) {
	$sub_folders = array();
	$sub_files = array();

	$items = scandir($directory);
	$filtered_items = array_diff($items, array(".", ".."));

	foreach ($filtered_items as $item) {
		$path = path_join($directory, $item);

		if (is_dir($path)) {
			$sub_folders[$item] = walk_dir($path);
		} else {
			$sub_files[] = $item;
		}
	}

	$sub_items = array(
		"folders" => $sub_folders,
		"files"   => $sub_files
	);

	return $sub_items;
}

?>