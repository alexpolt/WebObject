<?php

	/*
    	    Alex Poltavsky, 2008
    	    www.alexclub.org
	*/

	class Web_Journal extends Web_Object {
		const NORMAL = 0, WARNING = 1, ALERT = 2;

        	private $ext = array( 'data' => 'Str_Journal' );

		private $props = array( 'index', 'text' );		

        	// When new object is crated
        	function initialize( $opts = array() ) {
                	parent::initialize( $opts );
			$name = isset( $opts[ 'name' ] ) ? $opts[ 'name' ] : strtolower( $this->getParent()->getType() ) . $this->getId();
                	$this->data->initialize();
                	$this->data->ip = '1.1.1.1';//SYS::getRemoteIp();
                	$this->index->created = SYS::getTime();
        	}

		function __get( $name ) {
			$val = parent::__get( $name );
		return $val;
		}

		function __set( $name, $value ) {
			parent::__set( $name, $value );
			if( $name == 'tags' ) 
				    $this->tags->$name = $value;
		}		
		
				
		function getExtensions() { return array_merge( parent::getExtensions(), $this->ext ); }
		function getDataArray() { return array_merge( parent::getDataArray(), $this->props ); }
		

    
	}





