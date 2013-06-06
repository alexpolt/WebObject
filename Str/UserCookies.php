<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Str_UserCookies extends Str_File {

	    private $propCache = array();

            function initNode( $parent, $name ) {
                    $this->parent = $parent;
                    parent::initNode( $parent, $name );
            }
            function propId( $name ) { return isset( $this->propCache[ $name ] ) ? $this->propCache[ $name ] :
                                                    $this->propCache[ $name ] = $this->parent->map->nameToId( $id ); }
            function propName( $id ) { return isset( $this->propCache[ $id ] ) ? $this->propCache[ $id ] :
                                                     $this->propCache[ $id ] = $this->parent->map->idToName( $id ); }

	    function __set( $name, $value ) {
		    $id = $this->propId( $name );
		    parent::__set( $id, $value );
		    $this->commit();
	    }
            function __get( $name ) { return parent::__get( $this->propId( $name ) ); }
            function __isset( $name ) { return parent::__isset( $this->propId( $name ) ); }
            function __unset( $name ) { parent::__unset( $this->propId( $name ) ); }
            function rename( $oldName, $newName ) {
                    $oldName = $this->propId( $oldName );
                    $newName = $this->propId( $newName );
		    parent::rename( $oldName, $newName );
	    }

	    function storeData() {
		    $this->parent->setCookieData( $this->getDataArray() );
	    }
	    function checkConnection() {}
	    function delete() {}
	    function select( $opts = array() ){ 
		    $this->setDataArray( $this->parent->getCookieData() );
	    }



    }




