<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */
    
    class Web_Base extends NODE implements IteratorAggregate {

	    // Caching of once accessed names
	    protected $cache = array();

	    // Extensions... Could be name => array( 'Class', array( arg1,arg2 ) )
	    private $ext = array( 'view' => 'Ext_View', 'service' => 'Ext_Service', 'css' => 'Ext_Css', 'js' => 'Ext_Js', 
				    'info' => 'Ext_Info', 'config' => 'Ext_Config', 'data' => 'Str_File', 'meta' => 'Str_Meta',
				    'cache' => 'Cch_File', 'access' => 'Ext_Access', 'sequence' => 'Ext_Sequence',
				    'ext' => 'Ext_Extensions' );
	    private $extCache;
	    private $extObjCache = array();

	    private $props = array( 'ext', 'config', 'data' );

	    const VISIBLE = 0, READ = 1, WRITE = 2, COMMENT = 3, PASSWORD = 4, PERMISSION = 5;
            private $access = array( self::VISIBLE, self::READ, self::WRITE, self::COMMENT, 
					self::PASSWORD, self::PERMISSION );

	    function getId() {
		    $id = parent::getId();
		    if( is_null( $id ) ) {
			$id = $this->sequence->genNewId();
			$this->setId( $id );
		    }
	    return $id;
	    }
	    function getSitePath() { $path = parent::getPath(); array_shift( $path ); return $path; }
	    function getLink() { return $this->data->link; }
	    function setLink( $link ) { $this->data->link = $link; }

	    // When new object is created 
	    function initialize( $opts = array() ) {
		    $path = $this->getSitePath();
		    $this->data->link = implode( $path, '/' );
		    $exts = $this->getExtensions();
		    //foreach( $exts as $ext => $class ) 
		    //		    $extObject = $this->$ext->initialize();
	    }

	    // Magic __get.
	    function __get( $name ) { 
		    // * look up an extension
		    return $this->getExtension( $name );
	    }

	    // Deleting objects
	    function delete() { 
							    if( DEBUG::$UNSET ) DEBUG::log( DEBUG::$STR['DEL'] . __METHOD__ );
		    $OBJ = $this->data->$name;

		    $exts = $this->getExtensions();
		    foreach( $exts as $ext => $class ) $this->$ext->delete();

		    $pageIt = $this->getIteratorAll();
		    foreach( $pageIt as $name => $VAL ) {
			    if( CFG::$PROP_CACHING 
					&& isset( $this->cache[ $name ] ) ) unset( $this->cache[ $name ] );
			    unset( $this->$name );
		    }
	    }

	    // Extensions. To override.
	    function getExtensions() { return $this->ext; }
	    function getExtension( $name ) {
							    if( DEBUG::$EXTENSION ) DEBUG::log( DEBUG::$STR['EXT'] . __METHOD__.' ( '.$name.' ) ' );
		    if( isset( $this->extObjCache[ $name ] ) ) return $this->extObjCache[ $name ];

		    if( is_null( $this->extCache ) ) 
				    $this->extCache = $this->getExtensions();

		    $ext = isset( $this->extCache[ $name ] ) ? $this->extCache[ $name ] : false;
		    if( $ext ) {
			    $class = $table = NULL;
			    if( is_array( $ext ) ) { $class = $ext[0]; $table = $ext[1]; }
			    else $class = $ext;
							    if( DEBUG::$EXTENSION ) DEBUG::log( DEBUG::$STR['EXT'] . __METHOD__.' ('.$name.' => '.$class.')' );
			    $extObject = new $class( $this->getId(), $table );
			    $extObject->initNode( $this, $name );
			    return $this->extObjCache[ $name ] = $extObject;
		    }
	    return NULL;
	    }
	    
	    // Get class hierarchy
	    function getClass() { return array( __CLASS__ ); }
	    

	    // includes a file at objects context
	    function includ( $file ) { ob_start(); include $file; return ob_get_clean(); }

	    // default action of object
	    function execute( $opts = array() ) {}
	    
            function getAccessList() { return $this->access; }
	    function getDataArray() { return $this->props; }
	    function getIterator() { return Itr_Simple( $this ); }


    }



