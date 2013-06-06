<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Str_BanIP extends Str_Simple {
	    
	    const CREATED = 0, VALUE = 1;
	    const MAX_TIME = 7776000; // 3 months

	    function __construct() {
		    parent::__construct( 'ban_ip' );
	    }
	    function initialize( $opts = array() ) {
		    $job = Std_Delegat();
		    $job->setDelegat( $this, 'maintenance' );
		    SYS::$TREE->cron->addJob( Web_Cron::TABLES, $job );
	    }
	    function maintanance() {
		    foreach( $this->data as $name => $value ) {
			    $span = SYS::getTime() - $value[ self::CREATED ];
			    if( $span > self::MAX_TIME ) unset( $this->$name );
		    }
	    }

            function getDataType( $name ) { return TYPE::PLAIN; }
            function setDataType( $name, $value ) { }
            function getDataValue( $name ) { return $this->data[ $name][ self::VALUE ]; }
            function setDataValue( $name, $value ) { 
		    $this->data[ $name ] = array(); 
		    $this->data[ $name ][ self::CREATED ] = SYS::getTime();
		    $this->data[ $name ][ self::VALUE ] = $value; 
	    }

    }




