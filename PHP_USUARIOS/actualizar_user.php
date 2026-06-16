<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}

require_once "../CONFIG_DATABASE/config.php";

$usuario_id = $_SESSION['usuario_id'];

// 🔍 Obtener datos actuales
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// 📝 PROCESAR FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $edad     = $_POST['edad'];
    $tipo_doc = $_POST['tipo_doc'];
    $num_doc  = $_POST['num_doc'];
    $celular  = $_POST['celular'];
    $correo   = $_POST['correo'];
    $password = $_POST['password'];

    // 🔒 SI NO INGRESA CONTRASEÑA → NO SE ACTUALIZA
    if (!empty($password)) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios 
                SET nombre=?, apellido=?, edad=?, tipo_doc=?, num_doc=?, celular=?, correo=?, password=? 
                WHERE id=?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssisssssi", 
            $nombre, $apellido, $edad, $tipo_doc, $num_doc, $celular, $correo, $passwordHash, $usuario_id
        );

    } else {

        $sql = "UPDATE usuarios 
                SET nombre=?, apellido=?, edad=?, tipo_doc=?, num_doc=?, celular=?, correo=? 
                WHERE id=?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssissssi", 
            $nombre, $apellido, $edad, $tipo_doc, $num_doc, $celular, $correo, $usuario_id
        );
    }

    if ($stmt->execute()) {

        // 🔄 Actualizar sesión
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_apellido'] = $apellido;

        header("Location: dashboard.php?ok=actualizado");
        exit;

    } else {
        $error = "Error al actualizar los datos. Intenta nuevamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar datos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f1f3f7;
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2.5rem 1rem;
        }

        .form-wrapper {
            width: 100%;
            max-width: 560px;
        }

        /* Header del form */
        .form-header {
            background: #1a1a2e;
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem 1.75rem;
        }

        .form-header h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        .form-header p {
            font-size: .82rem;
            opacity: .55;
            margin: .2rem 0 0;
        }

        /* Cuerpo */
        .form-body {
            background: #fff;
            border: 1px solid #e2e6ed;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 1.75rem;
        }

        /* Sección separadora */
        .form-section-label {
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #8e96a8;
            margin: 1.25rem 0 .75rem;
            padding-bottom: .4rem;
            border-bottom: 1px solid #edf0f5;
        }

        .form-section-label:first-child { margin-top: 0; }

        /* Labels */
        .form-label {
            font-size: .78rem;
            font-weight: 600;
            color: #3d4559;
            margin-bottom: .3rem;
        }

        /* Inputs */
        .form-control, .form-select {
            border: 1.5px solid #dde1ea;
            border-radius: 8px;
            font-size: .88rem;
            padding: .55rem .85rem;
            color: #1a1a2e;
            transition: border-color .18s, box-shadow .18s;
            background: #fafbfc;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1a1a2e;
            box-shadow: 0 0 0 3px rgba(26,26,46,.07);
            background: #fff;
            outline: none;
        }

        .form-control::placeholder { color: #b0b8c8; }

        /* Password hint */
        .password-hint {
            font-size: .75rem;
            color: #8e96a8;
            margin-top: .3rem;
        }

        /* Botones */
        .btn-guardar {
            background: #1a1a2e;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: .85rem;
            font-weight: 600;
            padding: .65rem 1rem;
            width: 100%;
            transition: background .18s, transform .15s;
        }

        .btn-guardar:hover {
            background: #c0392b;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-volver {
            display: block;
            text-align: center;
            margin-top: .65rem;
            font-size: .82rem;
            color: #8e96a8;
            text-decoration: none;
            transition: color .18s;
        }

        .btn-volver:hover { color: #1a1a2e; }

        /* Alert */
        .alert-danger {
            border: none;
            background: #fef2f2;
            color: #991b1b;
            border-left: 3px solid #ef4444;
            border-radius: 8px;
            font-size: .85rem;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>

<body>

<div class="form-wrapper">

    <!-- Encabezado -->
    <div class="form-header">
        <h4>✏️ Actualizar datos</h4>
        <p>Modifica tu información personal</p>
    </div>

    <!-- Cuerpo del formulario -->
    <div class="form-body">

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <!-- Datos personales -->
            <p class="form-section-label">Datos personales</p>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                        value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control"
                        value="<?= htmlspecialchars($usuario['apellido']) ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Edad</label>
                <input type="number" name="edad" class="form-control"
                    value="<?= $usuario['edad'] ?>" min="1" max="120" required>
            </div>

            <!-- Documento -->
            <p class="form-section-label">Documento</p>

            <div class="row g-3 mb-3">
                <div class="col-5">
                    <label class="form-label">Tipo</label>
                    <input type="text" name="tipo_doc" class="form-control"
                        value="<?= htmlspecialchars($usuario['tipo_doc']) ?>" required>
                </div>
                <div class="col-7">
                    <label class="form-label">Número</label>
                    <input type="text" name="num_doc" class="form-control"
                        value="<?= htmlspecialchars($usuario['num_doc']) ?>" required>
                </div>
            </div>

            <!-- Contacto -->
            <p class="form-section-label">Contacto</p>

            <div class="mb-3">
                <label class="form-label">Celular</label>
                <input type="text" name="celular" class="form-control"
                    value="<?= htmlspecialchars($usuario['celular']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo" class="form-control"
                    value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            </div>

            <!-- Seguridad -->
            <p class="form-section-label">Seguridad</p>

            <div class="mb-4">
                <label class="form-label">Nueva contraseña</label>
                <input type="password" name="password" class="form-control"
                    placeholder="Dejar en blanco para no cambiar">
                <p class="password-hint">🔒 Solo completa este campo si deseas cambiar tu contraseña.</p>
            </div>

            <!-- Acciones -->
            <button type="submit" class="btn-guardar">Guardar cambios</button>
            <a href="dashboard.php" class="btn-volver">← Volver al dashboard</a>

        </form>

    </div>
</div>

</body>
</html>