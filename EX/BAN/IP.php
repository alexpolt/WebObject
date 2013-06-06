<?php

    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_BAN_IP extends EX { 
            private $msg = array (
                    'ru' => 'Ваш IP адрес заблокирован.',
                    'en' => 'Your IP address is banned.',
            );
            function getDescription() {
                return $this->msg[ LANG::getLang() ];
            }

    }



