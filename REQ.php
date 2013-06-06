<?php

	class REQ {

	    static $URI;
	    static $PATH;
	    static $OPTS;
	    static $URL_HOOKS = array();
	    static $BROWSER = array();
	    
	    const REG_GET = '/^\w+$/';
	    const REG_PATH = '/^\/(?:index\.php)?([\w\/]+)(?:\.html)?(?:\/)?$/'; // Example: /index.php/news/post234.html/

	    static function init() {
		    global $_SERVER, $_POST, $_GET, $_COOKIE;
		    self::$URI = I::$URI;

		    // Get rid of quotes
		    if ( get_magic_quotes_gpc()) {
			    foreach( $_GET as $key => $val ) $_GET[ $key ] = stripslashes( $val );
			    foreach( $_POST as $key => $val ) $_POST[ $key ] = stripslashes( $val );
			    foreach( $_COOKIE as $key => $val ) $_COOKIE[ $key ] = stripslashes( $val );
		    }

		    // Handle GET arguments
		    foreach( $_GET as $name => $value ) {
			    self::$OPTS[ strtolower( $name ) ] = $value;
		    }
	
		    // jQuery AJAX
		    if( isset( $_SERVER['X-Requested-With'] ) 
			    && $_SERVER['X-Requested-With'] == 'XMLHttpRequest' ) MODE::$AJAX = 1;

		    // Create path from URI.
		    $match = NULL;
		    $path = str_replace( '//', '/', self::$URI );
		    preg_match( self::REG_PATH, self::$URI, $match );
		    if( isset( $match[1] ) ) {
				$pathNames = explode( I::$SL, $match[1] );
				$stop = false;
				foreach( $pathNames as $name ) {
					if( ! $stop && array_search( $name, CFG::$URL_HOOKS ) ) {
						self::$URL_HOOKS[ CFG::$URL_HOOKS ] = $name;
					} else $stop = true;
					array_push( self::$PATH, strtolower( $name ) );
				}
		    }

		    // Redirects handling. For instance: /favicon.ico => /static/icons/favicon.ico
		    $startName = self::$PATH[0];
		    foreach( CFG::$REDIRECTS as $name => $url )
			    if( $name == $startName ) throw new EX_REDIRECT( $url );
	    
		    self::detectBrowser();

	    }

	    /* Deprecated
	    static function popPath() { return array_pop( self::$CUR_PATH ); }
	    static function pushPath( $name ) { array_push( self::$CUR_PATH, $name ); }
	    static function shiftPath() { return array_shift( self::$CUR_PATH ); }
	    static function unshiftPath( $name ) { array_unshift( self::$CUR_PATH, $name ); }
	    */

	    static function isIE() { return self::$BROWSER[ 'ie' ]; }
	    static function isIEMac() { return self::$BROWSER[ 'iemac' ]; }
	    static function isGecko() { return self::$BROWSER[ 'gecko' ]; }
	    static function isOpera() { return self::$BROWSER[ 'opera' ]; }
	    static function isLynx() { return self::$BROWSER[ 'text' ]; }

	    private static function detectBrowser() {
            		$userAgent = isset( $GLOBALS['_SERVER' ]['HTTP_USER_AGENT' ] ) ? $GLOBALS['_SERVER' ]['HTTP_USER_AGENT' ] : NULL;
			if( is_null( $userAgent ) ) return;

        		self::$BROWSER[ 'ie' ]      = ( strpos( $userAgent, 'msie' ) !== false ) && ! ( strpos( $userAgent, 'opera' ) !== false );
        		self::$BROWSER[ 'opera' ]   = ( strpos( $userAgent, 'opera' ) !== false );
        		self::$BROWSER[ 'gecko' ]   = ( strpos( $userAgent, 'gecko' ) !== false && ! self::$BROWSER[ 'konq' ] );
        		self::$BROWSER[ 'text' ] = ( strpos( $userAgent, 'links' ) !== false ) || ( strpos( $userAgent, 'lynx' ) !== false ) || ( strpos( $userAgent, 'w3m' ) !== false);
        		self::$BROWSER[ 'konq' ] = self::$BROWSER[ 'safari' ] = ( strpos( $userAgent, 'konqueror' ) !== false || strpos( $userAgent, 'safari' ) !== false );
        		self::$BROWSER[ 'iemac' ]   = self::$BROWSER[ 'ie' ] && ( strpos( $userAgent, 'mac' ) !== false );
		    /*
        		self::$BROWSER[ 'ie4' ]     = self::$BROWSER[ 'ie' ] && ( strpos( $userAgent, 'msie 4' ) !== false );
        		self::$BROWSER[ 'ie5' ]     = self::$BROWSER[ 'ie' ] && ( strpos( $userAgent, 'msie 5' ) !== false );
        		self::$BROWSER[ 'ie6' ]     = self::$BROWSER[ 'ie' ] && ( strpos( $userAgent, 'msie 6' ) !== false);
        		self::$BROWSER[ 'aol' ]   = ( strpos( $userAgent, 'aol' ) !== false );
        		self::$BROWSER[ 'webtv' ] = ( strpos( $userAgent, 'webtv' ) !== false );
        		self::$BROWSER[ 'aoltv' ] = self::$BROWSER[ 'tvnavigator' ] = ( strpos( $userAgent, 'navio' ) !== false) || ( strpos( $userAgent, 'navio_aoltv' ) !== false );
        		self::$BROWSER[ 'hotjava' ] = ( strpos( $userAgent, 'hotjava' ) !== false );

        		self::$BROWSER[ 'ns' ] = ( strpos( $userAgent, 'mozilla' ) !== false ) && ! ( strpos( $userAgent, 'spoofer' ) !== false) && ! ( strpos( $userAgent, 'compatible' ) !== false ) 
							&& ! ( strpos( $userAgent, 'hotjava' ) !== false ) && ! ( strpos( $userAgent, 'opera' ) !== false ) && ! ( strpos( $userAgent, 'webtv' ) !== false) ? 1 : 0;
		    */
            }


	}

