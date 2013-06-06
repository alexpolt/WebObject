<?php

    include_once "db__collection.php";
    
    class db__map extends db__collection {
	private static $instance = array();

        private static $table = array (
            "id" => array("type" => "int", "not null" => 1, "unsigned" => 1),
            "name" => array("type" => "varchar(255)", "not null" => 1 ),
            "objid" => array("type" => "varchar(255)", "not null" => 1 ),
            "created" => array("type" => "int", "not null" => 1, "unsigned" => 1),
            "options" => array("charset" => "UTF8", "_collate" => "utf8_general_ci", 
                                "unique"=>array("id"=>"id", "name"=>'name(16)'),
				"index" =>array("index1" => array("created"=>"created"))
				)
        );

	    function __construct( $tableName ) {
		    parent::__construct( 0, $tableName );
	    }

            static function getInstance() {
                    $args = func_get_args();
                    $tableName = isset($args[0]) ? $args[0] : NULL;
		    if( ! $tableName)
                	    $tableName = isset($args[1]) ? $args[1] : NULL;
                    if(! $tableName )
                                    throw new EX_CONSTRUCTOR( __METHOD__.' Need table name: '.$tableName );
                    if( isset( self::$instance[$tableName] ) )
                                    return self::$instance[$tableName];
                    return self::$instance[$tableName] = new self( $tableName );
            }
            static function createInstance() {
                    $args = func_get_args();
                    $tablename = isset($args[0]) ? $args[0] : NULL;
                    if( empty($tablename)) throw new EX_CONSTRUCTOR('Need tablename');
                    if(SYS::$ADMIN_MODE)
                            	self::installTable($tablename);
                    return new self($tablename);
	    }
	    function getTable() { return self::$table; }
            static function installTable($tableName, $table=NULL) {
                    if(SYS::$ADMIN_MODE)
                                db__storageMysql::installTable( $tableName, self::$table );
            }

	    function nameToId($name, $create=false) {
		    $id = parent::nameToId($name);
		    if( is_null($id)
			    and $create ) {
				$newid = SYS::genNewId( $this->getFile() );
				return $this->$name = $newid;
		    }
	    return $id;
	    }
	    


    }


?>