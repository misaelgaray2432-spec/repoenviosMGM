<?php
declare(strict_types=1);

$host = mysql-misaelgaray.alwaysdata.net'; // Reemplaza si tu proveedor te dio el host completo.
$db   = 'misaelgaray_repoenvios';
$user = 'misaelgaray';
$pass = 'Mgm1927.';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
}

// Crea la tabla automáticamente si no existe.
$pdo->exec("
    CREATE TABLE IF NOT EXISTS envios (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        destinatario VARCHAR(150) NOT NULL,
        direccion VARCHAR(255) NOT NULL,
        descripcion TEXT NOT NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
