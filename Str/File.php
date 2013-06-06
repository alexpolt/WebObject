<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Str_File extends Str_Base implements ArrayAccess {
	    private $filename;
	    private $lockname;	    


	    function checkConnection() {
		    SYS::checkAndCreateFile( $this->getFileName() );
	    }

	    function getFileName() {
		    return is_null( $this->filename ) ? 
				$this->filename = I::$DATA . I::$SL . $this->getTable() . I::$SL . 'data_'. $this->getId() . '.php' : 
				$this->filename;
	    }
	    function getLockName() {
		    return is_null( $this->lockname ) ? 
				$this->lockname = 'Storage_' . $this->getTable() : $this->lockname;
	    }

	    function getDataType( $name ) { 
		    if( is_array( $this->data[ $name ] ) ) {
			    if( isset( $this->data[ $name ][ 'type' ] ) 
				    and isset( $this->data[ $name ][ 'table' ] )
					and isset( $this->data[ $name ][ 'id' ] )  ) return TYPE::OBJECT;
		    return TYPE::ARAY;
		    }
	    return TYPE::PLAIN; 
	    }
	    function setDataType( $name, $value ) { }
	    function getDataValue( $name ) { return $this->data[ $name]; }
	    function setDataValue( $name, $value ) { $this->data[ $name] = $value; }
            function storeObject( $name, $object ) {
		    parent::storeObject( $name, $object );
            }
	    function restoreObject( $name ) {
			    $obj = parent::restoreObject( $name );
	    return $obj;
	    }

	    function select( $opts = array() ) {
		    if( $this->isSelected() ) return;
		    $this->data = include $this->getFileName();
		    if( ! $this->data ) { 	$e = error_get_last(); 
						throw new EX_FAILURE( $e['message'] ); }
		    if( ! is_array( $this->data ) ) $this->data = array();
                                                                if( DEBUG::$SELECT ) DEBUG::log( '*'.__METHOD__.' ('.$this->getFileName().")\n".print_r($this->data, true)."\n");
	    $this->isSelected( true );
	    }

	    function storeData( ) {
		    $this->lock();
		    file_put_contents( $this->getFileName(), '<?php return ' . var_export( $this->data, true ) . ';'."\n" );
		    $this->unlock();
	    }

	    function delete() { 
		    $filename = $this->getFileName();
		    if( file_exists( $filename )
				    && is_writable( $filename ) ) unlink( $filename );
		    $this->data = array();
		    $this->cache = array();
	    }

    	    function lock() { SYS::lock( $this->getLockName() ); }
    	    function unlock() { SYS::unlock( $this->getLockName() ); }

            function moveUp( $name ) { $top = array_shift( $this->data ); array_push( $this->data, $top ); }
            function moveDown( $name ) { $bottom = array_pop( $this->data ); array_unshift( $this->data, $bottom ); }
            function rename( $oldName, $newName ) {
		    if( ! isset( $this->data[ $oldName ] ) ) throw new EX_NOT_FOUND( __METHOD__ . ' ( ' . $oldName . ' ) ' );
		    $value = $this->data[ $oldName ];
		    unset( $this->data[ $oldName ] );
		    $this->data[ $newName ] = $value;
	    }


            function offsetGet( $name ) { 
			if( ! isset( $this->data[ $name ] ) ) $this->beforeGet( $name );
	    return isset( $this->data[ $name ] ) ? $this->data[ $name ] : NULL; 
	    }
            function offsetSet( $name, $value ) { }
            function offsetUnset( $name ) { }
            function offsetExists( $name ) { }

	    function selectPage( $page = 1, $pageCount = NULL ) { }
	    function selectAll( ) { }
	    function search( $what ) { }

	    function onUpdate( $name ) { }
	    function onInsert( $name ) { }
	    function onDelete( $name ) { }
	    function count() { return count( $this->data ); }

	    function preload() { }
	    function preloadModeOn() { }
	    function preloadModeOff() { }
    }




