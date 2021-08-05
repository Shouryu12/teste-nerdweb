<?php
require_once __DIR__ . "/Nerdweb/Database.php";
$configDB = [
    "Host" => "",
    "Name" => "",
    "User" => "",
    "Pass" => ""
];
$database = new \Nerdweb\Database($configDB);
