<?php

    class db__storageMysql  {
        private static $instance = array();
        private static $tableCheck = array();
	
	private $tablename;
	private $table;
	public $DB;

	/**
	 * Constructor
    	 */
	protected function __construct ( $tablename, array $table ) {
	    $this->DB = db__mysql::getInstance();
	    $this->tablename = SYS::$DBPREFIX.$tablename;
	    $this->table = $table;
	}

        static function installTable($tableName, $table) {
                if(! $tableName or ! is_array($table) )
                            throw new EX_CONSTRUCTOR(__METHOD__.' need tablename and table');
		if( isset ( self::$tableCheck[$tableName] ) ) return;

		$DB = db__mysql::getInstance();
		if(SYS::$ADMIN_MODE
			and ! $DB->tableExists(SYS::$DBPREFIX.$tableName) ) {
				$DB->createTable( SYS::$DBPREFIX.$tableName, $table);
	        }
		self::$tableCheck[$tableName] = 1;
	}

        public static function getInstance() {
                $args = func_get_args();
                $tableName = isset($args[0]) ? $args[0] : NULL;
                $table = isset($args[1]) ? $args[1] : NULL;
                if(! $tableName or !is_array($table))
                            throw new EX_CONSTRUCTOR(__METHOD__.' need tablename and table');
                if( isset( self::$instance[$tableName] ) )
                                    return self::$instance[$tableName];
                self::$instance[$tableName] = new self($tableName, $table);
                return self::$instance[$tableName];
        }

        public function __destruct() {}

        public function insertRow(array $row) {
                if(! count($row)) 
                	    throw new EX_FAIL(__METHOD__ . ' I was expecting array. ' . print_r($row,true));

		$col_patch = $val_patch = ''; // if we need to add some details to query
		$keys = $vals = '';

		if( isset($this->table['options']['primary key']) 
				    and is_array($this->table['options']['primary key']) 
							and isset($this->table['options']['auto_increment']) ) {
		    $col_patch .=  implode( $this->table["options"]["primary key"], ',' ) . ',';
		    $val_patch .= '\'\',';
		    foreach($this->table['options']['primary key'] as $val) unset( $row[ $val ] );
		}
                foreach($row as $k => $v) {
                    $keys .= ','.$k;
                    $vals .= ',\''.db__mysql::escape($v).'\'';
                }
                    $keys = substr($keys,1);
                    $vals = substr($vals,1);

            	$sql = 'REPLACE INTO '.$this->tablename.'('.$col_patch.$keys.') VALUES ('.$val_patch . $vals.')';
		$this->DB->sql($sql);
		$result = $this->DB->affectedRows();
		if( $result )
			return true;
	}

        public function updateRow(array $row, $ignoreKeys = false) {

                if(! count($row) ) 
                	    throw new EX_FAIL( __METHOD__ . ' I was expecting array. ' . print_r($row,true) );

        	$sql = $setstr = '';
		$where = $this->prepareWhere($row, $ignoreKeys);

                foreach($row as $k => $v) {
                    $setstr .= ', '.$k.' = \''.db__mysql::escape($v).'\'';
                }
		if( isset ($this->table['count'] )) {
				$setstr .= ', count = count+1';
				if ( isset($row['row']['count']) ) unset($row['row']['count']);
		}
		$setstr = ' SET '.substr($setstr,1);
                $sql = 'UPDATE '.$this->tablename.$setstr . $where;
            	$this->DB->sql($sql);
		return $this->DB->affectedRows();
	}

        public function deleteRow(array $row, $ignoreKeys = false) {

                if(! count($row) ) 
                	throw new EX_FAIL( __METHOD__ . ' I was expecting array. ' . print_r($row,true) );

		$where = $this->prepareWhere($row, $ignoreKeys);

                $sql = 'DELETE FROM '.$this->tablename . $where;
            	$res = $this->DB->sql($sql);

		$result = $this->DB->affectedRows();
		    if( $result )
				return $result;
	}

        public function countRows(array $keys) {
	    $where = $this->prepareWhere($keys, true);
	    $sql = 'SELECT count(*) FROM '.$this->tablename . $where;
    	    $data = $this->DB->fetchRow($sql);
	    if( is_array($data) 
			and isset($data[0]) )
				    return $data[0];
	    else return NULL;
	}

        public function fetchData(array $keys,$ignoreKeys = false) {
            $data = array();
	    $orderby = $limit = $tags = '';

	    $where = $this->prepareWhere($keys,$ignoreKeys);

	    if( isset($keys['order']) ) {
		    $orderby = ' ORDER BY ' . $keys['order'];
		    unset( $keys['order'] );
	    }
	    if( isset($keys['limit']) ) {
		    $limit = ' LIMIT ' . $keys['limit'];
		    unset( $keys['limit'] );
	    }
	    if( isset($keys['where'])) {
		    if(! empty($where)) $where .= ' AND '.$keys['where'];
		    else $where = ' WHERE '.$keys['where'];
	    }
            $sql = 'SELECT * FROM '.$this->tablename . $where.$orderby.$limit;
    	    $data = $this->DB->fetchArray($sql);
	    return $data;
 	}

        public function clearStorage(array $keys) {
	    $where = $this->prepareWhere($keys);
            $sql = 'DELETE FROM '.$this->tablename . $where;
    	    return $this->DB->sql($sql);
	}

	private function prepareWhere(array &$keys, $ignoreKeys = false) {
	    $where_patch = array(); // where_patch - if we need to add some details to query
	    
	    $found_unique_keys = 0; //checking for existence of all necessary keys
	    $found_primary_keys = 0; //checking for existence of all necessary keys

	    $unique_keys = isset( $this->table['options']['unique'] ) ?  $this->table['options']['unique'] : array();
	    $primary_keys = isset( $this->table['options']['primary key'] ) ? $this->table['options']['primary key'] : array();

	    $keys_ = $keys;
    	    foreach($keys_ as $k => $v) {
		    if( isset( $primary_keys[$k] ) ) {
			if( strlen(trim($v)) == 0 ) throw new EX_DB('Wrong primary key value for '.$k.', v = '.$v);
			$found_primary_keys++;
			$where_patch[] = $k .  ' = ' . '\'' . db__mysql::escape($v) . '\'';
			unset($keys[$k]);
		    } else if( isset( $unique_keys[$k] )  ) {
			if( strlen(trim($v)) == 0 ) throw new EX_DB('Wrong unique key value for '.$k.', v = '.$v);
			$found_unique_keys++;
			$where_patch[] = $k .  ' = ' . '\'' . db__mysql::escape($v) . '\'';
			unset($keys[$k]);
		    }
	    }

	    if(! $ignoreKeys)
		if( ( count($unique_keys) != $found_unique_keys )  
			and ( count($primary_keys) != $found_primary_keys ) ) {
	    			throw new EX_FAIL ( __METHOD__ . ' Not all keys are present. '.print_r($keys, true) );
	    }

	    return count($where_patch) ? ' WHERE ' . implode ($where_patch,' AND ') : '';
	}

        public function insertId() {
	    return $this->DB->insertId();
	}
        public function lock() {
	    return $this->DB->sql('LOCK TABLES '.$this->tablename . ' WRITE');
	}
        public function unlock() {
	    return $this->DB->sql('UNLOCK TABLES');
	}
        //public function getSize();
    }

			/*	Alex Poltavsky, 2007	*/
?>