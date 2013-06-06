<?php

    include_once "NODE.php";


	class db__list extends NODE implements Iterator, Countable, I_COL {

	    private $storage;
	    private $data = array();

            private static $table = array (
                "id" => array("type" => "int", "not null" => 1, "unsigned" => 1 ),
                "value" => array("type" => "text", "not null" => 1 ),
                "count" => array("type" => "int", "not null" => 1, "default" => "1", "unsigned" => 1 ),
                "options" => array("charset" => "UTF8", "_collate" => "utf8_general_ci",
                                "unique" => array( "id"=>"id", "value" => 'value(255)'),
                                "index" => array( "index1" => array("count" => 'count') ),
                                )
            );
            const PAGE_COUNT = 25;
            const PROP_LEN = 50;

		function __construct($id, $tablename) {
			parent::__construct( $id, $tablename );
			$this->storage = db__storageMysql::getInstance($tablename, self::$table);
		}
		static function createInstance() {
			$args = func_get_args();
			$id = SYS::genNewId( __CLASS__ );
			$tablename = isset($args[0]) ? $args[0] : NULL;
			if( empty($tablename)) throw new EX_CONSTRUCTOR('Need tablename');
			if(SYS::$ADMIN_MODE)
				    db__storageMysql::installTable($tablename, self::$table);
			return new self($id, $tablename);
		}
		static function getInstance() {
			$args = func_get_args();
			$id = isset($args[0]) ? $args[0] : NULL;
			$tablename = isset($args[1]) ? $args[1] : NULL;
			if( empty($tablename) or is_null($id)) throw new EX_WRONG_PARAMETER('Need tablename');
			return new self($id, $tablename);
		}
        	static function installTable($tableName) {
                	if(SYS::$ADMIN_MODE)
                        	db__storageMysql::installTable( $tableName, self::$table );
        	}

		function exists($value) {
                        if(strlen(trim($value))==0) throw new EX_WRONG_VALUE('Empty value');
			$data = $this->storage->fetchData( array('id'=>$this->getId(), 'value'=>$value));
			if( is_array($data)
				    and isset($data[0]) ) return true;
		return false;
		}
		function put($value) {
                        if(strlen(trim($value))==0) throw new EX_WRONG_VALUE('Empty value');
 			if($this->exists($value)) {
				$this->storage->updateRow(array('id'=>$this->getId(), 'value'=>$value));
			} else
				$this->storage->insertRow(array('id'=>$this->getId(), 'value'=>$value));
		}
		function remove($value) {
				$this->storage->deleteRow(array('id'=>$this->getId(), 'value'=>$value));
		}
		function __isset($name) { return $this->exists($name); }
		function __get($name) { 
                		$select = array('id'=>$this->getId(), 'value'=>$name);
				$data = $this->storage->fetchData( $select );
                		if( is_array($data)
                            		    and isset($data[0]) ) return $data[0]['count'];
		return NULL;
		}

		function selectPage($opts) {
		    $this->data = array();
                    $page = isset($opts['page']) ? $opts['page'] : 0;
                    $value = isset($opts['value']) ? $opts['value'] : NULL;
                    $limit = NULL; $where = NULL;

                    if(! isset($opts['all'])) {
                        $pageCount = isset($opts['page_count']) ? $opts['page_count'] : self::PAGE_COUNT;
                        $limit = ($page * $pageCount).', '.$pageCount;
                	if(! is_null($value)
                                and ! empty($value) and is_string($value))
                                        if(is_null($where)) $where = 'name like \'%'.addslashes($value).'%\'';
                                        else  $where = ' AND name like \'%'.addslashes($value).'%\'';
 
                    }

                    $select = array('id'=>$this->getId(), 'limit'=>$limit, 'order'=>'count desc');
		    $data = $this->storage->fetchData( $select, true );
                    if( is_array($data)
                                and count($data) ) {
				foreach($data as $row)
                            		    $this->data[ substr($row['value'], 0, self::PROP_LEN) ] = $row['count'];
                    } else $this->data=array();
			
		}
        	function current() { return current( $this->data ); }
        	function valid() { return current( $this->data ) === false ? false : true; }
        	function next() { return next( $this->data ); }
        	function prev() { return prev( $this->data ); }
        	function rewind() { return reset( $this->data ); }
        	function key() { return key( $this->data ); }
        	function count() { return count( $this->data ); }


	}


?>