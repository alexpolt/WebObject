<?php

	class DB_Table_Collection extends DB_Table {

		function getTableSql() {
			return '
CREATE TABLE ' . $this->table . '
(
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) unsigned NOT NULL collate cp1251_bin,
  `type` smallint(5) unsigned NOT NULL,
  `value` varchar(512) NOT NULL,
  `order` int(10) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`, `name`)
) CHARACTER SET cp1251;
';
		}

		function maintenance() {}

	}


