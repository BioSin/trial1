<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 *
 * Usage: http://trial1.loc/?q=123test%20123,1,,+79831611234,111234567890
 */
require_once 'Phone.php';
require_once 'PhoneParser.php';

if (empty($_REQUEST['q']) || !strlen($_REQUEST['q'])) {
    throw new BadMethodCallException('Missing required parameter "q"');
}

$parser = new PhoneParser();
$query = $_REQUEST['q'];
$result = $parser->parse($query);

var_dump($result);
