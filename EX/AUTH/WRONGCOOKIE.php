<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_WRONGCOOKIE extends EX { 

            private $msg = array (
                    'ru' => 'Ошбика в cookies.',
                    'en' => 'Cookie error.',
            );

            function getDescription() {
                return $this->msg[ LANG::getLang() ];
            }

    }



