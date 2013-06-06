<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    abstract class Str_Base extends NODE implements IteratorAggregate {
	    private $table;
	    private $parent;

	    protected $data = array();
	    private $caching;
	    private $cache = array();

	    private $selected = false;
	    private $commited = false;
	    private $shutdown = false;

	    const TYPE = 0, VALUE = 1;

	    function __construct( $id, $table = NULL ) {		
		    parent::__construct( $id, $table );
		    $this->table = $table;
		    $this->caching = CFG::$PROP_CACHING;
	    }

	    function initNode( $parent, $name ) {
		    parent::initNode( $parent, $name );
		    $this->parent = $parent;
	    }
	    function initialize( $opts = array() ) {}

            function getTable() {
                        if( is_null( $this->table ) ) {
                                    $tbl = $this->getParent()->getTable();
                                    //$table = substr( strrchr( $tbl, I::$UN ), 1 );
                                    $table = $tbl;
                                    return $this->table = strtolower( $table . I::$UN . $this->getName() );
                        }
            return $this->table;
            }

	    function __get( $name ) {
                                                                if( DEBUG::$GET ) DEBUG::log( DEBUG::$STR['GET'].$this->getParent()->getName().' ('.$this->getParent()->getType().') '.__METHOD__.' ('.$name.')' );
                    // * check name cache
                    if( $this->caching && isset( $this->cache[ $name ] ) ) {
                                                                if( DEBUG::$CACHE ) DEBUG::log( DEBUG::$STR['CACHE'].$this->getParent()->getName().' ('.$this->getParent()->getType().') '.__METHOD__.' ('.$name.')' );
                    return $this->cache[ $name ];
                    }

 		    if( ! isset( $this->data[ $name ] ) ) $this->beforeGet( $name );

		    $val = NULL;

		    if( isset( $this->data[ $name ] ) ) {
			$type = $this->getDataType( $name );
			if( $type == TYPE::PLAIN ) $val = $this->restorePlain( $name );
			else if( $type == TYPE::ARAY ) $val = $this->restoreArray( $name );
			else if( $type == TYPE::OBJECT ) $val = $this->restoreObject( $name );
        	    return $this->caching ? $this->cache[ $name ] = $val : $val;
		    }
	    return $val;
	    }

	    function __set( $name, $value ) {
                                                        	if( DEBUG::$SET ) DEBUG::log( DEBUG::$STR['SET'].$this->getParent()->getName().'('.$this->getParent()->getType().') '. __METHOD__.' ('.$name.','.(is_object($value)?get_class($value):(is_array($value)?print_r($value,true):$value)).')' );
 		    if( ! isset( $this->data[ $name ] ) ) $this->beforeSet( $name );

		    if( isset( $this->data[ $name ] ) ) 
				$this->onUpdate( $name );
		    else 	$this->onInsert( $name ); 

		    $type = TYPE::get( $value );

		    if( $type == TYPE::PLAIN ) $this->storePlain( $name, $value );
		    else if( $type == TYPE::ARAY ) $this->storeArray( $name, $value );
		    else if( $type == TYPE::OBJECT ) $this->storeObject( $name, $value );

		    $this->needCommit();

                    if( ! $this->caching ) return;
                    if( ! is_array( $value ) )
                                $this->cache[ $name ] = $value;


	    }

	    function __isset( $name ) { if( ! isset( $this->data[ $name ] ) ) $this->beforeGet( $name ); return isset( $this->data[ $name ] ); }
	    function __unset( $name ) {
                                                                if( DEBUG::$UNSET ) DEBUG::log( DEBUG::$STR['UNSET'].__METHOD__.' ('.$name.')' );
 		    if( ! isset( $this->data[ $name ] ) ) 
					$this->beforeGet( $name );
		    if( ! isset( $this->data[ $name ] ) ) 
					throw new EX_NOT_FOUND( $name );

                    $OBJ = $this->$name;

                    if( is_object( $OBJ )
                                && ! $OBJ instanceof Web_Array 
				    && ! $OBJ->checkFlag( FLAG::LINK ) )
                                    			    $OBJ->delete();

		    unset( $this->data[ $name ] );
		    $this->onDelete( $name );
		    $this->needCommit();
                    if( $this->caching
                                && isset( $this->cache[ $name ] ) ) unset( $this->cache[ $name ] );

	    }

	    function getDataType( $name ) { return $this->data[ $name ][ self::TYPE ]; }
	    function getDataValue( $name ) { return $this->data[ $name ][ self::TYPE ]; }
	    function setDataType( $name, $type ) { if( ! isset( $this->data[ $name ] ) ) $this->data[ $name ] = array();
						    $this->data[ $name ][ self::TYPE ] = $type; }
	    function setDataValue( $name, $value ) { if( ! isset( $this->data[ $name ] ) ) $this->data[ $name ] = array(); 
						    $this->data[ $name ][ self::VALUE ] = $value; }

	    function restorePlain( $name ) { return $this->getDataValue( $name ); }

	    function restoreObject( $name ) { 
		    $objArray = $this->getDataValue( $name );
		    $type = SYS::getName( $objArray[ 'type' ] );
		    $object = call_user_func( array( $type, 'createFromNodeArray' ), $objArray );
		    $object->initNode( $this, $name );
	    return $object;
	    }

	    function restoreArray( $name ) { 
		    $array = $this->getDataValue( $name );
		    $obj = new Web_Array(); 
		    $obj->initNode( $this, $name );
		    $obj->data->setDataArray( $array );
	    return $obj;
	    }

	    function storePlain( $name, $value ) {
		    $this->setDataType( $name, TYPE::PLAIN );
		    $this->setDataValue( $name, $value );
	    }
	    function storeObject( $name, $object ) {
		    $object->initNode( $this, $name );
		    $this->setDataType( $name, TYPE::OBJECT );
		    $this->setDataValue( $name, $object->getNodeArray() );
	    }
	    function storeArray( $name, $array ) {
		    $this->setDataType( $name, TYPE::ARAY );
		    $this->setDataValue( $name, $array );
	    }

	    function needCommit() { if( ! $this->shutdown ) { SYS::registerShutdown( $this, 'commit' ); $this->shutdown = true; } $this->isCommited( false ); }
	    function commit() { 
		    if( $this->isCommited() ) return;		if( DEBUG::$COMMIT ) DEBUG::log( DEBUG::$STR['COMMIT'].$this->getParent()->getName().' ( '.$this->getTable()." )\n".print_r($this->data, true)."\n");
		    $this->storeData();
		    $this->clearCache();
		    $this->isCommited( true );
	    }
	    function beforeGet( $name = NULL ) { if( ! $this->isSelected() ) { $this->checkConnection(); $this->select( array('name' => $name ) ); } }
	    function beforeSet( $name = NULL ) { if( ! $this->isSelected() ) { $this->checkConnection(); $this->select( array('name' => $name ) ); } }
	    function isSelected( $flag = NULL ) { if( is_null( $flag ) ) return $this->selected; else $this->selected = $flag; }
	    function isCommited( $flag = NULL ) { if( is_null( $flag ) ) return $this->commited; else $this->commited = $flag; }
	    function forceCommit() { $this->isCommited( false ); $this->commit(); }

	    function getDataArray() { return $this->data; }
	    function setDataArray( $data ) { $this->data = $data; }

	    function getIterator() { return new Itr_Simple( $this ); }
	    function getIteratorPage( $page, $pageCount ) { return new Itr_Page( $this, $page, $pageCount ); }
	    function getIteratorAll() { return new Itr_Simple( $this ); }
	    function getIteratorPreload() { return new Itr_Preload( $this ); }

	    function clearCache() { $this->cache = array(); }

	    abstract function storeData();
	    abstract function checkConnection();
	    abstract function preloadModeOn();
	    abstract function preloadModeOff();

	    abstract function preload();
	    abstract function select( $opts = array() );
	    abstract function selectPage( $page = 1, $pageCount = NULL );
	    abstract function selectAll( );
	    abstract function search( $what );

	    abstract function onUpdate( $name );
	    abstract function onInsert( $name );
	    abstract function onDelete( $name );
	    abstract function lock();
	    abstract function unlock();

	    abstract function count();
	    
	    abstract function moveUp( $name );
	    abstract function moveDown( $name );
	    abstract function rename( $oldName, $newName );

            function makeLink( $name, $object ) {
		    $object->setFlag( FLAG::LINK );
                    $this->$name = $object;
            }

    }




