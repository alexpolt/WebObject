<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Str_Object extends Str_Base {
	    private $table;
	    private $parent;
	    private $onUpdate = array(), $onInsert = array(), $onDelete = array();
	    private $propCache = array();

	    function initialize( $opts = array() ) {
		    parent::initialize( $opts );
		    $this->checkConnection();
		    $this->table->initialize( $opts );
	    }
	    function initNode( $parent, $name ) {
		    $this->parent = $parent;
		    parent::initNode( $parent, $name );
	    }
            function propId( $name ) { return isset( $this->propCache[ $name ] ) ? $this->propCache[ $name ] :
                                                    $this->propCache[ $name ] = $this->parent->map->nameToId( $id ); }
	    function propName( $id ) { return isset( $this->propCache[ $id ] ) ? $this->propCache[ $id ] : 
										 $this->propCache[ $id ] = $this->parent->map->idToName( $id ); }

	    function __get( $name ) { return parent::__get( $this->propId( $name ) ); }
	    function __set( $name, $value ) { parent::__set( $this->propId( $name ), $value ); }
	    function __isset( $name ) { return parent::__isset( $this->propId( $name ) ); }
	    function __unset( $name ) { parent::__unset( $this->propId( $name ) ); }

	    function restoreArray( $name ) {
		    $obj = parent::restoreArray( $name );
		    $obj->initNode( $this, $this->propName( $name ) );
	    return $obj;
	    }
	    function restoreObject( $name ) {
		    $obj = parent::restoreObject( $name );
		    $obj->initNode( $this, $this->propName( $name ) );
	    return $obj;
	    }

	    function storeData() {
		    $this->update();
		    $this->insert();
		    $this->del();
	    }

	    function update() {
		    foreach( $this->onUpdate as $propid ) {
			    $value = $this->data[ $propid ][ self::TYPE ] == TYPE::PLAIN ?
						    $this->data[ $propid ][ self::VALUE ] : serialize( $this->data[ $propid ][ self::VALUE ] );
			    $set = array( 'value' => $value );
			    $where = array( 'id' => $this->getId(), 'propid' => $propid );
			    $this->table->prepare();
			    $this->table->where( $where );
			    $this->table->update( $set, $where );
		    }
	    }
	    function insert() {
		    foreach( $this->onInsert as $propid ) {
			    $type = $this->data[ $propid ][ self::TYPE ];
			    $value = $this->data[ $propid ][ self::TYPE ] == TYPE::PLAIN ?
						$this->data[ $propid ][ self::VALUE ] : serialize( $this->data[ $propid ][ self::VALUE ] );
			    $data = array( 'id' => $this->getId(), 'propid' => $propid, 'value' => $value, 'type' => $type );
			    $this->table->insert( $data );
		    }
	    }
	    function del() {
		    foreach( $this->onDelete as $propid ) {
			    $where = array( 'id' => $this->getId(), 'propid' => $propid );
			    $this->table->prepare();
			    $this->table->where( $where );
			    $this->table->delete();
		    }
	    }

	    function checkConnection() {
		    $this->table = new DB_Table_Object( $this->getTable() );
	    }
	    function delete() {
		    $this->table->delete( array( 'id' => $this->getId() ) );
	    }

	    function select( $opts = array() ) { $this->selectAll(); }
	    function selectPage( $page = 1, $pageCount = NULL ) { $this->selectAll(); }
	    function selectAll( ) {
		    $this->table->prepare();
		    $this->table->where( array( 'id' => $this->getId() ) );
		    $rows = $this->table->select();
		    $data = array();
		    foreach( $rows as $row ) {
				$value = $row[ 'type' ] == TYPE::PLAIN ? $row[ 'value' ] : unserialize( $row[ 'value' ] );
				$data[ $row[ 'propid' ] ] = array(  self::TYPE => $row[ 'type' ], self::VALUE => $value );
		    }
		    $this->setDataArray( $data );
		    $this->isSelected( true );
	    }
	    function search( $what ) {}

	    function getDataArray() {
		    $data = array();
		    foreach( $this->data as $id => $value ) $data[ $this->propName( $id ) ] = $value;
	    return $data;
	    }

	    function onUpdate( $propid ) { $this->onUpdate[] = $propid; }
	    function onInsert( $propid ) { $this->onInsert[] = $propid; }
	    function onDelete( $propid ) { $this->onDelete[] = $propid; }

	    function lock() { $this->table->lock(); }
	    function unlock() { $this->table->unlock(); }

	    function count() { return $this->table->count(); }
	    
            function moveUp( $name ) {  }
            function moveDown( $name ) {  }
            function rename( $oldName, $newName ) {
		    $oldName = $this->propId( $oldName );
		    $newName = $this->propId( $newName );

                    if( ! isset( $this->data[ $oldName ] ) ) throw new EX_NOT_FOUND( __METHOD__ . ' ( ' . $oldName . ' ) ' );

                    $value = $this->$oldName;
                    unset( $this->$oldName );
                    $this->$newName = $value;
            }

            function preload() { }
            function preloadModeOn() { }
            function preloadModeOff() { }

    }


