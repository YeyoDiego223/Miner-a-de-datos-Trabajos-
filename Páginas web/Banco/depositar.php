<?php
session_start();

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'bank');

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numero_cuenta'])) {
    $numero_cuenta = $_POST['numero_cuenta'];

    $stmt = $conn->prepare("SELECT nombre, efectivo FROM usuarios WHERE numero_cuenta = ?");
    $stmt->bind_param("s", $numero_cuenta);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($nombre, $efectivo);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if (isset($_POST['monto'])) {
            $monto = $_POST['monto'];
            $nuevo_efectivo = $efectivo + $monto;

            $update_stmt = $conn->prepare("UPDATE usuarios SET efectivo = ? WHERE numero_cuenta = ?");
            $update_stmt->bind_param("ds", $nuevo_efectivo, $numero_cuenta);

            if ($update_stmt->execute()) {
                $mensaje = "Depósito exitoso. El nuevo saldo es $nuevo_efectivo.";
                $efectivo = $nuevo_efectivo;
            } else {
                $mensaje = "Error al realizar el depósito.";
            }
            $update_stmt->close();
        }
    } else {
        $mensaje = "Número de cuenta no encontrado.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Depositar</title>
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

.logout {
    text-align: center;
    margin-top: 50px;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Depósito de Dinero</h2>
        <?php if (isset($nombre)): ?>
            <div class="mensaje">
                <p>Nombre del usuario: <?php echo htmlspecialchars($nombre); ?></p>
                <p>Efectivo actual: <?php echo htmlspecialchars($efectivo); ?></p>
            </div>
            <form method="post" action="depositar.php">
    <input type="hidden" name="numero_cuenta" value="<?php echo htmlspecialchars($numero_cuenta); ?>">
    <label for="monto">Monto a depositar:</label>
    <input type="number" id="monto" name="monto" required>
    <br>
    <button type="submit">Depositar</button>
</form>
<br>
<form method="get" action="menu.php">
    <button type="submit">Regresar</button>
</form>

        <?php else: ?>
            <form method="post" action="depositar.php">
                <label for="numero_cuenta">Número de cuenta:</label>
                <input type="text" id="numero_cuenta" name="numero_cuenta" required>
                <br>
                <button type="submit">Buscar</button>
            </form>
        <?php endif; ?>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
