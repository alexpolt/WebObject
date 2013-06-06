<?php
    /*
        Alex Poltavsky, 2008
        www.alexclub.org
    */

    class CFG {
	static function set( $name, $value ) { self::$name = $value; }

	// Error control
	static $DISPLAY_ERRORS = 1;
	
	// MOTO
	static $MOTO = 'Russia CMS. Alex Poltavsky, 2008.';

	// Administrators
	static $ADMIN_IDS = array( 1 );
	static $ANONYMOUS_ID = 0;

	// Language
	static $LANGS = array( 'ru', 'en' );
	static $LANG = 'ru';	// default language


	// URL environment modifiers
	static $URL_HOOKS = array( 'ru' => 'LANG', 'en' => 'LANG', 'pda' => 'PDA', 'ajax' => 'AJAX' );

	// Allowed GET parameters. Otherwise throw exception.
	static $ALLOWED_GET = array( 'page' );

	// URL redirect hooks url => url to redir
	static $URL_REDIR = array( '' );

	// Default permission for new files
	static $FILEPERM = 0666; // rw-rw-rw-
	static $DIRPERM = 0777; // rwxrwxrwx

	// Some dirs
	static $LOCK_DIR = 'locks';

	// Properties caching
	static $PROP_CACHING = 1;

	// Caching
	static $CACHING = 0;
	static $LATE_REFRESH = 0;

	// Some constants
	static $MAX_VALUE_SIZE = 64000;		// maximum bytes for a post value
	static $FORM_VALID_TIME = 18000;	// time of form beeing valid. Default 30 min.
	static $MIN_POST_INTERVAL = 3;		// maximum allowed rate of post request per min.

	// Plain error out
	static $EX_TEXT_OUTPUT = 1;

	// Redirects. Name => Url
	static $REDIRECTS = array();

	// Informing admin about events. Level from 1 to 3.
	static $ADMIN_INFORM = 3;
	
	// Track iime of page generation
	static $EXEC_TIME = false;

	// Timezone
	static $TZ = 'Europe/Moscow';

	// Cookies
	static $COOKIE_PATH = '/';
	static $COOKIE_DOMAIN;

	// IPs of reverse proxies
	static $REVERSE_PROXY_IPS = array();
    }




