<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class HTML {
	static $SL = '/';
	static $NL = "\n";
	static $TB = "\t";
	static $BR = "<BR/>\n";

	private static function getCss( $css ) { return is_null( $css ) ? '' : ' class="' . $css . '"'; }

	static function h1( $text, $css = NULL ) { $css = self::getCss( $css ); return '<H1'.$css.'>' . $text . '</H1>' . self::$NL; }
	static function h2( $text, $css = NULL ) { $css = self::getCss( $css ); return '<H2'.$css.'>' . $text . '</H1>' . self::$NL; }
	static function h3( $text, $css = NULL ) { $css = self::getCss( $css ); return '<H3'.$css.'>' . $text . '</H1>' . self::$NL; }

	static function UL1( $css = NULL ) { $css = self::getCss( $css ); return '<UL'.$css.'>' . self::$NL; }
	static function LI() { return self::$TB . '<LI'.$css.'>' . self::$NL; }
	static function UL2() { return '</UL>' . self::$NL; }

    }


