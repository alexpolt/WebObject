<?php

    include_once "NODE.php";
    
    class db__collection extends NODE implements Iterator, Countable, I_COL {
	    public $storage;
	    protected $data = array();
	    protected $rowsToSave = array();
	    protected $objectCache = array();
	    protected $ntoa = array();
	    protected $aton = array();

	    protected $restored;
	    protected $saveState;

	    const PAGE_COUNT = 25;
	    const UPDATE_TIME = 3600;//86400;

            private static $table = array (
                "id"  => array("type" => "int", "not null" => 1, "unsigned" => 1),
                "name" => array("type" => "varchar(255)", "not null" => 1),
                "objid"  => array("type" => "int", "not null" => 1, "unsigned" => 1),
                "typeid" => array("type" => "smallint", "default" => 0, "not null" => 0, "unsigned" => 1),
                "file" => array("type" => "varchar(255)", "default" => "\"\""),
                "ownerid" => array("type" => "int", "default" => 0, "not null" => 1, "unsigned" => 1),
                "access" => array("type" => "int", "default" => 4, "not null" => 1, "unsigned" => 1),
                "flags" => array("type" => "smallint", "default" => 0, "not null" => 0, "unsigned" => 1),
                "tag1" => array("type" => "smallint", "default" => 0, "unsigned" => 1),
                "tag2" => array("type" => "smallint", "default" => 0, "unsigned" => 1),
                "tag3" => array("type" => "smallint", "default" => 0, "unsigned" => 1),
                "count" => array("type" => "int", "default" => 0, "not null" => 0, "unsigned" => 1),
                "status" => array("type" => "char(1)"),
                "created" => array("type" => "int", "default" => "0", "not null" => 0, "unsigned" => 1),
                "options"=> array("charset" => "UTF8", "_collate" => "utf8_general_ci",
				    "unique"=>array("id"=>"id", "name"=>'name(15)'),
				    "index"=>array("index1"=>array("tag1"=>"tag1"),
						    "index2"=>array("tag2"=>"tag2"),
						    "index3"=>array("tag3"=>"tag3"),
						    "index4"=>array("objid"=>"objid"),
						    "index5" => array("created"=>"created"),
						    "index6" => array("status"=>"status")),
				 )
            );

	    public $info = array('title_ru'=>'Коллекция','title_en'=>'Collection');

            private static $serviceTable = array (
                'prevpage' =>
                    array('method'=>'','label_ru'=>'Пред. страница','label_en'=>'Prev page',
                            'js'=>'var d=Z.menu.node.data;if(!d.page)d.page=0;d.opts={page:d.page};if(d.page>0)d.page--;Z.admin.refresh();', 'params'=> array(),
                        ),
                'nextpage' =>
                    array('method'=>'','label_ru'=>'След. страница','label_en'=>'Next page',
                            'js'=>'var d=Z.menu.node.data;if(!d.page)d.page=0;d.opts={page:++d.page};Z.admin.refresh();', 'params'=> array(),
                        ),
                'filter' =>
                    array('method'=>'','label_ru'=>'Фильтр','label_en'=>'Filter',
                            'js'=>'Z.tags.filter()', 'params'=> array(),
                        ),
            );



	    public function __construct( $id, $tableName ) {
		    parent::__construct( $id, $tableName );
		    SYS::addEvent($this, 'destruct', SYS::EVENT_SHUTDOWN);
		    $this->storage = db__storageMysql::getInstance( $tableName, $this->getTable() );
	    }
	    function wakeUp($parent, $name) {
		    parent::wakeUp($parent, $name);
	    }
	    public function destruct() { 
		    $this->saveState();
	    }
	    function getTable() { return self::$table; }
	    public function __destruct(){}
	    private function __toString(){}

            static function getInstance() {
                    $args = func_get_args();
                    $id = isset($args[0]) ? $args[0] : NULL;
                    $tableName = isset($args[1]) ? $args[1] : NULL;
                    if(! $tableName or is_null($id) ) throw new EX_CONSTRUCTOR( __METHOD__.', table = '.$tableName );
            	    return new self( $id, $tableName );
            }
            static function createInstance() {
                    $args = func_get_args();
                    $tableName = isset($args[0]) ? $args[0] : __CLASS__;
                    if(! $tableName )
                                    throw new EX_CONSTRUCTOR( __METHOD__.' table = '.$tableName );
                    if(SYS::$ADMIN_MODE) self::installTable( $tableName );
                    $id = SYS::genNewId( __CLASS__ );
                    return call_user_func( array(__CLASS__,'getInstance'), $id, $tableName );
            }

            static function installTable($tableName, $table=NULL) {
		    $table = is_null($table) ? self::$table : $table;
		    if(SYS::$ADMIN_MODE) 
                	    db__storageMysql::installTable( $tableName, $table );
            }

	    function erase() {
		    $page = 0;
		    $count = $this->getCount();
		    while($count > 0) {
			$this->selectPage( array('page'=>$page));
			foreach($this as $k=>$v) {
				$v = $this->$k;
				if( is_object($v)
					and !$v->isLink() ) $v->erase();
			}
			$page++; $count -= self::PAGE_COUNT;
		    }
		    $this->storage->deleteRow( array('id'=>$this->getId()), true );
		    $this->saveState = 0;
	    }

    	    function __set($name, $value) {
									if(SYS::$DEBUG_PROP) debug(__METHOD__.': '.$name.', value '.gettype($value));
		    $name = strtolower($name);
		    if( (is_object($value) 
				    and ! $value instanceof NODE) 
						    or is_array($value) )
						    throw new EX_INVALID_OBJECT('name: '.$name.', class: '.get_class( $value ) );
		    if(! isset( $this->data[$name])) 
				    $this->selectRow( array('name' => $name));

		    if(! isset( $this->data[$name])) 
			    $this->rowsToSave[] = array($name, 'insert');
		    else 	$this->rowsToSave[] = array($name, 'update');

        	    $this->objectCache[$name] = $value;
		    if( is_object($value)) {
			    	    $value->wakeUp( $this, $name );

				    $this->data[$name] = $value->objectToArray();

				    if( $value instanceof web__object ) {
					    if( isset( $value->data->tags ) ) {
							$this->getTags( $name, $value->data->tags );
					    }
				    }
		    } else $this->data[$name] = $value;
		    $this->saveState = 1;
    	    }
	    function getTags($name, $tags) {
		    $tags = Tags::stringToArray($tags, true); 
		    $i = 0;
		    for($i=1;$i<=3;$i++) {
			$val = array_shift($tags);
			$this->data[$name]['tag'.$i] = $val; 
		    }
	    }


    	    function __get($name) {
									if(SYS::$DEBUG_PROP) debug(__METHOD__.': '.$name);
		if( empty($name)) throw new EX_WRONG_NAME(__METHOD__);
		$name = strtolower($name);
                if( isset( SYS::$WORDS->$name ) ) return NULL;

		if( isset( $this->objectCache[$name] ) ) return $this->objectCache[$name];
		if(! isset( $this->data[$name])) 
				$this->selectRow( array('name' => $name));
        	if( isset( $this->data[ $name ] )  ) {
		    $val = $this->data[ $name ];
		    if( is_array( $val ) ) {
			    try {
				return $this->objectCache[$name] = SYS::resurrectObject($name, $val, $this);
                            } catch(EX_ACCESS_DENIED $e) { if(SYS::$SKIP_FORBIDDEN) return NULL;
                                                    	    else throw $e; }

		    }
                return $val;
		}
    	    return NULL;
    	    }

	    function needSave() { 
		    $this->saveState = 1; 
		    foreach( $this->objectCache as $name => $value ) {
						    $this->$name = $value;
		    }
	    }

	    protected function saveState() {
		    foreach($this->rowsToSave as $arr) {
			    $row = $this->data[ $arr[0] ]; 
			    if(! is_array($row)) $row = array('name' => $arr[0], 'objid' => $row);
			    $row['id'] = $this->getId();
								if(SYS::$DEBUG_TRACE) debug( print_r($row, true));
			    if( $arr[1] == 'update' ) $this->updateRow( $row );	
			    else  { 
				    $row['created'] = SYS::getUnixTime();
				    $this->insertRow( $row );	
			    }
		    }
		    $this->rowsToSave = array();
	    }
	    protected function insertRow($row) {
		    return $this->storage->insertRow( $row );	
	    }
	    protected function updateRow($row) {
		    return $this->storage->updateRow( $row );	
	    }
	    function getCount() {
		    return $this->storage->countRows( array('id'=>$this->getId()) );
	    }
	    function getRowsCount() {
		    return count( $this->data );
	    }
	    private function selectRow(array $row) {
		    $row['id'] = $this->getId();
		    $data = $this->storage->fetchData( $row, true );
		    if( is_array($data) and count($data) ) {
				$row = $data[0];
				$this->ntoa[ $row['objid'] ] = $row['name'];
				$this->aton[ $row['name'] ] = $row['objid'];
				if( isset( $row['typeid'] ) )
					$this->data[ $row['name'] ] = $row;
				else 	$this->data[ $row['name'] ] = $row['objid'];
		    }
	    }
	    function selectPage($opts) {
		    $this->data = array();
		    $page = isset($opts['page']) ? $opts['page'] : 0;
		    $tags = isset($opts['tags']) ? $opts['tags'] : NULL;
		    $name = isset($opts['name']) ? $opts['name'] : NULL;
		    $created = isset($opts['created']) ? $opts['created'] : NULL;
		    $hack = isset($opts['hack']) ? $opts['hack'] : NULL;
		    $status = isset($opts['status']) ? $opts['status'] : NULL;
		    $limit = NULL; $where = NULL;

		    if(! isset($opts['all'])) {
			$pageCount = isset($opts['page_count']) ? $opts['page_count'] : self::PAGE_COUNT;
			$limit = ($page * $pageCount).', '.$pageCount;

			if(! is_null($tags)
				and ! empty($tags)) {
					$where = $this->prepareTags($tags, $opts);
			}
			if(! is_null($status)
				and ! empty($status) ) 
					if(is_null($where)) $where = 'status = '.db__mysql::escape($status);
					else  $where = ' AND status = '.db__mysql::escape($status);
			if(! is_null($name)
				and ! empty($name) ) 
					if(is_null($where)) $where = 'name like \''.addslashes($name).'%\'';
					else  $where = ' AND name like \''.addslashes($name).'%\'';
			if(! is_null($created)
				and ! empty($created) ) 
					if(is_null($where)) $where = 'created = ' . $name;
					else  $where = ' AND created = ' . $name;
			if(! is_null($hack)
				and ! empty($hack) ) 
					if(is_null($where)) $where = $hack;
					else  $where = ' AND '.$hack;
		    }

		    $select = array( 'limit' => $limit, 'order' => 'created desc' );

		    if(! is_null($where)) $select['where'] = $where;

		    if(! isset($opts['omit_id']))
			    $select['id'] = $this->getId();

		    $data = $this->storage->fetchData( $select, true );

		    if( is_array($data)
				and count($data) ) {
				$this->data = array();
				foreach( $data as $row ) {
					    $this->ntoa[ $row['objid'] ] = $row['name'];
					    $this->aton[ $row['name'] ] = $row['objid'];
					    if( isset($row['typeid']) and $row['typeid'] > 0 )
							$this->data[ $row['name'] ] = $row;
					    else 	$this->data[ $row['name'] ] = $row['objid'];
				}
		    } else $this->data=array();
	    }
	    protected function prepareTags($tags, $opts) {
		    $arr = Tags::stringToArray($tags);
		    if( empty($arr)) return NULL;
		    $tags = array(); $in = implode($arr,',');
		    for($i=1;$i<=3;$i++)
				$tags[]='tag'.$i.' in ('.$in.')';
		    $type = isset( $opts['and']) ? $type = ' AND ' : ' OR ';
		    return ' ( '.implode($tags, $type) . ' ) ';
	    }

	    private function move($name, $type) {
		    if(! isset($this->$name)) throw new EX_WRONG_NAME($name);
		    $createdTime = $this->data[$name]['created'];
		    $order = $type == 'upper' ? 'created asc' : 'created desc';
		    $where = $type == 'upper' ? 'created >= '.$createdTime : 'created <= '.$createdTime;

		    $data=$this->storage->fetchData( array('id'=>$this->getId(), 'where'=>$where,'order'=>$order,'limit'=>'0,2'), true);

		    if( isset($data[0])
			    and isset($data[1]) ) {
				    $created1 = $data[0]['created'];
				    $created2 = $data[1]['created'];
			    $update1 = $data[0]; $update1['created'] = $created2;
			    $update2 = $data[1]; $update2['created'] = $created1;
			    $this->storage->updateRow( $update1, true );
			    $this->storage->updateRow( $update2, true );
		    }
	    }
	    function moveUpper($name) {
		    $this->move($name, 'upper');
	    }
	    function moveLower($name) {
		    $this->move($name, 'lower');
	    }

	    function __unset($pos) { 
		    $val = $this->$pos;
			if( is_object($val)) {
				if(! $val->isLink() ) $val->erase();
			}
		    $this->storage->deleteRow( array( 'id'=>$this->getId(),'name' => $pos), true );
		    unset( $this->objectCache[$pos] ); 
	    }
    	    function __isset($pos) { 
		    $pos = strtolower($pos);
            	    if( isset( SYS::$WORDS->$pos ) ) return NULL;
		    if(! isset( $this->data[$pos] )) 
					$this->selectRow(array('name'=>$pos));
		    return isset( $this->data[$pos] ); 
	    }

    	    function current() { return current( $this->data ); }
    	    function valid() { return current( $this->data ) === false ? false : true; }
    	    function next() { return next( $this->data ); }
    	    function prev() { return prev( $this->data ); }
    	    function rewind() { return reset( $this->data ); }
    	    function key() { return key( $this->data ); }
    	    function count() { return count( $this->data ); }

    	    function ren($oldname, $newname) { 
		if(! isset($this->$oldname) ) throw new EX_WRONG_PATH( $oldname );
		$this->$newname = $this->$oldname;
		$this->storage->deleteRow( array( 'id'=>$this->getId(), 'name' => $oldname ) );
	    }
	    function delProp($name) {
		$this->storage->deleteRow( array( 'id'=>$this->getId(),'name' => $name));
	    }
	    function delObject($name) {
		return $this->__unset( $name );
	    }
	    function makeLink($name, $o) {
		if(! $o instanceof web__object ) throw new EX_INVALID_OBJECT( get_class($o) );
		$type = $o->getType();
		$id = $o->getId();
		$file = $o->getFile();
		$newinst = NULL;
		$newInst = call_user_func( array( $type, 'getInstance'), $id, $file );
		$newInst->isLink(1);
		$this->$name = $newInst;
	    }


        public function nameToId($name) {
                SYS::isValidIdentifier($name);
                if(! isset($this->aton[$name]))
                            $this->selectRow(array('name'=>$name));
                if( isset($this->aton[$name]) ) {
                            $id = $this->aton[$name];
                            return $id;
                }
        return NULL;
        }

        public function idToName($id) {
            if(! is_numeric($id))
                            throw new EX_WRONG_PARAMETER(__METHOD__.' wrong arguments: '.$id);
            if(! isset($this->ntoa[$id]))
                            $this->selectRow(array('id'=>$this->getId(), 'objid'=>$id), true);
            if( isset($this->ntoa[$id]) )
                            return $this->ntoa[$id];
            throw new EX_NOT_FOUND( __METHOD__.': '.$id );
        }


        function getServiceTable() {
            return self::$serviceTable;
        }

        function getAccess() {
            if(!is_object($this->getParent())) return parent::getAccess();
            return $this->getParent()->getAccess();
        }
        function getOwner() {
            if(!is_object($this->getParent())) return parent::getOwner();
            return $this->getParent()->getOwner();
        }

    }


?>