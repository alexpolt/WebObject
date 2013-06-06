<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_REDIRECT extends EX {
	    private $link;

            function __construct( $link ) {
                parent::__construct( $link );
		$this->link = $link;
            }

	    function directOutput() { return true; }

            function handle() {
                HDRS::setHeader( 'Location', $this->link );
                return NULL;
            }
            function getHttpCode() { return 'HTTP/1.0 303 Permanently moved'; }

    }


