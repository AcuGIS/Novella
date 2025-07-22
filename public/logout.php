<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Novella\Auth\Auth;
use Novella\Config\Database;

$db = Database::getInstance()->getConnection();
$auth = Auth::getInstance($db);

$auth->logout();
header('Location: /login.php');
exit; 