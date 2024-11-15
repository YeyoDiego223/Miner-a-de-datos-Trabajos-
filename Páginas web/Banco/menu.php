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

// Obtener el nombre, número de cuenta y NIP del usuario
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT nombre, numero_cuenta, nip FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($nombre, $numero_cuenta, $nip);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .header {
            text-align: center;
            margin-top: 20px;
        }
        .actions {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
        }
        .action {
            text-align: center;
        }
        .action img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            cursor: pointer;
        }
        .action-title {
            margin-top: 10px;
            font-size: 18px;
            color: #0056b3;
        }
        .logout {
            text-align: center;
            margin-top: 50px;
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
        <div class="header">
            <h1>Bienvenido: <?php echo htmlspecialchars($nombre); ?></h1>
            <p>Número de cuenta: <?php echo htmlspecialchars($numero_cuenta); ?></p>
            <p>NIP: <?php echo htmlspecialchars($nip); ?></p>
        </div>
        <div class="actions">
            <div class="action">
                <a href="depositar.php">
                    <img src="deposito.png" alt="Depositar">
                    <div class="action-title">Depositar</div>
                </a>
            </div>
            <div class="action">
                <a href="retirar.php">
                    <img src="retiro.jfif" alt="Retirar">
                    <div class="action-title">Retirar</div>
                </a>
            </div>
            <div class="action">
                <a href="transferir.php">
                    <img src="transferir.jfif" alt="Transferir">
                    <div class="action-title">Transferir</div>
                </a>
            </div>
            <div class="action">
                <a href="credito.php">
                    <img src="credito.png" alt="Crédito">
                    <div class="action-title">Crédito</div>
                </a>
            </div>
        </div>
        <div class="logout">
            <form method="post" action="cerrar.php">
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
