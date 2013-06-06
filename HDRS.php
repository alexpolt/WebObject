<?php


    class HDRS {
	    
	    static $CACHING = false;
	    static $LAST_MODIFIED;
	    static $EXPIRES;
	    static $CONTENT_TYPE = 'text/html; charset=UTF-8';
	    static $CHARSET = 'UTF-8';
	    static $CONTENT_LENGTH;

	    static $HEADERS = array();
	    static $COOKIES = array();

            static function init() {
            }

	    static function send() {
		if( headers_sent() ) { echo '<!-- Headers! -->'."\n"; return; }

		if( ! self::$CACHING ) {
			header('Pragma: no-cache');
			header('Cache-Control: post-check=0, pre-check=0');
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Expires: Mon, 27 Jan 1997 00:00:00 GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		} else {
			if( self::$EXPIRES )
				header( 'Expires: ' . self::$EXPIRES );
			if( self::$LAST_MODIFIED )
				header( 'Last-Modified: ' . self::$LAST_MODIFIED );
		}
		if( ! empty( self::$HEADERS ) )
			foreach( self::$HEADERS as $v) header( $v );

		if( self::$CONTENT_LENGTH )
			header( 'Content-Length: ' . self::$CONTENT_LENGTH );

			header( 'Content-Type: ' . self::$CONTENT_TYPE );

		self::sendCookies();
    	    }

	    static function enableCaching( ) {
		self::$CACHING = true;
	    }
	    static function disableCaching( ) {
		self::$CACHING = false;
	    }
	    static function setLastModified( $unixtime ) {
		self::$LAST_MODIFIED = gmdate( 'D, d M Y H:i:s', $unixtime ).' GMT';
	    }
	    static function setExpires( $unixtime ) {
		self::$EXPIRES = gmdate( 'D, d M Y H:i:s', $unixtime ).' GMT';
	    }
	    static function setContentType( $type ) {
		self::$CONTENT_TYPE = $type;
	    }
	    static function setContentLength( $len ) {
		self::$CONTENT_LENGTH = $len;
	    }
	    static function setHeader($header, $value) {
		self::$HEADERS[ $header ] = $value;
	    }

    	    static function getCookie( $name ) {
                if( MODE::$CLI ) return NULL;
                return isset( $GLOBALS['_COOKIES'][ $name ] ) ? $GLOBALS['_COOKIES'][ $name ] : NULL;
    	    }
    	    static function setCookie( $name, $value, $expires = NULL, $path = NULL, $domain = NULL ) {
                if( MODE::$CLI ) return;
                $defPath = is_null( CFG::$COOKIE_PATH ) ? '/' : CFG::$COOKIE_PATH;
                $defDomain = is_null( CFG::$COOKIE_DOMAIN ) ? I::$SERVER_NAME : CFG::$COOKIE_DOMAIN;

                $string = rawurlencode( $name ) . '=' . rawurlencode( $value );
                is_null( $expires ) ? '' : $string .= '; expires=' . gmdate( 'D, d-M-Y H:i:s', $expires ) . ' GMT';
                is_null( $path ) ? $string .= '; path=' . $defPath : $string .= '; path=' . $path;
                is_null( $domain ) ? $string .= '; domain=' . $defDomain : $string .= '; domain=' . $domain;
                self::$COOKIES[ $name ] = $string;
    	    }
    	    static function clearCookie( $name ) {
                if( MODE::$CLI ) return;
                $string = rawurlencode( $name ) . '=';
                $string .= '; expires=' . gmdate( 'D, d-M-Y H:i:s', SYS::getTime() - 86400 ) . ' GMT';
                self::$COOKIES[ $name ] = $string;
    	    }

	    static function sendCookies() {
		if( ! empty( self::$COOKIES ) )
		foreach( self::$COOKIES as $cookie )
			header( 'Set-Cookie: ' . $cookie );
	    }

    	    static function set( $name, $value ) { self::$name = $value; }

    }



