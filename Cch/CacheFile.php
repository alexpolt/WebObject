<?php


    class CacheFile {
	    
	private static $data = NULL;

	static function put($key, $data) {
		if(is_null(self::$data)) self::$data = Folder::getInstance( 0, 'data/CacheData' );
		if(!isset(self::$data->$key)) self::$data->$key = File::createInstance('cache/'.$key);
		self::$data->$key->file = $data;
	}
	static function get($key) {
		if(is_null(self::$data)) self::$data = Folder::getInstance( 0, 'data/CacheData' );
		if(!isset(self::$data->$key)) return NULL;
		try {
		    return self::$data->$key->file;
		} catch(EX $e) {}
		return NULL;
	}
	static function del($key) {
		if(is_null(self::$data)) self::$data = Folder::getInstance( 0, 'data/CacheData' );
		if(!isset(self::$data->$key)) return;
		self::$data->$key->erase();
		unset( self::$data->$key );
	}
	static function exists($key) {
		if(is_null(self::$data)) self::$data = Folder::getInstance( 0, 'data/CacheData' );
		return isset(self::$data->$key);
	}

    }


?>