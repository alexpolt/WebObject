<?php
	/*
    		Alex Poltavsky, 2008
    		www.alexclub.org
	*/

	class USER {

	    const COOKIE_NAME = 'user';

	    static function getRemoteUser() {
        	    $cookie = HDRS::getCookie( self::COOKIE_NAME );

		    // cookie structure > serialize(  array(   sign, array( $login, serizlize( $data ) )   )  )
		    $data = is_null( $cookie ) ? NULL : unserialize( $cookie );		    
		    $sign = isset( $data[0] ) ? $data[0] : NULL;
		    $userdata = isset( $data[1] ) ? unserialize( $data[1] ) : NULL;

		    try {
			if( ! is_array( $data ) || empty( $data ) 
        		    || is_null( $sign ) || is_null( $userdata ) 
			    || ! is_array( $userdata ) || empty( $userdata ) ) {

        			if( MODE::$POST ) throw new EX_COOKIE();

        			if( ! is_null( $cookie ) )
                                    	    throw new EX_AUTH_WRONGCOOKIE();

				$user = new User_Anonymous();
				$user->login();

			return $user;
			}

			$login = $userdata[0];
			$user = SYS::$TREE->users->data->$user;

			if( is_null( $user ) || ! is_object( $user ) )
                                    	    throw new EX_AUTH_WRONGCOOKIE();

			AUTH::checkSign( $user->getAuthKey(), $data[1], $sign );

			$user->setCookieData( $userdata );

			return $user;		    
 
		    } catch ( EX $E ) {
				$user = new User_Anonymous();
				$user->login();
				throw $E;
		    }
	    		    
	    }



	}



