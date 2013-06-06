<?php

	/*
	 * Alex Poltavsky, 2008
	 * www.alexclub.ru
	 */

	class Ext_Sequence extends EXT {
	    private $parent;
		
	    function initNode( $parent, $name ) {
		    parent::initNode( $parent, $name );
		    $this->parent = $parent;
	    }

	    function genNewId() {
		    return SYS::$SEQ->genNewId( $this->parent->getTable() );
	    }

	    function delete() {}
	    
	}





