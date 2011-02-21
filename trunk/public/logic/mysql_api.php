<?php
/*
 * Database connection and interaction library
 */
class MysqlApi {
	private $host;
	private $database;
	private $user;
	private $password;

	private $link;

	/**
	 * Construct a new connection object.
	 *
	 * @param[in] host string The hostname of the database server.
	 */
	public function __construct($host='localhost', $database, $user='nobody',
		$password) {

		if ( !extension_loaded( 'mysql' )) {
			if ( !dl( 'mysql.so' )) {
				exit( 'Cannot load mysql extension.' );
			}
		}

		$this->host = $host;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
	}

	/**
	 * Establish a connection to a mysql database
	 *
	 * @return boolean. If TRUE, then the connection either previously existed,
	 *     or was established properly.
	 */
	public function connect()
	{
		if (!is_null($this->link)) {
			return TRUE;
		}

		$this->link = mysql_connect($this->host, $this->user, $this->password);
		if (is_null($this->link)) {
			error_log('unable to establish connection with mysql database');
			return FALSE;
		}

		if (!is_null($this->database) && 
			!mysql_select_db($this->database, $this->link)) { 
			error_log('unable to select mysql database');
			return FALSE;
		}

		return TRUE; 
	}

	/**
	 * Query the database.
	 *
	 * @param[in] query string A SQL command to be executed.
	 * @return mysql database connection resource.
	 */
	function query($query) {
		if (is_null($this->link) && (!$this->connect())) {
			return FALSE;
		}

		$result = mysql_query($query, $this->link);
		if (!$result) {
			$err = mysql_error();
			error_log("Could not get a result from the query, err: {$err}");
			return FALSE;
		}
		return $result;
	}

	/**
	 * Retrieve data from the database, return an associative array
	 *
	 * @param[in] query string A SQL command to be executed.
	 * @param[in] primary_key string (optional, defaults to NULL). If supplied,
	 *     then the results should be indexed by the value found in this column.
	 */
	function get($query, $primary_key=NULL) {
		$found = array();

		$result = $this->query($query);
		if (is_null($result)) {
			return FALSE;
		}

		while($info = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$key = is_null($primary_key) ? NULL : $info[$primary_key];
			$found[$key] = $info;
		}

		return $found;
	}
}
?>
