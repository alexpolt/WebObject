<?php

	class DB_Table_Object extends DB_Table {

		function getTableSql() {
			return '
CREATE TABLE ' . $this->table . '
(
  id int(10) unsigned NOT NULL,
  propid int(10) unsigned NOT NULL,
  type smallint(5) unsigned NOT NULL,
  value varchar(512) NOT NULL,
  UNIQUE KEY (id, propid)
) CHARACTER SET cp1251;
';
		}

		function maintenance() {}

	}


