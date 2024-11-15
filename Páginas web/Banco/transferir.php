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
$stmt = $conn->prepare("SELECT nombre, numero_cuenta, efectivo FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($nombre, $numero_cuenta_origen, $efectivo);
$stmt->fetch();
$stmt->close();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['monto']) && isset($_POST['numero_cuenta'])) {
    $monto = $_POST['monto'];
    $numero_cuenta_destino = $_POST['numero_cuenta'];

    if ($monto <= $efectivo && $monto > 0) {
        $stmt_destino = $conn->prepare("SELECT efectivo FROM usuarios WHERE numero_cuenta = ?");
        $stmt_destino->bind_param("s", $numero_cuenta_destino);
        $stmt_destino->execute();
        $stmt_destino->bind_result($efectivo_destino);
        $stmt_destino->store_result();
        $stmt_destino->fetch();

        if ($stmt_destino->num_rows > 0) {
            $nuevo_efectivo_origen = $efectivo - $monto;
            $nuevo_efectivo_destino = $efectivo_destino + $monto;

            $conn->autocommit(FALSE);

            $update_origen = $conn->prepare("UPDATE usuarios SET efectivo = ? WHERE numero_cuenta = ?");
            $update_origen->bind_param("ds", $nuevo_efectivo_origen, $numero_cuenta_origen);

            $update_destino = $conn->prepare("UPDATE usuarios SET efectivo = ? WHERE numero_cuenta = ?");
            $update_destino->bind_param("ds", $nuevo_efectivo_destino, $numero_cuenta_destino);

            if ($update_origen->execute() && $update_destino->execute()) {
                $conn->commit();
                $mensaje = "Transferencia exitosa. El nuevo saldo es $nuevo_efectivo_origen.";
                $efectivo = $nuevo_efectivo_origen;
            } else {
                $conn->rollback();
                $mensaje = "Error al realizar la transferencia.";
            }

            $update_origen->close();
            $update_destino->close();
        } else {
            $mensaje = "Número de cuenta destino no encontrado.";
        }

        $stmt_destino->close();
    } else {
        $mensaje = "Saldo insuficiente o monto inválido.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Transferir</title>
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
        <h2>Transferir Dinero</h2>
        <div class="mensaje">
            <p>Nombre del usuario: <?php echo htmlspecialchars($nombre); ?></p>
            <p>Efectivo actual: <?php echo htmlspecialchars($efectivo); ?></p>
        </div>
        <form method="post" action="transferir.php">
            <label for="numero_cuenta">Número de cuenta destino:</label>
            <input type="text" id="numero_cuenta" name="numero_cuenta" required>
            <br>
            <label for="monto">Monto a transferir:</label>
            <input type="number" id="monto" name="monto" required>
            <br>
            <button type="submit">Transferir</button>
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
