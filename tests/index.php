<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once '../vendor/autoload.php';

use MASNathan\Object;

$x = [
	'name' => 'test',
	'test' => [
		'uno_test' => 1,
		'test_2' => 2
	]
];

$x = new stdClass();
$x->name = 'Test';
$x->last_name = 'Dummy';
$x->tests = new stdClass();
$x->tests->test_p1 = 1;
$x->tests->test_p2 = 2;
$x->tests->test_p3 = 3;
$x->tests->test_p4 = 4;

$obj = new Object($x);

echo '<pre>';
$c = 1;
foreach ($obj->getTests() as $key => $value) {
	var_dump($key, $value, $c++);
}

//var_dump($obj);
