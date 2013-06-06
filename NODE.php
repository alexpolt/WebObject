<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    class FLAG {
	    const LINK = 1, ACTIVE = 2;
    }

    class NODE {

	    // runtime properties
	    public $runtime = array();

	    function __construct( $id = NULL, $table = NULL ) {
								    if( DEBUG::$CONSTRUCT ) DEBUG::log( $this->getType().'::__construct' );
		    $this->id = $id;
		    $this->table = $table;
	    } 

	    // Most basic props
	    private $id;
	    private $table;

            function getId() { return $this->id; }
            function setId( $id ) { return $this->id = $id; }
	    function getTable() { return is_null( $this->table ) ? $this->table = $this->getType() : $this->table; }
	    function setTable( $table ) { $this->table = $table; }

	    private $tableid;
            function getTableId() { return is_null( $this->tableid ) ? $this->tableid = SYS::getId( $this->getTable() ) : $this->tableid; }

	    private $type;
	    private $typeid;
            function getTypeId() { return is_null( $this->typeid ) ? $this->typeid = SYS::getId( $this->getType() ) : $this->typeid; }
            function getType() { return is_null( $this->type ) ? $this->type = get_class( $this ) : $this->type; }

	    private $path = array();
	    function getPath() { if( is_null( $this->path ) ) { $this->path = $parent->getPath(); array_push( $this->path, $this->getName() );} else return $this->path; }

	    // Traversing tree
	    function resolvePath( array $path ) {}

	    private $name;
	    function getName() { return $this->name; }
	    function setName( $name ) { $this->name = $name; }

	    private $parent;
	    function getParent() { return $this->parent; }

	    // Allows an object to be part of a tree.
	    function initNode( $parent, $name ) { 
			$this->parent = $parent; 
			$this->name = $name;
	    }


	    private $flags;
            function getFlags() { return $this->flags; }
            function setFlags( $flags ) { $this->flags = $flags; }
            function setFlag( $flag ) { $this->flags = $this->flags | $flag; }
            function unsetFlag( $flag ) { if( $this->flags & $flag ) $this->flags = $this->flags ^ $flag; }
            function checkFlag( $flag ) { return $this->flags & $flag; }

	    private $metadata = array();
            function getMetaData() { return $this->metadata; }
            function setMetaData( $data ) { $this->metadata = $data; }

	    function getNodeArray() {
			return array('type' => $this->getTypeId() ,
                                     'id'       => $this->getId(),
                                     'table'    => $this->getTableId(),
                                     'meta' => $this->getMetaData(),
                                     'flags' => $this->getFlags() );
	    }

    	    static function createFromNodeArray() {
		    $args = func_get_args();
		    $array = isset( $args[0] ) ? $args[0] : NULL;
            	    if( is_null( $array )
			    || ! isset( $array['type'] )
                    		|| ! isset( $array['table'] )
                        	    || ! isset( $array['id'] ) )
                                		throw new EX_FAILURE( __METHOD__.' ( ' . strtr( print_r( $array, true ), "\n", ' ' ) . ' ) ' );

            	    $id = $array[ 'id' ];
            	    $type = SYS::getName( $array[ 'type' ] );
            	    $table = SYS::getName( $array[ 'table' ] );
                                                                if( DEBUG::$NEW ) DEBUG::log( DEBUG::$STR['NEW'].__METHOD__.' ( type = '.$type.', id = '.$id.', table = '.$table.' )');

            	    if( class_exists( $type ) ) {
                    	    $object = new $type( $id, $table );
                    	    if( ! is_object( $object ) ) throw new EX_FAILURE( __METHOD__.' ( '. $type.', '.$id.', '.$table.' ) ' );
                    	    $object->setFlags( $array[ 'flags' ] );
                    	    $object->setMetaData( $array[ 'meta' ] );
            	    return $object;
            	    } else
    			    throw new EX_FAILURE( __METHOD__ . ' > class_exists ( ' . $type . ' ) ' );
    	    }

    }



