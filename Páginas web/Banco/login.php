<?php
session_start();
require_once 'GoogleAuthenticator.php';
$g = new PHPGangsta_GoogleAuthenticator();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $otp = isset($_POST['otp']) ? $_POST['otp'] : '';

    // Conectar a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'bank');

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, password, google_auth_secret, numero_cuenta FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $google_auth_secret, $numero_cuenta);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $result = $g->verifyCode($google_auth_secret, $otp, 0); // 0 = 0*30sec reloj desfase

            if ($result) {
                $_SESSION['username'] = $username;
                $_SESSION['numero_cuenta'] = $numero_cuenta;
                header("Location: menu.php");
                exit();
            } else {
                echo "<div class='container'><div class='error'>Código OTP inválido</div></div>";
            }
        } else {
            echo "<div class='container'><div class='error'>Contraseña incorrecta</div></div>";
        }
    } else {
        echo "<div class='container'><div class='error'>Nombre de usuario no encontrado</div></div>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
   
    <style>
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
        <h2>Inicio de sesion</h2>
        <form method="post" action="login.php">
            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <label for="otp">Código OTP:</label>
            <input type="text" id="otp" name="otp" required>
            <br>
            <button type="submit">Acceder</button>
        </form>
        
<br>
<form method="get" action="register.php">
    <button type="submit">Regristro</button>
</form>
    </div>
</body>
</html>
