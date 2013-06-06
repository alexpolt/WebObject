<?php

	/*
	 * Alex Poltavsky, 2008
	 * www.alexclub.ru
	 */

	class Ext_Access extends EXT {

	    const PASSWORD = 'password'; // Get option name for password

	    private $parent;
		
	    function initNode( $parent, $name ) {
		    parent::initNode( $parent, $name );
		    $this->parent = $parent;
	    }

	    function initialize( $opts = array() ) {
        	    $parent = $this->parent;
                    $parent->data->{ ACCESS::OWNER } = is_null( $parent ) ? CFG::$ADMIN_IDS[ 0 ] : $parent->getOwner();
                    $parent->data->{ ACCESS::WORLD } = is_null( $parent ) ? $this->getAccessBitPosition( ACCESS::READ ) : $parent->getAccess( ACCESS::WORLD );
                    $parent->data->{ ACCESS::LOGGED } = is_null( $parent ) ? $this->getAccessBitPosition( ACCESS::READ )
                                                                                || $this->getAccessBitPosition( ACCESS::COMMENT )
                                                                                : $parent->getAccess( ACCESS::GROUPS );
	    }
		    
            // Access control
            function getAccessBitPosition ( $type ) {
                    static $list = NULL, $keys = NULL, $cache = array();
                    if( isset ( $cache[ $type ] ) ) return $cache[ $type ];
                    if( is_null( $list ) ) $list = $this->parent->getAccessList();

                    if( ! isset ( $list[ $type ] ) )
                                            throw new EX_FAILURE( __METHOD__ . '. There is no such access type: ' . $type );
                    if( is_null( $keys ) ) $keys = array_keys( $list );
                    $idx = array_search( $type, $keys );
            return $cache[ $type ] = 1 << $idx;
            }

            function isSpecial( $accessBits ) { 
		    $visible = $this->getAccessBitPosition( Web_Base::VISIBLE );
		    $read = $this->getAccessBitPosition( Web_Base::READ );
		    $write = $this->getAccessBitPosition( Web_Base::WRITE );
		    if( $accessBits & $visible || 
			    $accessBits & $visible ||
				$accessBits & $visible )
					    throw new EX_AUTH_SPECIAL();
	    }

            function checkAccess( $type ) { 
        	    if( MODE::$ADMIN or MODE::$DEV ) return true;
	    
        	    $userid = SYS::$USER->getId();
    		    $ownerid = $this->getOwner();
        	    if( $userid == $ownerid ) return true;

    		    $accessWorld = $this->getAccess( ACCESS::WORLD );
		    $this->isSpecial( $accessWorld );
        	    if( $accessWorld & $type ) return true;

		    if( SYS::$USER instanceof User_Anonymous )
    					    throw new EX_AUTH_DENIED( $type );

    		    $accessLogged = $this->getAccess( ACCESS::LOGGED );
		    $this->isSpecial( $accessLogged );
        	    if( $accessLogged & $type ) return true;


        	    if( SYS::$USER->inFriends( $ownerid ) ) {
        		    $accessFriends = $this->getAccess( ACCESS::FRIENDS );
			    $this->isSpecial( $accessFriends );
        		    if( $accessFriends & $type ) return true;

	        	    $groups = $this->getAccess( ACCESS::GROUPS );
			    if( ! is_null( $groups ) )
                	    foreach( $groups as $grid => $grpAccess ) {
				$this->isSpecial( $grpAccess );
                                if( SYS::$USER->inGroup( $grid )
                                                && $type & $grpAccess )
                                                            return true;
			    }
        	    }

	            $passwd = $this->getPassword();
		    if( ! is_null( $passwd ) ) {
			    $pass = isset( REQ::$OPTS[ self::PASSWORD ] ) 
					    && is_numeric( REQ::$OPTS[ self::PASSWORD ] ) ? 
								REQ::$OPTS[ self::PASSWORD ] : NULL;
			    if( ! is_null( $pass ) 
				    and $pass == $passwd ) {
						return true;
			    }
		    }

    	    throw new EX_AUTH_DENIED( $type );
	    }

            function getOwner() { return $this->parent->data->_owner; }
            function getOwnerObject() { return new User( $this->getOwner() ); }
            function setOwner( $id ) { if( is_object( $id ) ) $id = $id->getId(); $this->parent->data->_owner = $id; }

            function getAccess( $group ) { return $this->parent->data->$group; }
            function setAccess( $group, $access ) { $this->parent->data->$group = $access; }
            function checkAccessBit( $group, $type ) { $access = $this->getAccess( $group ); return $access & ( $this->getAccessBitPosition( $type ) ); }
            function setAccessBit( $group, $type ) { $access = $this->getAccess( $group ); $this->setAccess( $group, $access | ( $this->getAccessBitPosition( $type ) ) ); }
            function unsetAccessBit( $group, $type ) { $access = $this->getAccess( $group ); $this->setAccess( $group, $access ^ ( $this->getAccessBitPosition( $type ) ) ); }

            function unsetPassword() { if( isset( $this->parent->data->{ ACCESS::PASSWORD } ) ) unset( $this->parent->data->{ ACCESS::PASSWORD } ); }
            function setPassword( $password ) { $this->parent->data->{ ACCESS::PASSWORD } = $key; }
            function getPassword( ) { return isset( $this->parent->data->{ ACCESS::PASSWORD } ) ? $this->parent->data->{ ACCESS::PASSWORD } : NULL; }

            function setGroupAccess( $grpid, $access ) { $this->parent->data->{ ACCESS::GROUPS }->$grpid = $access; }
            function getGroupAccess( $grpid ) { return $this->parent->data->{ ACCESS::GROUPS }->$grpid; }
            function setGroupAccessBit( $grpid, $access, $type ) {
                                        $access = $this->getGroupAccess( $grpid );
                                        $this->setGroupAccess( $grpid, $access | ( $this->getAccessBitPosition( $type ) ) ); }
            function unsetGroupAccessBit( $grpid, $access, $type ) {
                                        $access = $this->getGroupAccess( $grpid );
                                        $this->setGroupAccess( $grpid, $access ^ ( $this->getAccessBitPosition( $type ) ) ); }
            function checktGroupAccessBit( $grpid, $access, $type ) {
                                        $access = $this->getGroupAccess( $grpid );
                                        return $access & ( $this->getAccessBitPosition( $type ) ); }
            function issetGroupAccess( $grpid ) { return isset( $this->parent->data->{ ACCESS::GROUPS }->$grpid ) ? true : false; }



	}





