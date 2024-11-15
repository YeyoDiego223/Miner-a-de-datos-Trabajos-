<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'bank');

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT nombre, efectivo, nip FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($nombre, $efectivo, $nip);
$stmt->fetch();
$stmt->close();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['monto']) && isset($_POST['nip'])) {
    $monto = $_POST['monto'];
    $ingresado_nip = $_POST['nip'];

    if ($ingresado_nip == $nip) {
        if ($monto <= $efectivo) {
            $nuevo_efectivo = $efectivo - $monto;
            $update_stmt = $conn->prepare("UPDATE usuarios SET efectivo = ? WHERE username = ?");
            $update_stmt->bind_param("ds", $nuevo_efectivo, $username);

            if ($update_stmt->execute()) {
                $mensaje = "Retiro exitoso. El nuevo saldo es $nuevo_efectivo.";
                $efectivo = $nuevo_efectivo;
            } else {
                $mensaje = "Error al realizar el retiro.";
            }
            $update_stmt->close();
        } else {
            $mensaje = "Saldo insuficiente.";
        }
    } else {
        $mensaje = "NIP incorrecto.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Retirar</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .mensaje {
            margin-bottom: 20px;
            color: #0056b3;
        }
        /* Fondo verde para toda la página */
body {
    background-color: #2e8b57; /* Verde oscuro */
    color: white;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

/* Contenedor principal */
.container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    color: #000;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

/* Estilo para los títulos */
h1, h2 {
    color: #fff;
    text-align: center;
}

/* Estilo para los formularios y campos */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

label, input, select, button {
    width: 80%;
    margin: 10px 0;
    padding: 10px;
    border: none;
    border-radius: 5px;
}

/* Estilo para los botones */
button {
    background-color: #a9a9a9; /* Gris */
    color: #000;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #808080; /* Gris oscuro */
}

/* Estilo para los mensajes */
.mensaje {
    background-color: #f0f0f0;
    color: #333;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

/* Estilo para los enlaces de acción */
.action img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    cursor: pointer;
    margin: 10px;
}

.action-title {
    margin-top: 10px;
    font-size: 18px;
    color: #0056b3;
    text-align: center;
}

.actions {
    display: flex;
    justify-content: space-around;
    margin-top: 50px;
}

/* Estilo para el formulario de cerrar sesión */
.logout {
    text-align: center;
    margin-top: 50px;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Retirar Dinero</h2>
        <div class="mensaje">
            <p>Nombre del usuario: <?php echo htmlspecialchars($nombre); ?></p>
            <p>Efectivo actual: <?php echo htmlspecialchars($efectivo); ?></p>
        </div>
        <form method="post" action="retirar.php">
            <label for="monto">Monto a retirar:</label>
            <input type="number" id="monto" name="monto" required>
            <br>
            <label for="nip">NIP:</label>
            <input type="password" id="nip" name="nip" required>
            <br>
            <button type="submit">Retirar</button>
        </form>
        <br>
        <form method="get" action="menu.php">
    <button type="submit">Regresar</button>
</form>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
