<?php

    /*
        Alex Poltavsky, 2008
        www.alexclub.org
    */

    class Web_Cron extends Web_Base {

	    const MINUTE = 'minute', QUARTER = 'quorter', HOUR = 'hour', DAY = 'day', TABLES = 'tables';

            private $ext = array( self::MINUTE => 'Str_File', self::HOUR => 'Str_File', self::DAY => 'Str_File',
				    self::QUARTER => 'Str_File', self::TABLES => 'Str_File', );

	    function addJob( $time, $object ) {
		    if( ! isset( $this->ext[ $time ] ) 
			    || ! method_exists( $object, 'execute' ) )
					    throw new EX_FAILURE( __METHOD__ . ' ( ' . $type . ' ) ' );
		    $id = $this->sequence->genNewId();
		    $name = 'job' . $id . '_' .  strtolower( $object->getType() );
		    $this->$time->$name = $object;
	    }

            function getExtensions() { return array_merge( parent::getExtensions(), $this->ext ); }
 
    }



