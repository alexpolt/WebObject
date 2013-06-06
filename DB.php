<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class DB {
    	    static $CFG = array( 'master' => array( 'hostname' => 'localhost',
						    'user' => 'admin',
						    'password' => 'qwer',
						    'db' => 'cms', 
						    'charset' => 'utf8',
						    'class' => 'DB_Mysql' ) );
	    static $PREFIX = 'rcms_';
            static $AUTO_INCREMENT = 1000;
	    static $DEFAULT_CLUSTER = 'master';
	    static $PAGE_COUNT = 10;
	    static $CACHING = true;

    	    static function set( $name, $value ) { self::$name = $value; }

    }



    