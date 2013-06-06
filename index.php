<?php

        ini_set( "display_errors", 1 );
        error_reporting( E_ALL | E_STRICT );

        require_once 'INIT.php';

	$html = '';

	try {

    	    try {

    		SYS::init();
        	REQ::init();
		LANG::init();
    		AUTH::init();
    		MODE::init();
		HDRS::init();
		HEAD::init();

		SYS::$USER = USER::getRemoteUser();

		$obj = SYS::$SITE->resolvePath( REQ::$PATH );

		if( $obj instanceof Web_Object )
			    $html = $obj->exec( REQ::$OPTS );
	    
	    } catch ( EX $E ) { 
		$html = $E->handle();
		if( $E->directOutput() ) { 
			    HDRS::send();    
			    echo $html;
		return;
		}
	    }

	} catch ( EX $E ) { return; }

	
	HDRS::send();    
	HEAD::processHtml( $html );

	if( MODE::$AJAX )
		echo AJAX::wrap( $html );		
	else {
    		echo HEAD::htmlStart();
		if( strlen( $html ) > 0 ) {
			    SYS::$SITE->runtime['content'] = $html;
			    echo SYS::$SITE->views->content->exec();
		} else
			    echo SYS::$SITE->views->front->exec();
		echo HEAD::htmlEnd();
	}




