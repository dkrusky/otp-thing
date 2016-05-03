<?php
class db {
	var $params;
	var $_db;
	var $last_error;

	function __construct() {
		$this->params = Array();
		$this->params[] = '';

		$this->_db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($this->_db->connect_errno > 0){ $last_error = $this->_db->connect_error; throw new Exception("Connection Failed: " . $this->_db->connect_error); }
	}

	function __destruct() {
		try {
			$this->_db->close();
			$this->_db = null;
			$this->last_error = null;
			$this->params = null;
		} catch (Exception $e) {
			// ignore exception quietly on destruction of objects
		}
	}

	function add($value, $type='s') {
		$this->params[] = $value;
		$this->params[0] .= $type;
	}

	function resetparams() {
		$this->params = Array();
		$this->params[] = '';
	}

	function getError() {
		return $this->last_error;
	}

	function query($sql) {
		$p = $this->params;
		$bind_arguments = Array();
        foreach ($this->params as $recordkey => $recordvalue)
        {
            $bind_arguments[] = & $this->params[$recordkey];    # bind to array ref, not to the temporary $recordvalue
        }


		$qtype = explode(" ",strtolower(trim($sql)))[0];
		$result = false;
		$rows = Array();
		if(count($p) > 1) {
			// store procedure
			switch($qtype) {
				case 'insert':
					// return the last insert id
					$result = false;
					if($stmt = $this->_db->prepare($sql)) {
						call_user_func_array(array($stmt, 'bind_param'), $bind_arguments);
						$stmt->execute();
						$result = $stmt->insert_id;
						$stmt->close();
					}
					break;
				case 'select':
					// return result set
					$result = Array();
					if($stmt = $this->_db->prepare($sql)) {
						call_user_func_array(array($stmt, 'bind_param'), $bind_arguments);
						$stmt->execute();

						$meta = $stmt->result_metadata();
						$parameters = Array();
						while ($field = $meta->fetch_field()) {
							$parameters[] = &$row[$field->name];
						}
						call_user_func_array(array($stmt, 'bind_result'), $parameters);

						while ($stmt->fetch()) {
							foreach($row as $key => $val) {
								$x[$key] = $val;
							}
							$result[] = $x;
						}
						$stmt->close();
					}
					break;
				case 'delete':
					// return how many rows affected
					$result = false;
					if($stmt = $this->_db->prepare($sql)) {
						call_user_func_array(array($stmt, 'bind_param'), $bind_arguments);
						$stmt->execute();
						$result = $stmt->affected_rows;
						$stmt->close();
					}
					break;
				case 'update':
					// return how many rows affected
					$result = false;
					if($stmt = $this->_db->prepare($sql)) {
						call_user_func_array(array($stmt, 'bind_param'), $bind_arguments);
						$stmt->execute();
						$result = $stmt->affected_rows;
						$stmt->close();
					} else {
						$this->last_error = "Prepare failed: (" . $this->_db->errno . ") " . $this->_db->error;
						echo $this->last_error; die();
					}
					break;
			}
		} else {
			// normal query
			switch($qtype) {
				case 'insert':
					break;
				case 'select':
					$result = Array();
					if($resource = $this->_db->query($sql)) {
						for ($result = array(); $tmp = $resource->fetch_array(MYSQLI_ASSOC);) $result[] = $tmp;
					}
					break;
				case 'update':
					break;
				case 'delete':
					break;
				case 'alter':
					break;
				case 'create':
					break;
			}
		}

		if(empty($result)) { $result = false; }
		return $result;

	}

}