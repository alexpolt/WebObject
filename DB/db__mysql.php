<?php
    /**
     * Mysql DB class. 
     *@package CMS RUSSIE
     *@subpackage basic classes
     *@author Alex Poltavsky, 2007. Www.alexclub.ru
     *@version 1.0
     */

    
    class db__mysql {
	private static $instance;

	private static $connected = false;
	private $cfg;
	private $rs;
	private $affectedRows;
	private $cache;
	private $errMsg;

	private $tables = array();	

	const E_EXISTS = 1050;
	const E_DUP = 1062;
	const E_COUNT = 1136;
	const E_SYNTAX = 1064;

	private function __construct () {
	    $cfg =  SYS::$MYSQL;
	    if( isset( SYS::$TREE->config->mysql ) ) {
			$mysqlCfg = SYS::$TREE->config['mysql'];
			if( isset( $mysqlCfg['passwd'] )
				and ! empty( $mysqlCfg['passwd'] ) ) $cfg = $mysqlCfg;
	    }
	    $this->cfg = $cfg;
	    self::$instance = $this;
	}
	private function makeConnect() {
	    $cfg = $this->cfg;

	    $this->db = mysql_connect($cfg['host'],$cfg['user'],$cfg['passwd']);

	    if(! $this->db) 
			throw new EX_DB (__METHOD__.': '.mysql_error());

	    if(! mysql_select_db($cfg['db']) ) 
			throw new EX_DB (__METHOD__.': '.mysql_error());
	    /**@TODO**/
	    //$this->cache = FACTORY::getInstance('CACHE','db');
	    self::$connected = true;
	}
	public static function getInstance () {
	    if(self::$instance) 
		return self::$instance;
		return self::$instance = new self();
	}
	private static function createInstance() {}
    
	function __destruct () {
	    // It doesnt allow us to finish gracefully
	    // mysql_close($this->db);
	}
    
	function query($sql) {
	    return $this->sql($sql);
	}

	function sql($sql) {
	    if(! self::$connected ) $this->makeConnect();
	    $this->errMsg = '';
	    $this->sql = $sql;
								if(SYS::$DEBUG_SQL) debugsql( $sql );
	    $this->rs = mysql_query($sql,$this->db);
	    $error = false;
	    if(! $this->rs) {
		$error = true;
		$errno = mysql_errno();	
		$errmsg = __METHOD__ . ': ';
		if( SYS::$ADMIN_MODE )
		    $errmsg .= 'SQL ('.$sql.')'."\n";
		if($errno == db__mysql::E_EXISTS)
		    $errmsg .= 'Got error: already exists. ';
		else if($errno == db__mysql::E_SYNTAX)
		    $errmsg .= 'Got error: syntax error. ';
		else if($errno == db__mysql::E_DUP) 
		    $errmsg .= 'Got error: duplicates. ';
		else if($errno = db__mysql::E_COUNT)
		    $errmsg .= 'Got error: column count doesnt match. ';
		else
		    $errmsg .= mysql_error();
		$this->errMsg = $errmsg;

		throw new EX_DB( $errmsg );
	    }
	    return true;
	}

	function tableExists($tablename) {
	    if(! count($this->tables)) {
		    $this->sql('show tables');
		    while($row = $this->fetchRow())
				    $this->tables[] = $row[0];
	    }

	    $result = in_array($tablename, $this->tables);
	    if( $result)
		return true;
	return false;
	}

	static function escape($str) {
	    return mysql_escape_string($str);
	}
    
	function insertId() {
	    return mysql_insert_id();
	}
	function fetchAssoc($sql='') {
	    if( $sql )
		$this->sql($sql);

	    if($this->rs && $this->numRows())
	        return mysql_fetch_assoc($this->rs);
	    else if( $this->rs ) return array();
	return false;
	}

	function fetchRow($sql='') {
	    if( $sql )
		$this->sql($sql);

	    if($this->rs && $this->numRows())
		    return mysql_fetch_row($this->rs);
	return false;
	}

	function fetchArray($sql='') {
	    if( $sql )
		$this->sql($sql);

	    if($this->rs && $this->numRows()) {
		$data = array();
		
		while($row = mysql_fetch_assoc($this->rs))
					    $data[] = $row;
		return $data;
		
	    } else if ($this->rs)
				    return array();
	return false;		
	}
	
	function affectedRows() {
	    return $this->rs ? mysql_affected_rows() : 0;
	}
	
	function numRows() {
	    return is_resource($this->rs) ? mysql_num_rows($this->rs) : 0;
	}
	
	function getErrorCode() {
	    return mysql_errno();
	}
	
	function getErrorMessage() {
	    return $this->errMsg;
	}
	function getErrorMsg() {
	    return mysql_error() ." \n".$this->sql;
	}
	
	function setCacheEngine(CACHE $obj) {
	    $this->cache = $obj;
	    return true;
	}
	
	function getCacheEngine() {
	    return $this->cache;
	}
    
        public function createTable($name,array $table) {
            $sql = "CREATE TABLE " . $name . " ( ";
            foreach($table as $k => $v) {
                if($k == "options" || $k == "tablename") continue;
                $sql .= "\t$k ".$v["type"];
                if( isset($v["auto_increment"]) ) $sql .= " AUTO_INCREMENT";
                if( isset($v["charset"]) ) $sql .= " CHARACTER SET ".$v["character set"];
                if( isset($v["collate"]) ) $sql .= " COLLATE ".$v["collate"];
                if( isset($v["unsigned"]) ) $sql .= " UNSIGNED";
                if( isset($v["default"]) ) $sql .= " DEFAULT ".$v["default"];
                if( isset($v["not null"]) ) $sql .= " NOT NULL";
                $sql .= ",\n";
            }
            if( isset($table["options"]["primary key"]) and is_array( $table["options"]["primary key"] ) ) {
                    $sql .= "\n\tPRIMARY KEY (";
                    $sql .= implode( $table["options"]["primary key"], ',' );
                    $sql .= "),";
            }
            if( isset($table["options"]["unique"]) and is_array( $table["options"]["unique"] ) ) {
                    $sql .= "\n\tUNIQUE (";
                    $sql .= implode( $table["options"]["unique"], ',' );
                    $sql .= "),";
            }
            if( isset($table["options"]["index"]) and is_array( $table["options"]["index"] ) ) {
		    foreach($table["options"]["index"] as $index)  {
                	$sql .= "\n\tINDEX (";
                	$sql .= implode( $index, ',' );
                	$sql .= "),";
		    }
            }
            $sql = substr($sql,0,strlen($sql)-1);

            $sql .= "\n ) \nCHARACTER SET " . $table["options"]["charset"];
            if( isset($table["options"]["collate"]) )
                $sql .= " \nCOLLATE " . $table["options"]["collate"];
            if( isset($table["options"]["auto_increment"]) )
                $sql .= " \nAUTO_INCREMENT = " . SYS::$AUTO_INCREMENT;

                $DB = db__mysql::getInstance();
                return $DB->sql($sql);
        }

    
    }
			    /*	Alex Poltavsky, 2007	*/
?>