<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class Seq_DB extends NODE implements IteratorAggregate {
	    private static $instance;
	    private $storage;
	    
	    function __construct() {
		    parent::__construct();
		    $this->storage = new DB_Table_Seq( __CLASS__ );
	    }

    	    function genNewId( $typename ) {
        	    $storage->lock();
        	    $update = array( 'typename' => $typename, 'id' => 'id+1' );
        	    $storage->update( $update );
        	    $data = $storage->fetchRow( array('typename' => $typename) );
        	    $newid = $data[0]['id'];
        	    $storage->unlock();
        	    if( ! $newid )
                	    throw new EX_ERROR( 'Failed to create new id.' );
            return $newid;
    	    }
	    
	    function genNewId( $name ) {
		    $lockname = $this->getType();
		    SYS::lock( $lockname );
			    if( ! isset( $this->storage->$name ) )
						$this->storage->$name = 0;
			    $newid = ++$this->storage->$name;
			    $this->storage->forceCommit();
		    SYS::unlock( $lockname );
        	    if( ! $newid )
                	    throw new EX_ERROR( 'Failed to create new id' );
		    return $newid;
	    }

            function getIterator() { return $this->storage->getIterator(); }


    }


    