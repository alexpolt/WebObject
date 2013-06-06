<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.ru
    */

	class Itr_Simple implements IteratorSeekable {
		private $object;
		private $data;
		private $pos = 0;

		function __construct( $object ) { $this->object = $object; }

		function setDataArray( $data ) { $this->data = $data; }
		
        	function current() {
			    $key = key( $this->data );
			    return $this->object->$key;
		}

        	function valid() { return current( $this->data ); }

        	function next() { 
			    next( $this->data ); 
			    $this->pos++;
			    $key = key( $this->data );
		return $this->object->$key;
		}

        	function prev() { 
			    prev( $this->data ); 
			    $this->pos--;
			    $key = key( $this->data );
		return $this->object->$key;
		}

        	function rewind() { 
			    if( ! $this->object->isSelected() ) 
					    $this->object->select(); 
			    $this->setDataArray( $this->object->getDataArray() );
			    reset( $this->data ); 
		}

        	function key() { return key( $this->data ); }

        	function seek( $pos ) {  }



	}



