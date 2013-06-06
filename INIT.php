<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    require_once 'DEBUG.php';

    class I {
	static $URI;
	static $ROOT;
	static $HOST = 'xxx';
	static $DATA = 'data';
	static $STATIC = 'static';
	static $SL = '/';
	static $UN = '_';
	static $TIME;
	static $METHOD;
	static $SERVER_NAME;
	static $API;
    }
    I::$API = php_sapi_name();

    I::$ROOT = dirname(__FILE__);
    I::$URI = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '/';
    I::$HOST = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'localhost';
    I::$TIME = isset( $_SERVER['REQUEST_TIME'] ) ? $_SERVER['REQUEST_TIME'] : time();
    I::$SERVER_NAME = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : 'shell';
    I::$METHOD = isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'POST' ? 'POST' : 'GET';

    function __autoload( $class ) {
        if( class_exists( $class, false ) ) return true;
	$debug = DEBUG::$AUTOLOAD;


	$names = explode( I::$UN, $class );
        $filename = implode( $names, I::$SL ) . '.php';

        if( $debug ) echo __METHOD__.': searching for '.$filename.'...   ';

        if( file_exists( $filename ) ) {
                    if( $debug ) echo ' Found.'."\n";
                    require_once $filename;
        } else 
	if( substr($class,0,3) == 'EX_' ) {
    		    @eval('class '.$class.' extends EX {}') === FALSE ? die('Eval failed, class not found') : 1;
    	} else
	if( $debug ) echo ' Not found. '."\n";

        if( class_exists( $class, false ) ) return true;

    throw new EX_FAILURE( __METHOD__ . ' ( ' . $class . ' ) ' );
    }

