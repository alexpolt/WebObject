<?php

    /**
     * SequenceGen. Utilizes LOCK TABLES ... WRITES. And updates a column.
     * 
    */

    class Seq_DB {
	private static $storage;

	static function genNewId($typename) {
	    if(! is_string($typename) or strlen($typename) == 0 ) {
			    ERROR::raiseError( __METHOD__ . ' Need typename as argument' );
			    return NULL;
	    }
	    $storage = is_null( self::$storage )
	    $storage->lock();
	    $update = array('typename' => $typename,'id'=>'id+1');
	    $storage->updateRow($update);
	    $data = $storage->fetchData(array('typename'=>$typename));
	    $newid = $data[0]['id'];
	    $storage->unlock();
	    if(! $newid ) 
		    throw new EX_ERROR( 'Failed to create new id.' );
	    return $newid;
	}


    }
			/*	Alex Poltavsky, 2007	*/
?>