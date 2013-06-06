<?php

    ini_set("display_errors", 1);
    error_reporting( E_ALL | E_STRICT );

    require_once 'INIT.php';
    SYS::init();


    $a = new Web_Object();
    $a = new Web_Object(2);
    $c = new Web_Object(1);
    echo "\n>>>c->igor = ".$c->igor->getName();
    echo "\n>>>a->test = ".$a->test;
    echo "\n>>>a->meta->alex = ".$a->meta->alex = 'METATEST';
    echo "\n>>>a->getAccessKey = ".print_r($a->getAccessKey(),true);
    echo "\n>>>c->test = ".$c->test = 'YEEEES!';

    echo "\n>>>c->test = ".$c->test."\n";
    echo "\n>>>".print_r( $c->getMetadata() )."\n\n";
    //print_r( $a->getObjectArray() );
    //print_r( $c->getObjectArray() );
echo "\n";
exit();

    $a = new Web_Object(1);
    $a->objC = $c;
    $b = new Web_Object(2);
    $b->alex = 'test';
    $b->igor = array('arr_test');
    $a->obj = $b;
    //$a->igor->good->{'0'} = 'no';
    echo "\n\n".str_repeat('-', 50)."\n";
    echo 'Type '.$a->obj->getType()."\n";
    echo 'Name '.$a->obj->getName()."\n";
    echo 'Path '.implode('/', $a->obj->getPath())."\n";
    $a->obj->meta->name_ru = 'Привет мир!';
    echo $a->obj->meta->name_ru."\n\n";
    //$b = new Web_Object(2);
    //echo gettype($a->a)."\n\n";
    //echo $a->a."\n\n";

    echo "\n";


