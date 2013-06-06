<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_COOKIE extends EX {
	    private $msg = array (
		    'ru' => 'Ошибка проверки cookie. Проверьте, поддерживает ли Ваш браузер cookie.',
		    'en' => 'Cookie check failed. Check if your browser supports cookies.',
	    );

	    function getDescription() {
		return $this->msg[ LANG::getLang() ];
	    }

    }



