<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class TYPE {

	const PLAIN = 0, ARAY = 1, OBJECT = 2;

        static function get( & $value ) { 
		    if( is_object( $value ) ) return self::OBJECT; 
		    if( is_array( $value ) ) return self::ARAY; 
	return self::PLAIN; 
	}


    }

