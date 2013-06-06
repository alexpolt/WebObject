<?php

    /**
     */

    
    class DB_Mysql {

	private $CFG;
	private $db;
	private $rs;
	private $sql;


	const E_EXISTS = 1050;
	const E_DUP = 1062;
	const E_COUNT = 1136;
	const E_SYNTAX = 1064;

	function __construct ( $cfg ) {
	    $this->CFG = $cfg;
	}

	private $cache = array();	
        static function getInstance( $cluster = 'master' ) {
                if( isset( self::$cache[ $cluster ] ) )
                                        return self::$cache[ $cluster ];
        return self::$cache[ $cluster ] = new DB_Mysql( DB::$CFG[ $cluster ] );
        }

	function connect() {
	    $cfg = $this->CFG;

	    $this->db = mysql_connect( $cfg['hostname'], $cfg['user'], $cfg['password'] );

	    if( ! $this->db ) 
			throw new EX_DB ( mysql_error() );

	    if( ! mysql_select_db( $cfg['db'] ) ) 
			throw new EX_DB ( mysql_error() );

	    if( function_exists( 'mysql_set_charset' ) )
			mysql_set_charset( $cfg['charset'], $this->db );
	    else
			$this->sql( 'SET NAMES ' . $cfg['charset'] );

	}
    
	function query( $sql ) { return $this->sql( $sql ); }

	function sql( $sql ) {
	    if( ! is_resource( $this->db ) ) $this->connect();
								    if( DEBUG::$SQL ) DEBUG::sql( $sql );
	    $this->sql = $sql;						
	    $this->rs = mysql_query( $sql, $this->db );
	    if( ! $this->rs ) {
		$errno = mysql_errno( $this->db );
		$errmsg = '';
		if( MODE::$ADMIN or MODE::$DEV ) $errmsg .= 'SQL ('.$sql.')' . "\n" . $errno . ': ' . mysql_error( $this->db );
		else {
		    if( $errno == self::E_EXISTS ) $errmsg .= 'Got error: already exists.';
		    else if( $errno == self::E_SYNTAX ) $errmsg .= 'Got error: syntax error.';
		    else if( $errno == self::E_DUP ) $errmsg .= 'Got error: duplicates.';
		    else if( $errno == self::E_COUNT ) $errmsg .= 'Got error: column count doesnt match.';
		    else $errmsg .= 'Unknown DB error.';
		}
	    throw new EX_DB( $errmsg );
	    }
	return true;
	}

	private $tables = array();	
	function tableExists( $tablename ) {
	    $tables = $this->getTables();
	    $result = in_array( $tablename, $tables );
	    if( $result)
		return true;
	return false;
	}
	function getTables() {
	    if( ! count( $this->tables ) ) {
		    $this->sql( 'SHOW TABLES' );
		    if( $this->rowsCount() ) {
			$rows = $this->fetchAll();
			foreach( $rows as $row ) $this->tables[] = $row[0];
		    }
	    }
	return $this->tables;
	}

	function insertId() { return mysql_insert_id(); }

	function fetchAssoc( $sql = '' ) { return $this->fetch( $sql, MYSQL_ASSOC ); }
	function fetch( $sql = '', $type = MYSQL_NUM ) {
	    if( $sql )
		$this->sql( $sql );

	    if( $this->rs )
		    return mysql_fetch_array( $this->rs, $type );
	return NULL;
	}

	function fetchAll( $sql = '' ) {
	    if( $sql ) $this->sql( $sql );
		if( $this->rowsCount() ) {
			$data = array();
			while( $row = $this->fetch() ) $data[] = $row;
		return $data;
		}
	return NULL;
	}
	function fetchAllAssoc( $sql = '' ) {
	    if( $sql ) $this->sql( $sql );
		if( $this->rowsCount() ) {
			$data = array();
			while( $row = $this->fetch( NULL, MYSQL_ASSOC ) ) $data[] = $row;
		return $data;
		}
	return NULL;
	}
	
	function rowsAffected() {
	    return $this->rs === true ? mysql_affected_rows() : 0;
	}
	
	function rowsCount() {
	    return is_resource( $this->rs ) ? mysql_num_rows( $this->rs ) : 0;
	}

	function quote( $str ) { return $this->escape( $str ); }
	function escape( $str ) { return mysql_escape_string( $str ); }
	

    }	
    




