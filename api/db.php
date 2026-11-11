<?php
declare(strict_types=1);

$host = 'localhost';
$dbname = 'imovecheck';
$username = 'root';
$password = ''; // Padrão do XAMPP. Altere se seu MySQL tiver senha.

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Não foi possível conectar ao banco. Confira api/db.php e importe database.sql.']);
    exit;
}
