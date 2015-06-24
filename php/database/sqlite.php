<?php

/**
 * A basic wrapper for the SQLite PDO
 */
class SQLite {
	function __construct($filename) {
		if (!file_exists($filename)) {
			printf("<b>Warning</b>: SQLite database \"%s\" not found. Database will be created\n", $filename);
		}

		$this->__connection = new PDO("sqlite:" . $filename);
	}

	/**
	 * Execute a query and view the result
	 * @param  string      $query The query to be executed
	 * @return RowIterator        An iterator over the result set
	 */
	function query($query) {
		$result = $this->__connection->query($query);

		return new RowIterator($result);
	}

	/**
	 * Execute a query
	 * 
	 * @param string The query to be executed
	 */
	function exec($query) {
		$this->__connection->exec($query);
	}
}


/**
 * A wrapper for PDO's ::fetch
 */
class RowIterator {
	function __construct($query_result) {
		$this->result = $query_result;

		$this->next_element = null;
		$this->next();
	}

	/**
	 * Check if the iterator has a value
	 * 
	 * @return bool If the iterator has a next value
	 */
	function has_next() {
		return $this->next_element != null;
	}

	/**
	 * Get the next value
	 * 
	 * @return array The next value or null
	 */
	function next() {
		$ret = $this->next_element;

		if ($this->result !== false) {
			$this->next_element = $this->result->fetch(PDO::FETCH_ASSOC);
		} else {
			$this->next_element = null;
		}

		return $ret;
	}
}

?>