<?php
	/*
    		Alex Poltavsky, 2008
    		www.alexclub.org
	*/

	class Std_Delegat extends Web_Single {

		function setDelegat( $object, $method, $args = NULL ) {
			$this->meta->object = $object->getNodeArray();
			$this->meta->method = $method;
			$this->meta->args = $args;
		}

		function execute() {
			$object = NODE::getInstance( $this->meta['object'] );
			$method = $this->meta->method;
			$args = $this->meta[ 'args' ];
		return call_user_func_array( array( $object, $method), $args );			 
		}

	}



