<?php

function path_join($a, $b) {
	$paths = array(
		rtrim($a, "/ "),
		ltrim($b, "/ ")
	);

	return implode("/", $paths);
}

?>