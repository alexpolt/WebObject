<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Web_Array extends Web_Base {
                private $ext = array( 'data' => 'Str_Array' );
    
                function __construct() {
			parent::__construct( 0 );
		}

                function delete() {}

                function getExtensions() { return array_merge( parent::getExtensions(), $this->ext ); }

		function getIterator() { $this->data->getIterator(); }

    }



