<?php

    include_once "db__storage.php";
    
    class db__object extends db__storage {

    	    private static $table = array (
        	"id"  => array("type" => "int", "default" => 1, "not null" => 1, "unsigned" => 1),
        	"attrid" => array("type" => "int", "default" => 1, "not null" => 0, "unsigned" => 1),
        	"value"  => array("type" => "text", "not null" => 0),
        	"typeid" => array("type" => "smallint", "default" => 0, "not null" => 0, "unsigned" => 1),
        	"file" => array("type" => "varchar(255)", "default" => "\"\""),
        	"objid"  => array("type" => "int", "default" => 0, "not null" => 1, "unsigned" => 1),
        	"ownerid" => array("type" => "int", "default" => 0, "not null" => 1, "unsigned" => 1),
        	"access" => array("type" => "int", "default" => 0, "not null" => 1, "unsigned" => 1),
        	"flags" => array("type" => "smallint", "default" => 0, "not null" => 1, "unsigned" => 1),
        	"count" => array("type" => "int", "default" => 0, "not null" => 0, "unsigned" => 1),
        	"options"=> array("charset" => "UTF8", "_collate" => "utf8_general_ci",
                                    "unique"=> array('id' => 'id', 'attrid' => 'attrid') )
    	    );

	    public function __construct( $id, $tableName ) {
		    parent::__construct( $id, $tableName );
	    }
	    function getTable() { return self::$table; }
	    static function getInstance() {
            	    $args = func_get_args();
            	    $id = isset($args[0]) ? $args[0] : NULL;
            	    $tableName = isset($args[1]) ? $args[1] : __CLASS__;
            	    if(! $tableName or ! is_numeric($id))
                        	    throw new EX_CONSTRUCTOR( __METHOD__.' id = '.$id.', table = '.$tableName );
            	    return new self( $id, $tableName );
	    }
            static function createInstance() {
            	    $args = func_get_args();
            	    $tableName = isset($args[0]) ? $args[0] : __CLASS__;
            	    if(! $tableName or ! is_numeric($id))
                        	    throw new EX_CONSTRUCTOR( __METHOD__.' table = '.$tableName );
                    if(SYS::$ADMIN_MODE) self::installTable( $tableName );
                    $id = SYS::genNewId( __CLASS__ );
                    return call_user_func( array(__CLASS__,'getInstance'), $id, $tableName );
            }


            static function installTable($tableName, $table=NULL) {
                    $table = is_null($table) ? self::$table : $table;
                    if(SYS::$ADMIN_MODE)
                                db__storageMysql::installTable( $tableName, $table );
	    }


    }


?>