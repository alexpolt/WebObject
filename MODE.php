<?php
    /*
        Alex Poltavsky, 2008
        www.alexclub.org
    */

    class MODE {

        // Modes
        static $DEV = 1;
        static $PDA;
        static $ADMIN;
        static $AJAX;
        static $GET;
        static $POST;
	static $CLI;
	
	static function init() {
		if( isset( REQ::$URL_HOOKS[ 'PDA' ] ) ) self::$PDA = 1;
		if( isset( REQ::$URL_HOOKS[ 'AJAX' ] ) ) self::$AJAX = 1;
                if( I::$API == 'cli' ) {
                            self::$CLI = 1;
                            CFG::$PROP_CACHING = 0;
                            MODE::$POST = MODE::$GET = false;
                }
                // GET or POST
                else if( isset( $_SERVER['REQUEST_METHOD'] )
                        	&& $_SERVER['REQUEST_METHOD'] == 'GET')
                                    MODE::$POST = ! MODE::$GET = true;
                else                MODE::$POST = ! MODE::$GET = false;
		
	}

	static function set( $name, $value ) { self::$name = $value; }
    }


