<?php

$host     = '127.0.0.1'; // ou localhost
$port     = '3306';
$dbname   = 'meu_banco';
$user     = 'meu_usuario';
$password = 'minha_senha';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conexão com MySQL via Docker realizada com sucesso!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}