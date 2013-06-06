<?php
    /*
	Alex Poltavsky, 2008
	www.alexclub.org
    */

    class EX_EXIT extends EX {

            function handle() { throw new EX_DUMMY( __EXIT__ ); }

    }


