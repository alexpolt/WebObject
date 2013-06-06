<?php

    
    class HEAD extends Web_Single {
	    private static $obj;

	    private static $JS_URL;
	    private static $CSS_URL;
	    private static $INCLUDE = array();

	    const LINK_CSS1 = ' #include_js(', LINK_CSS2 = ') ';
	    const LINK_JS1 = ' #include_js(', LINK_JS2 = ') ';

	    const FS = '-';
	    const CSS_DIR = 'css';
	    const JS_DIR = 'js';

	    static function init() {
		self::$obj = new self();
	    }

	    static function htmlStart() {
		$html = SITE::$DOCTYPE . HTML::$NL . '<HTML>' . HTML::$NL;
		$html .= '<HEAD>' . HTML::$NL;

		// TITLE
		$html .= HTML::$TB . '<TITLE>' . SITE::$TITLE . '</TITLE>'. HTML::$NL;

		// META
		foreach( SITE::$META as $name => $content ) 
			$html .= HTML::$TB . '<META name="' . $name . '" content="' . $content . '" />'. HTML::$NL;
		// HTTP_EQUIV
		foreach( SITE::$HTTP_EQUIV as $name => $content ) 
			$html .= HTML::$TB . '<META http-equiv="' . $name . '" content="' . $content . '" />'. HTML::$NL;
		// LINK
		foreach( SITE::$LINK as $key => $attrs ) {
			$html .= HTML::$TB . '<LINK ';
			foreach( $attrs as $name => $value )  $html .= $name . '="' . $value . '" ';
			$html .= ' />' . HTML::$NL;
		}
		// LINK CSS
		foreach( SITE::$CSS as $key => $URL )
			$html .= HTML::$TB . '<LINK rel="stylesheet" type="text/css" href="' . $URL . '?' . SITE::$CSS_VER . '" />' . HTML::$NL;

		// OBJECTS CSS
		if( ! is_null ( self::$CSS_URL ) )
			$html .= HTML::$TB . '<LINK rel="stylesheet" type="text/css" href="' . self::$CSS_URL . '" />' . HTML::$NL;

		$html .= '</HEAD>' . HTML::$NL . HTML::$NL;
		return $html;
	    }

	    static function htmlEnd() {
		$html = '';

		// SITE JS
		foreach( SITE::$JS as $key => $URL )
			$html .= '<script language="JavaScript" type="text/javascript" src="' . $URL  . '?' . SITE::$JS_VER . '" ></script>' . HTML::$NL;

		// INLINE JS
		foreach( self::$INCLUDE as $val ) {
			$html .= '<script>' . HTML::$NL; 
			$html .= $val; 
			$html .= '</script>' . HTML::$NL;
		}

		// OBJECTS JS
		if( ! is_null ( self::$JS_URL ) )
			$html .= '<script language="JavaScript" type="text/javascript" src="' . self::$JS_URL . '" ></script>' . HTML::$NL;

		// EXEC TIME
		$time = ''; if( CFG::$EXEC_TIME ) $time = SYS::getExecTime();

		$html .= HTML::$NL . $time . CFG::$MOTO . HTML::$NL . '</HTML>' . HTML::$NL;

	    return $html;
	    }

	    static function processHtml( $html ) {
		    $cssMatch = NULL;
		    // Find CSS includes
		    $res = preg_match_all( '/<!--' . self::LINK_CSS1 . '(.*?)' . self::LINK_CSS2 . '-->/', $html, $cssMatch, PREG_PATTERN_ORDER );
		    if( $res ) {
			    $css = array();
			    foreach( $cssMatch[1] as $cssFile ) {
						    $css[] = self::getFileId( $cssFile ); 		if( DEBUG::$HEAD ) DEBUG::log( '#CSS include( ' . $cssFile . ' )' );
			    }
			    sort( $css );
			    self::$CSS_URL = I::$SL . I::$STATIC . self::CSS_DIR . I::$SL . implode( $css, self::FS ) . self::FS . SITE::$CSS_VER . '.css';
												if( DEBUG::$HEAD ) DEBUG::log( '#CSS_URL ' . self::$CSS_URL );
			    if( ! file_exists( I::$ROOT . self::$CSS_URL ) ) {
					    SYS::checkAndCreateFile( I::$ROOT . self::$CSS_URL );
					    foreach( $cssMatch[1] as $cssFile ) {
							$text = file_get_contents( I::$ROOT . I::$SL . $cssFile );
							file_put_contents( I::$ROOT . self::$CSS_URL, $text, FILE_APPEND );
					    }
			    }
		    }

		    $jsMatch = NULL;
		    // Find JS includes
		    $res = preg_match_all( '/<!--' . self::LINK_JS1 . '(.*?)' . self::LINK_JS2 . '-->/', $html, $jsMatch, PREG_PATTERN_ORDER );
		    if( $res ) {
			    $js = array();
			    foreach( $jsMatch[1] as $jsFile ) {
						    $js[] = self::getFileId( $cssFile ); 		if( DEBUG::$HEAD ) DEBUG::log( '#JS include( ' . $jsFile . ' )' );
			    }
			    sort( $js );
			    self::$JS_URL = I::$SL . I::$STATIC . self::JS_DIR . I::$SL . implode( $js, self::FS ) . self::FS . SITE::$JS_VER . '.js';
												if( DEBUG::$HEAD ) DEBUG::log( '#JS_URL ' . self::$JS_URL );
			    if( ! file_exists( I::$ROOT . self::$JS_URL ) ) {
					    SYS::checkAndCreateFile( I::$ROOT . self::$JS_URL );
					    foreach( $cssMatch[1] as $jsFile ) {
							$text = file_get_contents( I::$ROOT . I::$SL . $jsFile );
							file_put_contents( I::$ROOT . self::$JS_URL, $text, FILE_APPEND );
					    }
			    }
		    }

	    }

	    static function getFileId( $file ) {
		    if( ! isset( self::$obj->data->$file  ) )
				self::$obj->data->$file = self::$obj->sequence->genNewId();
	    return self::$obj->data->$file;
	    }

	    static function linkCSS( $file ) { return '<!--' . self::LINK_CSS1 . $file . self::LINK_CSS2 . '-->' . HTML::$NL; }
	    static function linkJS( $file ) {  return '<!--' . self::LINK_JS1 . $file . self::LINK_JS2 . '-->' . HTML::$NL; }
	    static function includeJS( $js ) { self::$INCLUDE[] = $js; }



    }




