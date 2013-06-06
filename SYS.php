<?php
    /*
        Alex Poltavsky, 2008
        www.alexclub.org
    */


    class SYS {

        // Basic standard objects
        static $TREE;    // global data tree
        static $USER;    // current user
        static $SITE;    // current site tree - subtree of global tree
        static $REQ;    // request

	static function init() {
		if( CFG::$EXEC_TIME ) self::startExecTime();
		self::$MAP = new Map_System;
		self::$SEQ = new Seq_File;
		//self::$BAN = new Map_Ban;
		//self::$SITE = new Web_Tree();
		//self::readConfig();
        	ini_set( "display_errors", MODE::$DEV ? MODE::$DEV : CFG::$DISPLAY_ERRORS );
    		register_shutdown_function( array('SYS', 'FINISH'), & SYS::$SHUTDOWN_LIST );
		date_default_timezone_set( CFG::$TZ );
	}

	static function readConfig() {
		$array = self::$SITE->config->data->getDataArray();
		foreach( $array as $class => $params )
			foreach( $params as $cfgvar => $param )
				    call_user_func( array( $class, 'set' ), $cfgvar, $param );
	}

        // static function (  ) {}
	static $TIME;
        static function getTime() { return is_null( self::$TIME ) ? self::$TIME = time() : self::$TIME; }

	static $SEQ;
        static function genNewId( $name ) { return self::$SEQ->genNewId( $name ); }

	static $MAP;
        static function getId( $name ) { return self::$MAP->nameToId( $name ); }
        static function getName( $name ) { return self::$MAP->idToName( $name ); }


        static $SHUTDOWN_LIST = array();  // List of objects to destroy on exit
        static $FINISHING = false;
        static function registerShutdown( $obj, $method ) { 
            						if( DEBUG::$SHUTDOWN ) DEBUG::log( 'SHUTDOWN REG> ' . get_class( $obj ).'::'.$method );
		array_unshift( self::$SHUTDOWN_LIST, array( $obj, $method ) ); 
	}
        static function FINISH() {
                self::$FINISHING = true;
                $errors = NULL;
                    foreach(self::$SHUTDOWN_LIST as $array) {
			    $obj = $array[ 0 ];
			    $method = $array[ 1 ];
                        				if( DEBUG::$SHUTDOWN ) DEBUG::log( 'SHUTDOWN FIN> ' . get_class( $obj ).'::'.$method );
                            try {
                            	    call_user_func( array( $obj, $method ) );
                            } catch( Exception $e ) {
                                    if( MODE::$ADMIN or MODE::$DEV ) { 
						$errors .= $e->handle() . "\n------------\n";
				    }
                            }
		    }
        if( $errors ) echo $errors;
        }


        static $LOCKS = array();  // For holding file handles
        static function lock( $lockname ) {
		$filename = I::$DATA . I::$SL . CFG::$LOCK_DIR . I::$SL . $lockname;
		self::checkAndCreateFile( $filename );
		$f = self::$LOCKS[ $lockname ] = fopen( $filename, 'r' );
		flock( $f, LOCK_EX );
	}
        static function unlock( $lockname ) {
		if( ! isset( self::$LOCKS[ $lockname ] ) ) throw new EX_FAILURE( __METHOD__ .': ' . $lockname . ' was not found' );
		$f = self::$LOCKS[ $lockname ];
		flock( $f, LOCK_UN ); fclose( $f );
	}


        static function checkAndCreateFile( $filename ) {
		$dirname = dirname( $filename );
                if( ! file_exists( $dirname ) )  {
                                    $r = mkdir( $dirname, CFG::$DIRPERM, true );
                                    if( ! $r ) { $e = error_get_last(); throw new EX_FAILURE( $e['message'] ); }
                }
                if( ! file_exists( $filename ) ) {
                                    $r = touch( $filename );
                                    if( ! $r ) { $e = error_get_last(); throw new EX_FAILURE( $e['message'] ); }
                                    chmod( $filename, CFG::$FILEPERM );
                }
	}

        static function classNameToPath($name) { return str_replace( '_', I::$SL, $name ); }

	static $STARTTIME;
        static function getExecTime() { return microtime(true) - self::$STARTTIME; }
        static function startExecTime() { SYS::$STARTTIME = microtime(true); }

        static function getRemoteIp() {
	    global $_SERVER;
	    if( MODE::$CLI ) return NULL;
	    static $ip_address = NULL;

	    if( ! isset( $ip_address ) ) {
		$ip_address = $_SERVER['REMOTE_ADDR'];
		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) )
    		if ( ! empty( SYS::$REVERSE_PROXY_IPS ) 
			    && in_array( $ip_address, SYS::$REVERSE_PROXY_IPS, TRUE ) )
    					$ip_address = array_pop( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
	    }
        return $ip_address;
        }
        static function getRemoteHost() { return I::$HOST; }





    }




