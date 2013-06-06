<?php

    include_once "NODE.php";
    
    class db__storage extends NODE implements Iterator, Countable, I_WEBDATA {
	    public $storage;
	    protected $data;
	    protected $map;
	    private $rowsToSave = array();

	    private $objectCache = array();

	    private $restored;
	    private $saveState;

	    public $info = array('title_ru'=>'Объект БД','title_en'=>'Object DB');

	    public function __construct( $id, $tableName ) {
		    parent::__construct( $id, $tableName );
		    SYS::addEvent($this, 'destruct', SYS::EVENT_SHUTDOWN);
		    $this->storage = db__storageMysql::getInstance( $tableName, $this->getTable() );
		    $this->map = $this->getMap( $tableName ); 
		    $this->restoreState();
	    }
	    function wakeUp($parent, $name) {
		    parent::wakeUp($parent, $name);
	    }
	    function getMap($tableName) {
		    return Map::getInstance( $tableName );
	    }
	    public function destruct() { 
		    $this->saveState();
	    }
	    function getTable() {}
	    public function __destruct() { }
	    private function __toString() {}
	    private static function getInstance() {}
	    private static function createInstance() {}

	    function erase() {
		    foreach($this as $k=>$v) {
			    $v = $this->$k;
			    if(  is_object($v)
					and ! $v->isLink()) $v->erase();
		    }
		    $row = array('id'=>$this->getId());
		    $this->storage->deleteRow( $row, true );
		    $this->saveState = 0;
	    }
    	    function __set($name, $value) {
									if(SYS::$DEBUG_PROP) debug(__METHOD__.': '.$name.', value '.gettype($value));
		    if(! $this->restored) $this->restoreState();

		    $name = strtolower($name);
		    $id = $this->map->nameToId( $name, true );

		    if( is_object($value) 
				    and ! $value instanceof NODE )
						    throw new EX_INVALID_OBJECT('name: '.$name.', class: '.get_class( $value ) );

		    if(! isset( $this->data[$id])) 
			    $this->rowsToSave[] = array($id,'insert');
		    else 	$this->rowsToSave[] = array($id,'update');

		    if( is_object($value)) {
				$this->objectCache[$name] = $value;
				$value->wakeUp( $this, $name );
        			$this->data[$id] = $value->objectToArray();
		    } else
        			$this->data[$id] = $value;

		    $this->saveState = 1;
    	    }

    
    	    function __get($name) {
									if(SYS::$DEBUG_PROP) debug(__METHOD__.': '.$name);
                if( empty($name)) throw new EX_WRONG_NAME(__METHOD__);
 		$name = strtolower($name);
		if( isset( SYS::$WORDS->$name ) ) return NULL;

		if(! $this->restored) $this->restoreState();

		if( isset( $this->objectCache[$name] ) ) return $this->objectCache[$name];

		$id = $this->map->nameToId( $name );

        	if( isset( $this->data[ $id ] )  ) {
		    $val = $this->data[ $id ];
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
		    if(! $this->saveState )
					return;
		    foreach($this->rowsToSave as $arr) {
			    $val = $this->data[ $arr[0] ];
			    $row = array( 'id'=>$this->getId(), 'attrid' => $arr[0] );
			    if( is_array($val) ) {
					    unset( $val['name'] );
					    $row = array_merge($row, $val);
			    } else $row['value'] = $val;
									if(SYS::$DEBUG_TRACE) debug( print_r($row, true));
			    if( $arr[1] == 'update' ) $this->storage->updateRow( $row );	
			    else  $this->storage->insertRow( $row );	
		    }
		    $this->saveState = 0;
		    $this->rowsToSave = array();
	    }
	    private function restoreState() {
		    $data = $this->storage->fetchData( array( 'id' => $this->getId() ), true );
		    if( is_array($data)
				and count($data) ) {
				foreach( $data as $row ) {
					if( $row['typeid'] > 0 ) {
					    $this->data[ $row['attrid'] ] = $row;
					} else
					    $this->data[ $row['attrid'] ] = $row['value'];
				}
		    } else $this->data = array();
		    $this->restored = 1;
	    }

    	    function __unset($pos) { 
		if(! $this->restored) $this->restoreState();
		$id = $this->map->nameToId($pos);
		if(! isset( $this->data[$id] )  ) throw new EX_WRONG_NAME ( $pos );
		    if(! isset( SYS::$MAGIC_KEYS[$pos]))  {
			    $val = $this->$pos;
			    if( is_object($val)) {
					if(! $val->isLink() ) $val->erase();
			    }
		    }
		    $this->storage->deleteRow( array('id' => $this->getId(), 'attrid'=> $id ) );
		    unset( $this->objectCache[$pos] ); 
	    }
    	    function __isset($pos) { 
		$pos = strtolower($pos);
		if( isset( SYS::$WORDS->$pos ) ) return NULL;

		if(! $this->restored) $this->restoreState();
		$id = $this->map->nameToId($pos); return !is_null($id) && isset( $this->data[$id] ); 
	    }

    	    function current() { return current( $this->data ); }
    	    function valid() { return current( $this->data ) === false ? false : true; }
    	    function next() { return next( $this->data ); }
    	    function prev() { return prev( $this->data ); }
    	    function rewind() { if(! $this->restored) $this->restoreState(); return reset( $this->data ); }
    	    function key() { return $this->map->idToName( key( $this->data ) ); }
    	    function count() { return count( $this->data ); }

    	    function ren($oldname, $newname) { 
		if(! $this->restored) $this->restoreState();
		if(! isset($this->$oldname) ) throw new EX_WRONG_PATH( $oldname );
		$this->$newname = $this->$oldname;
		$oldid = $this->map->nameToId( $oldname );
		$this->storage->deleteRow( array('id' => $this->getId(), 'attrid'=> $oldid ) );
	    }
	    function delProp($name) {
		$id = $this->map->nameToId( $name );
		$this->storage->deleteRow( array('id' => $this->getId(), 'attrid'=> $id ) );
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