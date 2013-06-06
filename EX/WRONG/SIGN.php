<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_WRONG_SIGN extends EX { 

            private $msg = array (
                    'ru' => 'Ошибка в данных.',
                    'en' => 'Data error.',
            );

            function getDescription() {
                return $this->msg[ LANG::getLang() ];
            }

    }



