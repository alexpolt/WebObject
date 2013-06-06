<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_REQRATE extends EX {

	    private $msg = array (
		    'ru' => 'Превышено количество возможных запросов.',
		    'en' => 'Request rate is exceeded.',
	    );

            function getDescription() {
                return $this->msg[ LANG::getLang() ];
            }

    }



