<?php 
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function dnd($dump) {
    echo "<pre>";
    var_dump($dump);
    echo "<pre>";
}


 
require_once("DB.php");
$db = DB::get_instance();

// $fields = ['user_name' => 'hallo', 'email' => 'emailAdres@sssss'];
// $db->update('users', 12, $fields);
$db->delete("users", 12);
// $params = [
//     'conditions' => "user_name = ? AND email = ?",
//     'bind' => ['w', 'w'],
//     'order' => "user_name Desc",
//     'limit' => 5
// ];
$params = [
    'conditions' => ['user_name = ?', 'email = ?'],
    'bind' => ['w', 'w'],
    'order' => "user_name Desc",
    'limit' => 5
];

// $params = [
//     'conditions' => ['user_name', 'email'],
//     'bind' => ['w', 'w'],
//     'order' => "user_name Desc",
//     'limit' => 5
// ];

$contacts = $db->find('users', $params);
// $contacts = $db->findFirst('users');
// $contacts = $db->findFirst('users');
var_dump($contacts);
// $query = $db->query("SELECT * FROM users ORDER BY user_name");

// $contacts = $query->results();

// var_dump($contacts);

// echo "index";

// echo $db;