<?php

	class DB_Table_Text extends DB_Table {

		function getTableSql() {
			return '
CREATE TABLE ' . $this->table . '
(
  `id` int(10) unsigned NOT NULL,
  `attrid` int(10) unsigned NOT NULL,
  `type` smallint(5) unsigned NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `id` (`id`, `attrid`)
) CHARACTER SET cp1251;
';
		}

		function maintenance() {}
		

		function search( $query ) {
			$sql = 'SELECT id FROM ' . $this->table;
			$sql .= ' MATCH ( value ) AGAINST ( ' . DB::escape( $query )  . ' IN NATURAL LANGUAGE MODE )';
			$sql .= ' GROUP BY 1';
			return $this->db->fetchAllAsoc( $sql );
		}

	}


