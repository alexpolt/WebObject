<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Str_Meta extends Str_File {

	    function __set( $name, $value ) {
		    parent::__set( $name, $value );
		    $this->commit();
	    }
	    function storeData() {
		    $obj = $this->getParent();
		    $obj->setMetaData( $this->getDataArray() );
		    $name = $obj->getName();
		    $data = $obj->getParent();
		    if( ! is_null( $data ) ) 
				    $data->$name = $obj;
	    }
	    function checkConnection() {}
	    function delete() {}
	    function select( $opts = array() ){ 
		    $this->setDataArray( $this->getParent()->getMetaData() ); 
	    }



    }




