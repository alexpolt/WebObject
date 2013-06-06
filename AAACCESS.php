<?php


    class ACCESS {

            public $info = array(
                'label_ru' => 'Access объект',
                'label_en' => 'Access object',
                'title_ru' => 'Объект доступа - отвечает за авторизацию',
                'title_en' => 'Access object manages authorization',
            );
	    
	    const A_VISIBLE = 1;
	    const A_WRITE = 2;
	    const A_READ = 4;

    	    const A_WORLD = 1;
    	    const A_LOGGED = 3;
    	    const A_OWNER = 6;
    	    const A_GROUP = 9;
    	    const A_GROUPNUM = 12;

    	    const A_WORLD_READ = 4;
    	    const A_WORLD_WRITE = 2;
    	    const A_WORLD_VISIBLE = 1;
    	    const A_WORLD_RWV = 7;

            const A_LOGGED_READ = 32;
            const A_LOGGED_WRITE = 16;
            const A_LOGGED_VISIBLE = 8;
            const A_LOGGED_RWV = 56;

            const A_OWNER_READ = 256;
            const A_OWNER_WRITE = 128;
            const A_OWNER_VISIBLE = 64;
            const A_OWNER_RWV = 448;

            const A_GROUP_READ = 2048;
            const A_GROUP_WRITE = 1024;
            const A_GROUP_VISIBLE = 512;
            const A_GROUP_RWV = 3584;

	    public static $cfg;
	    
            private function __construct() {}

            static function init() {
                self::$cfg = FolderAccess::getInstance();
            }

            static function install() {
                SYS::$TREE->config->access = FolderAccess::getInstance();
                SYS::$TREE->config->access->use_www_auth = 0;
	    }

	    static function hasAccess($ownerid, $access) {

		    if( SYS::$ADMIN_MODE ) return self::A_OWNER;
		    $u = SYS::$USER;

		    //if($ownerid == SYS::$ANONYMOUS_ID) $ownerid = $u->getId(); //hack - owner is dynamically changed to logged user

		    if( ($access & self::A_WORLD_RWV) > 0 ) return self::A_WORLD;
		    if( ($access & self::A_OWNER_RWV) > 0 )
			if (! $u->isAnonymous() && $u->getId() == $ownerid ) return self::A_OWNER;
		    if( ($access & self::A_LOGGED_RWV) > 0 ) 
			if (! $u->isAnonymous() ) return self::A_LOGGED;
		    if( ($access & self::A_GROUP_RWV) > 0 )
			if (! $u->isAnonymous() && $u->isInGroup( $access >> self::A_GROUPNUM, $u->getName()  ) ) return self::A_GROUP;

//SYS::$ADMIN_MODE=1;
//throw new EX_FAIL( $ownerid." ".$access." ".$u->getName() );
	    throw new EX_ACCESS_DENIED( $u->getName() );
	    }
	    static function isWritable( $ownerid, $access, $userid=NULL) {
		    return self::checkAccess( self::A_WRITE, $ownerid, $access, $userid=NULL);
	    }
	    static function isReadable( $ownerid, $access, $userid=NULL) {
		    return self::checkAccess( self::A_READ, $ownerid, $access, $userid=NULL);
	    }
	    static function isVisible( $ownerid, $access, $userid=NULL) {
		    return self::checkAccess( self::A_VISIBLE, $ownerid, $access, $userid=NULL);
	    }

	    static function checkAccess($type, $ownerid, $access, $userid=NULL) {
		    $u = SYS::$USER;

		    $ac = self::hasAccess(); // does he hasAccess? yes....

		    if(! is_null($userid)) $u = User::getInstance( $userid );

		    if(($ac & self::A_WORLD) and  ($access & $type) ) return true;

		    if(($ac & self::A_OWNER) and  $u->getId() === $ownerid )
				    if( $access >> $ac & $type ) return true;
		    if(($ac & self::A_LOGGED) and ! $u->isAnonymous() ) 
				    if( $access >> $ac & $type ) return true;
		    if(($ac & self::A_GROUP) and  $u->isInGroup( $ac & self::A_GROUP ) )
				    if( $access >> $ac & $type ) return true;
	    }

    }




?>