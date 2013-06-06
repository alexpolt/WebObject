<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_DENIED extends EX {
	    private $directOutput;

            function __construct( $msg ) {
                parent::__construct( $msg );
            }

            function directOutput() {
		if( $this->directOutput ) return true;
	    return parent::directOutput();
	    }

	    function getHttpCode() { return 'HTTP/1.0 401 Unauthorized'; }

            function handle() {
                $api = php_sapi_name();
                if( $api !== 'CGI'
                             && AUTH::$USE_WWW_AUTH ) {
				    $this->directOutput = true; // make plain output in any case
                                    HDRS::setHeader( $this->getHttpCode() );
                                    HDRS::setHeader('WWW-Authenticate: Basic realm="Enter password"');
		return;
                }
	    return parent::handle();
            }

    }



