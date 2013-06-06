<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class KEY {

	static $SESSION = 'o3459qab4894qasegfhawadfgjkqe5bv';
	static $FORM = '04mvbaff2txls95nbl2d6washyka';
	static $SERVER = '034nba584bsduhg934snv942nwb0sy45';


        static function set( $name, $value ) { self::$name = $value; }
    }


    class AUTH {
	public $USE_WWW_AUTH;

	const FORM_TIME = 'form_time', FORM_SIGN = 'form_sign';

	static function init() {

	    $ipban = new Str_BanIP();
	    $ip = SYS::getRemoteIp();
	    if( isset( $ipban->$ip ) )
			throw new EX_BAN_IP( $ip );

	}


	static function getFormSign() {
	    $time = SYS::getTime();
	    $sign = self::sign( KEY::$FORM, $time );
	    return  '<input type="" name="' . self::FORM_TIME . '" value="'.$time.'" />' .
		    '<input type="" name="' . self::FORM_SIGN . '" value="'.$sign.'" />';
	}

	static function checkPostSign() {
	    global $_POST;
	    $time = isset( $_POST[ self::FORM_TIME ] ) && is_numeric( $_POST[ self::FORM_TIME ] ) ? $_POST[ self::FORM_TIME ] : NULL;
	    $sign = isset( $_POST[ self::FORM_SIGN ] ) && strlen( $_POST[ self::FORM_SIGN ] ) == 32 ? $_POST[ self::FORM_SIGN ] : NULL;
	    
	    if( is_null( $time ) 
		    || is_null( $sign ) 
			|| CFG::$FORM_VALID_TIME < SYS::getTime() - $time 
			    || ! self::checkSign( KEY::$FORM, $time, $sign ) )
				    throw new EX_INVALID_POST( 'Post form is invalid. Sorry.' );
	}

	static function checkSign( $key, $data, $sign ) {
	    $trueSign = self::sign( $key, $data );
	    if( strcmp( $sign, $trueSign ) )
			    throw new EX_WRONG_SIGN( strtr( print_r( $data , true ), "\n", ' ' ) );
	return true;
	}

	static function sign( $key, $data ) {
	    return md5( $key . $data . $key );
	}


    }



