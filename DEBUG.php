<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class DEBUG {
            static $AUTOLOAD = 1;	// __autoload
            static $SQL = 1;		// SQL logging
            static $BACKTRACE = 0;	// includ backtrace in messages
            static $SHUTDOWN = 1;	// shutdown sequence debug
            static $TRACE = 0; 		// enable time trace
            static $SET = 1; 		// enable time trace
            static $GET = 1; 		// enable time trace
            static $UNSET = 1; 		// enable time trace
            static $CONSTRUCT = 1;	// watch after object instantiation
            static $NEW = 1;		// watch after object instantiation
            static $STORE = 1;		// enable time trace
            static $EXTENSION = 1;	// enable time trace
            static $SELECT = 1;		// enable time trace
            static $COMMIT = 1;		// enable time trace
            static $CACHE = 1;		// enable time trace
            static $HEAD = 1;		// enable time trace
	    static $STR = array('GET'=>'<< ', 'SET'=>'>> ', 'UNSET'=>'^ ', 'NEW'=>'NEW> ',
				'STR'=>'### ', 'SELECT'=>'+ ', 'COMMIT'=>'>>>> ', 'SQL'=>'!SQL ',
				'CACHE'=>'* ', 'EXT'=>'X-> ', 'DEL' => '% ');

	    static function sql( $msg ) {
		    self::log( '+++ ' . $msg . "\n" );
	    }
	    static function log( $msg ) {
		    echo $msg."\n";
    		    if( self::$BACKTRACE )
				debug_print_backtrace();
	    }

    }

    class LOGGING {
            static $LOG_FILE = 0;
            static $LOG_MAIL = 0;
	    static $LOG_SQL = 0;

	    static function set( $name, $value ) { self::$name = $value; }
    }



