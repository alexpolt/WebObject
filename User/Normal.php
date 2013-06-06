<?php
	/*
    		Alex Poltavsky, 2008
    		www.alexclub.org
	*/

	class User_Normal extends Web_Object {
            private $ext = array( 'message' => 'User_Message' );

            function getExtensions() { return array_merge( parent::getExtensions(), $this->ext ); }

	    function onRequest() {
		    $lastReqTime = $U->cookies->last_req_time;
        	    if( ! is_null( $lastReqTime )
                    	    && $lastReqTime < CFG::$REQUEST_INTERVAL )
                                        	    throw new EX_REQUESTRATE();
		    $U->cookies->last_req_time = SYS::getTime();

		    $last_ip = $U->data->last_ip;
		    $curr_ip = SYS::getRemoteIp();
		    if( strcmp( $last_ip, $curr_ip ) ) {
		    		$msg = str_replace( array( '{VAR1}','{VAR2}'), array( $last_ip, $currip ), $this->message->ipchanged );
				$this->journal->addEvent( $msg, Web_Journal::WARNING );
				$this->data->last_login_ip = $curr_ip;
		    }
		    
	    }

	    function login() {
		    $this->cookies->lang = $this->getLang();
		    $curr_ip = SYS::getRemoteIp();
		    $this->data->last_login_ip = $curr_ip;
		    $this->index->last_login_time = SYS::getTime();
	    }

	    function initialize( $opts = array() ) {
		    LANG::setLang( $this->getLang() );
		    $this->updateAuthKey();
	    }
	
	    function getSessionTime() { return $this->data->session_time; }
	    function getLogin() { return $this->data->login; }
	    function getPassword() { return $this->data->password; }
	    function setPassword( $passwd ) { 
		    return $this->data->password = $passwd; 
		    $this->updateAuthKey();	    
	    }

	    function getLang() { $lang = $this->data->lang; return is_null( $lang ) ? LANG::getLang() : $lang; }
	    function setLang( $code ) { $this->data->lang = $code; $this->cookies->lang = $code; }

	    function getAuthKey() { return $this->data->authkey; }
	    function updateAuthKey() { 
		    $passwd = $this->getPassword();
		    $this->data->authkey = Auth::sign( KEY::$SERVER_KEY, $passwd ); 
	    }

	    function getCookieData() { return $this->cookies; }
	    function setCookieData( $data ) { $this->cookies = $data; }
	    function setCookieHeaders( $data ) {
		    $toSign = serialize( array( $this->getLogin(), $data ) );
		    $sign = AUTH::sign( $this->getAuthKey(), $toSign );
		    $cookie = serialize( array( $sign, $toSign ) );
		    HDRS::setCookie( USER::COOKIE_NAME, $cookie, $this->getSessionTime() );
	    }


	}



