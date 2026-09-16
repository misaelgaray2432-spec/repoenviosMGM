<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

function responder(bool $ok, array $datos = [], int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode(['ok' => $ok] + $datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function leerJson(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === '' || $raw === false) {
        return [];
    }

    $datos = json_decode($raw, true);
    if (!is_array($datos)) {
        throw new RuntimeException('Solicitud inválida.');
    }

    return $datos;
}

try {
    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($metodo === 'GET') {
        $envios = $pdo->query('SELECT * FROM envios ORDER BY id DESC')->fetchAll();
        responder(true, ['envios' => $envios]);
    }

    if ($metodo !== 'POST') {
        responder(false, ['error' => 'Método no permitido.'], 405);
    }

    $datos = leerJson();
    $accion = $datos['accion'] ?? '';

    if ($accion === 'crear') {
        $destinatario = trim((string)($datos['destinatario'] ?? ''));
        $direccion = trim((string)($datos['direccion'] ?? ''));
        $descripcion = trim((string)($datos['descripcion'] ?? ''));

        if ($destinatario === '' || $direccion === '' || $descripcion === '') {
            throw new RuntimeException('Todos los campos son obligatorios.');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO envios (destinatario, direccion, descripcion) VALUES (?, ?, ?)'
        );
        $stmt->execute([$destinatario, $direccion, $descripcion]);

        responder(true, ['mensaje' => 'Envío registrado correctamente.']);
    }

    if ($accion === 'editar') {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        $destinatario = trim((string)($datos['destinatario'] ?? ''));
        $direccion = trim((string)($datos['direccion'] ?? ''));
        $descripcion = trim((string)($datos['descripcion'] ?? ''));

        if (!$id || $destinatario === '' || $direccion === '' || $descripcion === '') {
            throw new RuntimeException('Datos inválidos o campos incompletos.');
        }

        $stmt = $pdo->prepare(
            'UPDATE envios SET destinatario = ?, direccion = ?, descripcion = ? WHERE id = ?'
        );
        $stmt->execute([$destinatario, $direccion, $descripcion, $id]);

        responder(true, ['mensaje' => 'Envío actualizado correctamente.']);
    }

    if ($accion === 'eliminar') {
        $id = filter_var($datos['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            throw new RuntimeException('Envío inválido.');
        }

        $stmt = $pdo->prepare('DELETE FROM envios WHERE id = ?');
        $stmt->execute([$id]);

        responder(true, ['mensaje' => 'Envío eliminado correctamente.']);
    }

    responder(false, ['error' => 'Acción no válida.'], 400);
} catch (Throwable $e) {
    responder(false, ['error' => $e->getMessage()], 400);
}
