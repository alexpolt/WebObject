<?php


	abstract class DB_Table {
		protected $order = '', $limit = '', $where = '';
		protected $table;
		private $db;

		function __construct( $table, $cluster = 'master' ) {
			parent::__construct( 0, $table );
			$class = DB::$CFG[ $cluster ][ 'class' ];
			$this->db = call_user_func( array( $class, 'getInstance' ), $cluster );
			$this->table = $table;
		}
		function getTableName() { return $this->table; }
		abstract function getTableSql();
		abstract function maintenance();

		function initialize( $opts = array() ) {
			$this->createTable();
		}
		function createTable() {
			if( $this->db->tableExists( $this->table ) ) return;
			$this->db->sql( $this->getTableSql() );
		}
		
		function count() { $res = $this->db->fetch( 'SELECT count(*) FROM ' . $this->table ); return $res[0]; }
		function truncate() { $this->db->sql( 'TRUNCATE ' . $this->table ); }
		function drop() { $this->db->sql( 'DROP TABLE ' . $this->table ); }

		function massEscape( & $value, $key ) { $value = '\'' . $this->db->escape( $value ) . '\''; }

		function insert( $data ) {
			$fields = $values = '';
                	$fields = implode( array_keys( $data ), ', ' );
                	array_walk( $data, array( $this, 'massEscape' ) );
                	$values = implode( array_values( $data ), ', ' );
			$sql = 'INSERT INTO ' . $this->table . ' ( ' . $fields . ' ) VALUES ( ' . $values . ' )';
		return $this->db->sql( $sql );
		}

		function update( $data ) {
			$fields = array();
			foreach( $data as $field => $value ) $fields[] = $field . ' = \'' . $this->db->escape( $value ) . '\'';
                	$setStr = implode( $fields, ', ' );
			$sql = 'UPDATE ' . $this->table . ' SET ' . $setStr . $this->where;
		return $this->db->sql( $sql );
		}

		function select( $what = '*' ) {
			if( ! is_string( $what ) )
                		$what = implode( $what, ', ' );
			$sql = 'SELECT ' . $what . ' FROM ' . $this->table . $this->where . $this->limit . $this->order;
		return $this->db->fetchAllAssoc( $sql );
		}

		function delete() {
			$sql = 'DELETE FROM ' . $this->table . $this->where;
		return $this->db->sql( $sql );
		}

		function prepare() {
			$this->order = $this->limit = $this->where = '';
		}
		function order( array $columns, $type = ' DESC' ) {
			if( is_string( $columns ) ) { $this->order = $columns; return; }
			$cols = implode( $columns, ', ' );
			$this->order = ' ORDER BY ' . $cols . $type . ' ';
		}
		function limit( $from, $count ) {
			$this->limit = ' LIMIT ' . $from . ', ' . $count;
		}
		function where( $where, $type = ' AND ') {
			if( is_string( $where ) ) { $this->where = $where; return; }
			$fields = array();
			foreach( $where as $field => $value ) $fields[] = $field . ' = \'' . $this->db->escape( $value ) . '\'';
                	$whereStr = ' WHERE ' . implode( $fields, $type ) . ' ';
			$this->where = $whereStr;			
		}

    		function insertId() { return $this->db->insertId(); }
    		function affectedRows() { return $this->db->affectedRows(); }
    		function selectedRows() { return $this->db->numRows(); }
    		function lock() { return $this->db->sql( 'LOCK TABLES ' . $this->table . ' WRITE'); }
    		function unlock() { return $this->db->sql( 'UNLOCK TABLES' ); }


	}






