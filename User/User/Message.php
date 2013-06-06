<?php

	/*
    	    Alex Poltavsky, 2008
    	    www.alexclub.org
	*/


	class User_Message {
		private $msg = array(
		    'changedip' => array( 'ru' => ''
					    'en' => '' ),
		);


		function __get( $name ) { return isste( $this->msg[ $name ] ) ?  $this->msg[ $name ][ LANG::$CODE ] : NULL }

	}


