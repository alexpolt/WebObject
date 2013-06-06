<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.ru
    */

	class Itr_All implements Iterator {
		private $object;
		private $data;
		private $page;

		function __construct( $object ) { 
			$this->object = $object; 
			$this->page = 1; 
		}

		
        	function current() {
			try {
			    $key = key( $this->data );
			    return $this->object->$key;
			} catch ( EX_AUTH_SCREENED $e ) {
			    return Web_Spec_Screened( $this->object, $key );
			} catch ( EX_AUTH_PASSWORD $e ) {
			    return Web_Spec_Password( $this->object, $key );
			}
		}

        	function valid() { 
			if( current( $this->data ) === false ) {
				$this->page++;
				$this->object->selectPage( $this->page );
				$this->data = $this->object->getData();
				if( empty( $this->data ) ) return false;
				else reset ( $this->data );
			}
		return true;
		}

        	function next() { 
			try {
			    next( $this->data ); 
			    $key = key( $this->data );
			    $val = $this->object->$key;
			} catch ( EX_AUTH_SCREENED $e ) {
			} catch ( EX_AUTH_PASSWORD $e ) {
			} catch ( EX_AUTH_DENIED $e ) {
			    $this->next();			    
			}
		}

        	function prev() { prev( $this->data ); }

        	function rewind() { 
			    if( ! $this->object->isSelected() ) 
					    $this->object->select(); 
			    $this->data = $this->object->getDataArray();
			    reset( $this->data ); 
		}

        	function key() { return key( $this->data ); }



	}



