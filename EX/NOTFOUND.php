<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_NOTFOUND extends EX {

            private $msg = array (
                    'ru' => 'Путь не найден.',
                    'en' => 'Wrong path.',
            );

            function getDescription() {
                return $this->msg[ LANG::getLang() ];
            }
    }



