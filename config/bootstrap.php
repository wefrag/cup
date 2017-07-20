<?php
session_start();
header("Content-Type: text/html; charset=UTF-8"); 

global $db;
global $site;

error_reporting(E_ALL);

//define('MODE', 'dev');
define('MODE', 'prod');

try
{
	global $db;
	global $site;
	require(__DIR__.'/config.php');
	$db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
	$site = $db->query('SELECT * FROM config')->fetch();
}
catch (Exception $e)
{
	echo $e->getMessage().'<br/>';
    die('Erreur DB');
}

require __DIR__.'/functions.php';

