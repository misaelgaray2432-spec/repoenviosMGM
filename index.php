<?php
require __DIR__ . '/config.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'crear') {
            $destinatario = trim($_POST['destinatario'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            if ($destinatario === '' || $direccion === '' || $descripcion === '') {
                throw new RuntimeException('Todos los campos son obligatorios.');
            }

            $stmt = $pdo->prepare(
                'INSERT INTO envios (destinatario, direccion, descripcion) VALUES (?, ?, ?)'
            );
            $stmt->execute([$destinatario, $direccion, $descripcion]);
            header('Location: index.php?ok=creado');
            exit;
        }

        if ($accion === 'editar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $destinatario = trim($_POST['destinatario'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            if (!$id || $destinatario === '' || $direccion === '' || $descripcion === '') {
                throw new RuntimeException('Datos inválidos o campos incompletos.');
            }

            $stmt = $pdo->prepare(
                'UPDATE envios SET destinatario = ?, direccion = ?, descripcion = ? WHERE id = ?'
            );
            $stmt->execute([$destinatario, $direccion, $descripcion, $id]);
            header('Location: index.php?ok=actualizado');
            exit;
        }

        if ($accion === 'eliminar') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new RuntimeException('Envío inválido.');
            }

            $stmt = $pdo->prepare('DELETE FROM envios WHERE id = ?');
            $stmt->execute([$id]);
            header('Location: index.php?ok=eliminado');
            exit;
        }
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

$envioEditar = null;
if (isset($_GET['editar'])) {
    $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM envios WHERE id = ?');
        $stmt->execute([$id]);
        $envioEditar = $stmt->fetch();
    }
}

$envios = $pdo->query('SELECT * FROM envios ORDER BY id DESC')->fetchAll();

$mensajes = [
    'creado' => 'Envío registrado correctamente.',
    'actualizado' => 'Envío actualizado correctamente.',
    'eliminado' => 'Envío eliminado correctamente.'
];
$ok = $_GET['ok'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Envíos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
    <div>
        <h1>Gestión de Envíos</h1>
        <p>Administra destinatarios, direcciones y descripciones.</p>
    </div>
</header>

<main class="container">
    <?php if ($ok && isset($mensajes[$ok])): ?>
        <div class="alert success"><?= htmlspecialchars($mensajes[$ok]) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <section class="card">
        <div class="card-title">
            <h2><?= $envioEditar ? 'Editar envío' : 'Nuevo envío' ?></h2>
            <?php if ($envioEditar): ?>
                <a class="btn secondary" href="index.php">Cancelar</a>
            <?php endif; ?>
        </div>

        <form method="post" class="form-grid">
            <input type="hidden" name="accion" value="<?= $envioEditar ? 'editar' : 'crear' ?>">
            <?php if ($envioEditar): ?>
                <input type="hidden" name="id" value="<?= (int)$envioEditar['id'] ?>">
            <?php endif; ?>

            <label>
                Destinatario
                <input type="text" name="destinatario" maxlength="150" required
                       value="<?= htmlspecialchars($envioEditar['destinatario'] ?? '') ?>"
                       placeholder="Nombre del destinatario">
            </label>

            <label>
                Dirección
                <input type="text" name="direccion" maxlength="255" required
                       value="<?= htmlspecialchars($envioEditar['direccion'] ?? '') ?>"
                       placeholder="Dirección de entrega">
            </label>

            <label class="full">
                Descripción
                <textarea name="descripcion" rows="4" required
                          placeholder="Describe el contenido o las instrucciones del envío"><?= htmlspecialchars($envioEditar['descripcion'] ?? '') ?></textarea>
            </label>

            <div class="full actions">
                <button class="btn primary" type="submit">
                    <?= $envioEditar ? 'Guardar cambios' : 'Registrar envío' ?>
                </button>
            </div>
        </form>
    </section>

    <section class="card">
        <div class="card-title">
            <h2>Envíos registrados</h2>
            <span class="count"><?= count($envios) ?> registro(s)</span>
        </div>

        <?php if (!$envios): ?>
            <div class="empty">No hay envíos registrados todavía.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Destinatario</th>
                        <th>Dirección</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($envios as $envio): ?>
                        <tr>
                            <td><?= (int)$envio['id'] ?></td>
                            <td><strong><?= htmlspecialchars($envio['destinatario']) ?></strong></td>
                            <td><?= htmlspecialchars($envio['direccion']) ?></td>
                            <td><?= nl2br(htmlspecialchars($envio['descripcion'])) ?></td>
                            <td><?= htmlspecialchars($envio['creado_en']) ?></td>
                            <td class="actions-cell">
                                <a class="btn small secondary" href="?editar=<?= (int)$envio['id'] ?>">Editar</a>
                                <form method="post" onsubmit="return confirm('¿Eliminar este envío?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id" value="<?= (int)$envio['id'] ?>">
                                    <button class="btn small danger" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
