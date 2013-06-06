<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class Seq_File extends Web_Single {
	    
	    function genNewId( $name ) {
		    $lockname = $this->getType();
		    SYS::lock( $lockname );
			    if( ! isset( $this->data->$name ) )
						$this->data->$name = 0;
			    $newid = ++$this->data->$name;
			    $this->data->forceCommit();
		    SYS::unlock( $lockname );
		    return $newid;
	    }




    }


    