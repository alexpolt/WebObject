<?php

        ini_set( "display_errors", 1 );
        error_reporting( E_ALL | E_STRICT );

        require_once 'INIT.php';
        require_once 'AUTH.php';
        require_once 'CFG.php';
        require_once 'NODE.php';
        require_once 'EXT.php';
        require_once 'Web/Base.php';
        require_once 'Web/Single.php';
        require_once 'Web/Object.php';
        require_once 'Str/Base.php';
        require_once 'Str/File.php';
        require_once 'Seq/File.php';
        require_once 'Map/Object.php';
        require_once 'Map/System.php';
        require_once 'DB/Mysql.php';
        require_once 'DB/Table.php';
        require_once 'USER.php';
        require_once 'SYS.php';
        require_once 'REQ.php';
        require_once 'MODE.php';
        require_once 'HDRS.php';
        require_once 'HEAD.php';
        require_once 'LANG.php';
        require_once 'TYPE.php';
        require_once 'EX.php';
        require_once 'DB.php';

	$html = '';

	try {

    	    try {

    		SYS::init();
        	REQ::init();
    		MODE::init();
		LANG::init();
    		AUTH::init();
		HDRS::init();
		HEAD::init();

		echo "\n\n!------------\n";

		//$a = new Web_Object(1);
		//$a->initialize();

		//echo "\n\n!------------\n";
		//$a->data->alex = 'Hello world!';
		//$a->data->arr = array('test'=>'Hello world!');
		$a = new Web_Base(1);
		$b = new Web_Base(2);
		$b->data->test1 = 'test!';
		$b->data->test2 = array(1,2,3,4);
		$a->data->obj = $b;
		$b->meta->aga = array('alex'=>'yes');
		echo $a->data->obj->getName()."\n\n";
		//print_r( $a->data->obj->meta->getDataArray() )."\n\n";
		//print_r( $a->data->obj->meta->aga->data->getDataArray() )."\n\n";
		//echo $a->data->obj->meta->aga->getName()."\n\n";
		//print_r( $a->data->obj->meta->aga->data->getDataArray() )."\n\n";
		//$a->data->obj->meta->aga->data->moscow = 2008;
		//$a->data->a=1;
		//echo $a->data->alex . "!!!!\n\n";
		//$a->data->arr->data->moscow = 'Yes!';
		//print_r( $a->data->arr->data->getArray() ) . "\n\n";
		//echo $a->data->obj->getType() . "\n\n";
		//$a->data->obj->data->select();
		//print_r( $a->data->obj->data->getDataArray() ) . "\n\n";

		echo "!------------\n";

		exit();


		$obj = SYS::$SITE->resolvePath( REQ::$PATH );

		if( $obj instanceof Web_Object )
			    $html = $obj->exec( REQ::$OPTS );
	    
	    } catch ( EX $E ) { 
		$html = $E->handle();
		if( $E->directOutput() ) { 
			    HDRS::send();    
			    echo $html;
		return;
		}
	    }

	} catch ( EX $E ) { return; }

	
	HDRS::send();    
	HEAD::processHtml( $html );

	if( MODE::$AJAX )
		echo AJAX::wrap( $html );		
	else {
    		echo HEAD::htmlStart();
		if( strlen( $html ) > 0 ) {
			    SYS::$SITE->runtime['content'] = $html;
			    echo SYS::$SITE->views->content->exec();
		} else
			    echo SYS::$SITE->views->front->exec();
		echo HEAD::htmlEnd();
	}




