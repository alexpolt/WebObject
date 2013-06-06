<?php

	/*
	    Alex Poltavsky, 2008
	    www.alexclub.ru
	*/

	class Itr_Page extends Itr_Simple {
		private $page;
		private $pageCount;

		function __construct( $object, $page = 1, $pageCount = NULL ) { 
			    parent::__construct( $object ); 
			    $this->page = $page;
			    $this->pageCount = $pageCount;
		}

                function valid() {
                        if( current( $this->data ) === false ) {
                                $this->page++;
                                $this->object->selectPage( $this->page );
                                $this->data = $this->object->getData();
                                if( empty( $this->data ) ) return false;
                                else reset ( $this->data );
                        }
                return true;
                }
		

        	function rewind() { 
			    if( ! $this->object->isSelected() ) 
					    $this->object->selectPage( $this->page, $this->pageCount );
			    $this->data = $this->object->getDataArray();
			    reset( $this->data ); 
		}




	}



