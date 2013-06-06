<?php
    /*
        Alex Poltavsky, 2008
        www.alexclub.org
    */


    class SYS {

        /* Shutdown handling */
        const EVENT_SHUTDOWN = 1;
        public static $SHUTDOWN_LIST = array();  // List of objects to destroy on exit
        public static $SHUTDOWN_LIST_REG = array();  // List of objects by their class names
	static $FINISHING = false;

	public static function INIT() {
								    if(SYS::$DEBUG) debug(__METHOD__.' STARTED'."\n\n");
            #register_shutdown_function( array('SYS','FINISH'), &SYS::$SHUTDOWN_LIST );
            self::startExecTime();

	    if( isset($GLOBALS['_GET']['ajax']) ) SYS::$AJAX = 1;
	    SYS::initialLanguage();
	
 	    
	    SYS::$USER = user_anonymous::getInstance();
	    SYS::$REQ = Request::getInstance();

	    if(! file_exists(MAIN::$WRITE_DIR.'/data/.installed')) {
		    SYS::$USER = user_start::getInstance();
		    SYS::$INSTALL_MODE = 1;
	    }

	    SYS::$TREE = Tree::getInstance();
	    SYS::$TREE->setName('tree');
	    SYS::$HDRS = Headers::getInstance();

	    if( SYS::$INSTALL_MODE ) {

		    install::installTree();
		    install::installObjects();
		    touch(MAIN::$WRITE_DIR.'/data/.installed');
		    throw new EX_INITIALIZED('Sytem initialized...');
	    }

	    ACCESS::init();

	    SYS::$USER = Security::getLogged();
	    SYS::$MAIL = SYS::$TREE->config->email;
	    SYS::$WORDS = SYS::$TREE->config->words;

	    setlocale(LC_CTYPE, SYS::$TREE->config->locale_ctype);
								    if(SYS::$DEBUG) debug(__METHOD__.' FINISHED'."\n\n");
	}

	static function initialLanguage() {
	    $lang = getRemoteLanguage();
	    SYS::$LANG = is_null($lang) ? SYS::$LANG : $lang;
	}

	public static function isValidIdentifier($name) {
		if( strlen($name) >= 1 
			and preg_match('/^[A-Za-z0-9_.-]+$/', $name) )
									return true;
	throw new EX_INVALID_NAME($name);
	}
	public static function isValidFilename($file) {
		if( strlen($file) >= 1 
		      and ! strstr($file, '..') 
		         and ! strstr($file, '//') 
			    and preg_match('/^[A-Za-z0-9_\/.-]+$/', $file) )
									return true;
	throw new EX_INVALID_NAME($file);
	}
	public static function isValidPassword($passwd) {
		if( strlen($passwd) > 1 
		      and substr_count($passwd, ' ') == 0
			    and preg_match('/^[A-Za-z0-9_=+*#!@;:&><)($?,.-]{3,25}$/', $passwd) )
									return true;
	throw new EX_INVALID_PASSWORD($passwd);
	}

	//public static function idToName($id) { return SYS::$MAP->idToName($id); }
	//public static function nameToId($name) { return SYS::$MAP->nameToId($name); }
	//public static function issetName($name) { return SYS::$MAP->issetName($name); }

	public static function propToId($name) { if(!SYS::$PROPSMAP) SYS::$PROPSMAP = db__mapProp::getInstance(); return SYS::$PROPSMAP->nameToId($name); }
	public static function idToProp($id) { if(!SYS::$PROPSMAP) SYS::$PROPSMAP = db__mapProp::getInstance(); return SYS::$PROPSMAP->idToName($id); }
	public static function nameToId($name) { if(!SYS::$NAMESMAP) SYS::$NAMESMAP = MapNames::getInstance(); return SYS::$NAMESMAP->nameToId($name); }
	public static function idToName($id) { if(!SYS::$NAMESMAP) SYS::$NAMESMAP = MapNames::getInstance(); return SYS::$NAMESMAP->idToName($id); }
	public static function getTypeId($type) { if(!SYS::$TYPEMAP) SYS::$TYPEMAP = MapType::getInstance(); return SYS::$TYPEMAP->nameToId($type); }
	public static function getTypeName($id) { if(!SYS::$TYPEMAP) SYS::$TYPEMAP = MapType::getInstance(); return SYS::$TYPEMAP->idToName($id); }
	public static function genNewId($name) { return 0; if(!SYS::$SEQ) SYS::$SEQ = SequenceFile::getInstance(); return SYS::$SEQ->genNewId($name); }
	public static function getId($name) { return 0; if(!SYS::$SEQ) SYS::$SEQ = SequenceFile::getInstance(); return SYS::$SEQ->genNewId($name); }
	public static function classNameToPath($name) { return dirname(str_replace('__','/',$name)); }
	public static function getTime() { return I::$TIME; }

	public static function getExecTime() { return microtime(true) - self::$STARTTIME; }
	public static function startExecTime() { SYS::$STARTTIME = microtime(true); }

/*
            public static function getTypeId( $type ) {
            }
            public static function getTypeName( $id ) {
            }
            public static function getId( $name ) {
            }
            public static function getName( $id ) {
            }
*/
	public static function getPath($path = NULL, $obj = NULL ) { 
		
		if(! is_null($path) 
			and empty($path) )
            			throw new EX__WRONG_PATH( 'URL: ' . SYS::$REQ->getURI() );

		if( ! $path ) $path = SYS::$REQ->getPath();
		if( ! $obj  ) $obj = SYS::$TREE;

		// if we got a path string
		if( ! is_array($path) )
			    $path = explode('/', $path);

		$item = NULL; $val = NULL; $method = NULL; $webobjectHack = false;
		$origPath = $path; //for debug

		if( isset($path[0]) 
			    and $path[0] === $obj->getName() ) {
				if( count($path) == 1 ) 
					return array ($obj, NULL, NULL);
				else 
					array_shift($path);
		}
		$newobj = $obj;

                while(1) {
		    if( count($path) == 0) break;

		    $name = trim( array_shift($path));

		    if( strlen($name) == 0 ) continue;
		    if( $obj instanceof web__object 
				    and ! isset( SYS::$WORDS->$name ) 
						    and $name != 'data' ) {

							    $webobjectHack = true;
                    	                    		    $obj = $obj->data;
		    }

                    if( isset( $obj->$name ) ) {
				    $webobjectHack = false;
                            	    if(! isset(SYS::$MAGIC_KEYS[$name])) 
						    $val = $obj->$name;
				    else	    $val = $obj[$name];
				    // if object then move deeper
                                    if( is_object($val)) { 
                                                    $obj = $val;
						    $item = NULL;
                                                    continue; }
			// we have found an item. break here.
			$item = $name;
			break;
                    } else {
			$method = $name; 
			if( $webobjectHack )
					$obj = $obj->getParent(); 
			break;
		    }
                } // while end

		if( count($path) > 0 
			and  is_null( $method ) ) $method = array_shift($path);

		// if we didnt found any object then raise
		//if( $newobj === $obj and count($path) ) 
            	//	throw new EX__WRONG_PATH( implode($path, ',') );

		if( $val === $obj ) $val = NULL;

		return array($obj, $item, $val, $method);
	}

        static function resurrectObject($name, $objArray, $parent) {
        $object = NULL;
		if($objArray['ownerid'] == SYS::$ANONYMOUS_ID ) {
//echo $parent->getName().", ";
//echo $parent->getOwner().", ";
//echo $parent->getParent()->getOwner();
		    $objArray['ownerid'] = $parent->getOwner();
		}
                ACCESS::hasAccess( $objArray['ownerid'], $objArray['access'] );
                $typename = SYS::getTypeName( $objArray['typeid'] );
						    if(SYS::$DEBUG_TRACE) debug($typename.','.print_r($objArray,true));
		$file =  isset( $objArray['file'] ) ?  $objArray['file'] : NULL;
                if( class_exists( $typename )
                                and is_callable( $typename, 'getInstance' ) ) {
                	try {
                        	$object = call_user_func( array($typename, 'getInstance'), $objArray['objid'], $file );
						    if(SYS::$DEBUG_TRACE) debug($typename.' => created '. $name);
                	} catch ( Exception $e ) { $e->handle(); }
                }

                if(! is_null( $object ) ) {
                    $object->wakeUp( $parent, $name );
                    $object->flags( $objArray['flags'] );
		    $object->setCredentials( $objArray['ownerid'], $objArray['access'] );
                }
        return $object;
        }

        public static function addEvent($obj, $method, $eventType) {
                $classname = get_class($obj);
                if( $eventType == SYS::EVENT_SHUTDOWN ) {
                        if(! isset(self::$SHUTDOWN_LIST_REG[$classname]) )
                                        array_unshift(self::$SHUTDOWN_LIST, $obj);
                return true;
                }
        return SYS::$EVENTS->addEventListener($obj,$method,$eventType);
        }

        public static function removeEvent($obj, $eventType) {
            return SYS::$EVENTS->removeEventListener($obj,$method,$eventType);
        }


        public static function FINISH() {
		self::$FINISHING = true;
		$errors = NULL;
                if(is_array(self::$SHUTDOWN_LIST) and count(self::$SHUTDOWN_LIST))
                    foreach(self::$SHUTDOWN_LIST as $obj) {
			    if(self::$DEBUG_DESTRUCT) debug( "DESTRUCT " . get_class($obj) );
			    try {
                        	if( method_exists($obj, 'destruct') )
                                                	$obj->destruct();  // our artificial destructor
			    } catch(Exception $e) {
				    $errors .= $e->getMessage()."\n<br/>";
				    if(SYS::$ADMIN_MODE) $errors .= $e->handle()."\n<br/>";
			    }
                    }
		if( $errors ) throw new EX_FINISH( $errors );
        }


    }

?>