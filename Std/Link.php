<?php


	class Std_Link extends NODE {
		private $object;

		function setObject( $object ) {
			$this->object = $object;
			$this->meta->{ METADATA::TYPE } = $object->getTypeId();
		}

		function initNode( $name, $parent, $objectArray ) {
			parent::initNode( $name, $parent, $objectArray );
			$objectArray[ NODE::TYPEID ] = SYS::getName( $objectArray[ NODE::METADATA ][ METADATA::TYPE ] );
			$this->object = SYS::createObject( $objectArray );
		}

		function __set( $name, $value ) { $this->object->__set( $name, $value ); }
		function __get( $name ) { return $this->object->__get( $name ); }
		function __unset( $name ) { $this->object->__unset( $name ); }
		function __isset( $name ) { $this->object->__isset( $name ); }

		function __call( $name, $args ) { return call_user_func_array( array( $this->object, $name ), $args ); }

		function delete() {}

	}


