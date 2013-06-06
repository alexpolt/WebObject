<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Str_Array extends Str_Base implements ArrayAccess {


            function __get( $name ) {
								if( DEBUG::$GET ) DEBUG::log( DEBUG::$STR['GET'].$this->getParent()->getName().' ('.$this->getParent()->getType().') '.__METHOD__.' ('.$name.')' );
                    if( ! isset( $this->data[ $name ] ) ) $this->beforeGet( $name );

                    if( isset( $this->data[ $name ] ) ) {
                            $v = & $this->data[ $name ];
                            if( is_array( $v ) ) {
                			$obj = new Web_Array( $v );
                			$obj->initNode( $this, $name );
                                        return $obj;
			    }
                    return $v;
                    }
            return NULL;
            }

            function __set( $name, $value ) {
								if( DEBUG::$SET ) DEBUG::log( DEBUG::$STR['SET'].$this->getParent()->getName().' ('.$this->getParent()->getType().') '.__METHOD__.' ('.$name.')' );
                    if( ! isset( $this->data[ $name ] ) ) $this->beforeSet( $name );

                    if( is_object( $value ) )
                                    throw new EX_FAILURE( __METHOD__.' ( $name, object['.get_class($value).'] )' );
                    $this->data[ $name ] = $value;
                    $this->commit();
            }

	    function checkConnection() {}
	    function storeData() {}
            function needCommit() {}
            function commit() {
                                                                if( DEBUG::$COMMIT ) DEBUG::log( __METHOD__.' ('.$this->getTable().")\n".print_r($this->getDataArray(), true)."\n");
		    $data = $this->getParent()->getParent(); // data object
		    $data->{ $this->getParent()->getName() } = $this->getDataArray();
		    if( $data instanceof Str_Array ) $data->commit();
            }

            function __isset( $name ) { return isset( $this->data[ $name ] ); }
            function __unset( $name ) { unset( $this->data[ $name ] ); $this->commit(); }

            function getIterator() { return new Itr_Simple( $this ); }

	    function select( $opts = array() ) { }
	    function delete() { }
    	    function lock() { }
    	    function unlock() { }

            function moveUp( $name ) { $top = array_shift( $this->data ); array_push( $this->data, $top ); }
            function moveDown( $name ) { $bottom = array_pop( $this->data ); array_unshift( $this->data, $bottom ); }
            function rename( $oldName, $newName ) {
		    if( ! isset( $this->data[ $oldName ] ) ) throw new EX_NOT_FOUND( __METHOD__ . ' ( ' . $oldName . ' ) ' );
		    $value = $this->data[ $oldName ];
		    unset( $this->data[ $oldName ] );
		    $this->data[ $newName ] = $value;
	    }

            function offsetGet( $name ) { return isset( $this->data[ $name ] ) ? $this->data[ $name ] : NULL; }
            function offsetSet( $name, $value ) { }
            function offsetUnset( $name ) { }
            function offsetExists( $name ) { }

	    function selectPage( $page = 1, $pageCount = NULL ) { }
	    function selectAll( ) { }
	    function search( $what ) { }

	    function onUpdate( $name ) { }
	    function onInsert( $name ) { }
	    function onDelete( $name ) { }
	    function count() { return count( $this->data ); }

	    function preload() { }
	    function preloadModeOn() { }
	    function preloadModeOff() { }
    }




