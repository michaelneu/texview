<?php
require_once("../../php/util/io.php");

# Represents a TeXView project
class Project {
	function __construct($dirname) {
		$projects = path_join($GLOBALS["root_path"], "projects");
		$this->dirname = path_join($projects, $dirname);
		$this->logfile = path_join($this->dirname, "main.log");
	}

	function get_compile_status() {
		$colors = array(CompileStatus::FAIL, CompileStatus::SUCCESS, CompileStatus::UNKNOWN);

		return $colors[rand(0, count($colors) - 1)];
	}

	function get_last_compile() {
		if (file_exists($this->logfile)) {
			return filemtime($this->logfile);
		} else {
			return 0;
		}
	}
}

abstract class CompileStatus {
	const FAIL    = "FAIL";
	const SUCCESS = "SUCCESS";
	const UNKNOWN = "UNKNOWN";
}

?>