<?php
    /*
        Alex Poltavsky, 2008
        www.alexclub.org
    */

    class LANG extends Web_Single {
	private static $obj;
	private static $objs = array();
	static $CODE;
	
	function __construct( $code ) { parent::__construct( $code ); }

	static function init() {
                if( isset( REQ::$URL_HOOKS[ 'LANG' ] )
			    and array_search( REQ::$URL_HOOKS[ 'LANG' ], CFG::$LANGS ) ) {				    
		    self::$CODE = $code;
		} else 
		    self::$CODE = self::getRemoteLanguage();
		if( is_null( self::$CODE ) ) self::$CODE = CFG::$LANG;
	}
	
	static function setLang( $code ) { self::$CODE = $code; }
	static function getLang() { return self::$CODE; }
	static function getInstance() { new self( self::$CODE ); }

	static function _( $code ) {
		if( is_null( self::$obj ) ) self::$obj = self::getInstance();

		if( isset( self::$obj->data->$code ) ) {
			    return self::$obj->data->$code;
		} else self::createEntry( $code );
	}
	static function createEntry( $code ) {
		foreach( CFG::$LANGS as $ln ) {
		    $o = isset( self::$objs[ $ln ] ) ? self::$objs[ $ln ] : self::$objs[ $ln ] = new self( $ln );
		    $o->data->$code = $code . '_' . $ln;
		}
	}

	static function getRemoteLanguage() {
        	$A = getenv( 'HTTP_ACCEPT_LANGUAGE' );
        	if( empty( $A ) ) return NULL;
        	$M = NULL;
        	preg_match('/(\w{2,5})[;,]?/', $A, $M);
        	return isset( $M[1] ) ? $M[1] : NULL;
        }



    }


