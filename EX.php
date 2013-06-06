<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX extends Exception {

	    private $date;

            function __construct( $msg = '' ) {
                $this->date = date( 'D, d M Y H:i:s', SYS::getTime() );
                parent::__construct( $msg );
            }
	    function getDate() { return $this->date; }

            function handle() {
        	if( CFG::$EX_TEXT_OUTPUT ) 
			return $this->text();
		else 	return $this->html();
            }

	    function directOutput() { return CFG::$EX_TEXT_OUTPUT; }
	    function getHttpCode() { return NULL; }
	    function getDescription() {}

	    function text() {
                $code = $this->getHttpCode();
		$msg = $this->getMessage();
		$desc = $this->getDescription();
                $file = $this->getFile();
        	$line = $this->getLine();
                $bt = $this->getTraceAsString();
	    return $bt."\n\n".$msg."\n";
	    }
	    function html() {
		$msg = $this->getMessage();
		$desc = $this->getDescription();
                $file = $this->getFile();
        	$line = $this->getLine();
                $bt = $this->getTraceAsString();
	    return $bt."\n\n".$msg."\n";
	    }

    }


    