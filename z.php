<?php

    class a {
	const test = "test\n";
    }
    class bc extends a {
    }

    echo bc::test . "\n";
    exit;

    class a {
	const test1 = 'yes!';
	static $test1 = 'no!';
	static function t() {}
    }
    class b extends a {
    }

    echo a::$test1 . "\n\n";
    
    $var1 = 'a::test1';
    $var2 = 'a::$test2';
    echo $$var1 . "\n\n";
    echo $$var2 . "\n\n";

    exit;

    if( method_exists('a','t')) echo 'YES!'."\n";
    if( method_exists('b','t')) echo 'YES!'."\n";
    print_r( get_class_methods('b') );
    echo "\n"; exit();


    function test() { return array("test"); }

    echo $$test[0];
    echo "\n";
    exit();

    $c = mysql_connect(NULL,'admin','qwer');
    mysql_select_db('a');
    $r = mysql_query('select * from t', $c);
    if( $r ) echo "YES!\n";
    print_r(mysql_fetch_row($r));
    echo "\n";