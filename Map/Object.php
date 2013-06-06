<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class Map_Object extends Web_Single {
	    
	    function nameToId( $name ) {
		    if( is_numeric( $name ) )  throw new EX_FAILURE( __METHOD__.'('.$name.')' );
		    if( $name === '__lastid' ) throw new EX_FAILURE( __METHOD__.'('.$name.')' );
		    if( isset( $this->data->$name ) )
					return $this->data->$name;

		    $lockname = $this->getType();
		    SYS::lock( $lockname );

			$nextid = $this->sequence->genNewId();
			$this->data->$name = $nextid;
			$this->data->$nextid = $name;
			$this->data->forceCommit();

		    SYS::unlock( $lockname );

	    return $nextid;
	    }

	    function idToName( $id ) {
		    if( isset( $this->data->$id ) )
					return $this->data->$id;
	    throw new EX_NOT_FOUND( __METHOD__.' ('.$id.')' );
	    }



    }


    
