<?php
    /* 
	Alex Poltavsky, 2008
	www.alexclub.org
	
	access => read | write | logged | owner | admin
    */


    class Web_Service_BaseObject {
	public static $service = array (

	    'views' => array (
		'addprop' => array ( 'access' => 'admin', 'params' => array ( ),
				     'info' => array ( 'en' => 'Add new property', 'ru' => 'Добавить новое свойство' ), )
		'delprop' => array ( ),
		'setprop' => array ( ),
		'renprop' => array ( ),
	    ),


	    'services' => array (
		'addprop' => array ( 'access' => 'admin',
				     'params' => array ( ),
				     'info' => array ( 'en' => 'Add new property', 'ru' => 'Добавить новое свойство' ), )
		'delprop' => array ( ),
		'setprop' => array ( ),
		'renprop' => array ( ),
	    ),

	);

    }

