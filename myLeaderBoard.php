<?php

require_once("dbconf.php");
global $config

$pdo = new PDO($config['host'], $config['user'], $config['password']);
$board = $pdo->prepare("SELECT users, best_score
                        FROM users
                        ORDER BY best_score LIMIT 0,100");

$board->execute();

