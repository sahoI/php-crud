<?php

require "task.php";
$db = new PDO('mysql:host=db;dbname=task;charset=utf8mb4', 'root', 'root');
$httpdmethod = $_SERVER['REQUEST_METHOD'];

switch ($httpdmethod) {
    case 'GET':
        if (preg_match('/[api]\/[0-9]/', $_SERVER["REQUEST_URI"])) {
            $id = preg_replace('/[^0-9]/', '', $_SERVER["REQUEST_URI"]);
            getTask($db, $id);
        } elseif ($_SERVER["REQUEST_URI"] == '/api') {
            getTasks($db);
        }
        break;
    case 'POST':
        createTask($db);
        break;
    case 'PUT':
        $id = preg_replace('/[^0-9]/', '', $_SERVER["REQUEST_URI"]);
        updateTask($db, $id);
        break;
    case 'DELETE':
        $id = preg_replace('/[^0-9]/', '', $_SERVER["REQUEST_URI"]);
        deleteTask($db, $id);
        break;
}
?>