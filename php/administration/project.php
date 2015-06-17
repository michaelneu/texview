<?php
require_once("../../php/util/io.php");
require_once("../../php/database/database.php");

# Represents a TeXView project
class Project {
	function __construct($dirname) {
		$dirname = trim(basename($dirname));

		$dirname_clean = preg_replace("/^[a-z0-9]+$/", "", $dirname);
		if (strlen($dirname_clean) != 0) {
			$dirname = "";
		}
		
		$this->directory = $dirname;
		$projects        = path_join($GLOBALS["root_path"], "projects");
		$this->dirname   = path_join($projects, $dirname);
		$this->logfile   = path_join($this->dirname, "build/main.log");
	}

	function get_token() {
		$directory = $this->directory;

		$database = new Database();
		$iterator = $database->query("
			SELECT
				edit_token

			FROM 
				projects

			WHERE 
				directory = '$directory';");

		if ($iterator->has_next()) {
			$data = $iterator->next();
			$token = $data["edit_token"];

			return $token;
		}
	}

	function get_name() {
		$directory = $this->directory;

		$database = new Database();
		$iterator = $database->query("
			SELECT
				name

			FROM 
				projects

			WHERE 
				directory = '$directory';");

		if ($iterator->has_next()) {
			$data = $iterator->next();
			$name = $data["name"];

			return base64_decode($name);
		}
	}

	function get_compile_status() {
		if (file_exists($this->logfile)) {
			$contents = file_get_contents($this->logfile);

			if (strlen($contents) == 0 or strpos($contents, "\n!") !== false) {
				return CompileStatus::FAIL;
			} else {
				return CompileStatus::SUCCESS;
			}
		} else {
			return CompileStatus::UNKNOWN;
		}
	}

	function get_last_compile() {
		if (file_exists($this->logfile)) {
			clearstatcache();

			return filemtime($this->logfile);
		} else {
			return 0;
		}
	}

	function get_information() {
		$information = array(
			"time"   => $this->get_last_compile(),
			"status" => $this->get_compile_status()
		);

		return $information;
	}

	function get_pdf() {
		if ($this->get_compile_status() == CompileStatus::SUCCESS) {
			$pdf_path = path_join($this->dirname, "build/main.pdf");

			return $pdf_path;
		} else {
			return false;
		}
	}

	function get_directory_tree() {
		$src_dir = path_join($this->dirname, "src");

		return walk_dir($src_dir);
	}

	static function create($user_id, $alias) {
		$dirname = sha1(uniqid() . time());
		$token   = sha1($dirname . uniqid() . time());

		$user_id += 0;
		$alias    = base64_encode($alias);

		$database = new Database();
		$database->exec("
			INSERT INTO 
				projects (
					ref_owner, 
					directory, 
					name, 
					edit_token
				) 

			VALUES (
				$user_id, 
				'$dirname', 
				'$alias',
				'$token'
			);");

		$base_dir = "../../projects/$dirname";
		$base_dir_success = mkdir($base_dir);

		if ($base_dir_success) {
			$src_dir = path_join($base_dir, "src");

			mkdir($src_dir);
			mkdir(path_join($base_dir, "build"));

			file_put_contents(path_join($src_dir, "main.tex"), "\documentclass{article}

\\begin{document}
	~
\\end{document}");

			$project = array(
				"directory" => $dirname,
				"token"     => $token
			);

			return $project;
		} else {
			return null;
		}
	}

	static function delete($user_id, $project_id) {
		$user_id    += 0;
		$project_id += 0;

		$database = new Database();
		$iterator = $database->query("
			SELECT
				directory

			FROM 
				projects

			WHERE
				ref_owner = $user_id AND
				id        = $project_id;");

		if ($iterator->has_next()) {
			$information = $iterator->next();
			$project_dir = path_join("../../projects", $information["directory"]);

			$database->exec("DELETE FROM projects WHERE id = $project_id;");

			rrmdir($project_dir);
		}
	}
}

abstract class CompileStatus {
	const FAIL    = "FAIL";
	const SUCCESS = "SUCCESS";
	const UNKNOWN = "UNKNOWN";
}

?>