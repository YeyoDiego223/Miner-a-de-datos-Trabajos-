<?php
require_once 'GoogleAuthenticator.php';
$g = new PHPGangsta_GoogleAuthenticator();

function generarNumeroCuenta($conn) {
    do {
        $numero_cuenta = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT) . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE numero_cuenta = ?");
        $stmt->bind_param("s", $numero_cuenta);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);
    return $numero_cuenta;
}

function generarNIP() {
    return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
    $sexo = isset($_POST['sexo']) ? $_POST['sexo'] : '';
    $fecha_nacimiento = isset($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : '';
    $google_auth_secret = $g->createSecret();
    $qrCodeUrl = $g->getQRCodeGoogleUrl('YourAppName', $google_auth_secret);

    // Calcular edad
    $fecha_nacimiento_dt = new DateTime($fecha_nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento_dt)->y;

    // Conectar a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'bank');

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Generar número de cuenta único y NIP
    $numero_cuenta = generarNumeroCuenta($conn);
    $nip = generarNIP();
    $efectivo = 0;

    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, google_auth_secret, nombre, apellidos, sexo, fecha_nacimiento, edad, numero_cuenta, nip, efectivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bind_param("sssssssissi", $username, $hashed_password, $google_auth_secret, $nombre, $apellidos, $sexo, $fecha_nacimiento, $edad, $numero_cuenta, $nip, $efectivo);

    if ($stmt->execute()) {
        echo "<div class='container'>";
        echo "<div class='success'>Registro exitoso. Escanea este código QR con Google Authenticator:</div>";
        echo "<img src='".$qrCodeUrl."'><br>";
        echo "</div>";
    } else {
        echo "<div class='container'>";
        echo "<div class='error'>Error en el registro: " . $stmt->error . "</div>";
        echo "</div>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validarEntrada(event) {
            const pattern = /^[A-Za-z\s]*$/;
            const input = event.target;
            if (!pattern.test(input.value)) {
                input.value = input.value.replace(/[^A-Za-z\s]/g, '');
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nombre').addEventListener('input', validarEntrada);
            document.getElementById('apellidos').addEventListener('input', validarEntrada);
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>
        <form method="post" action="register.php">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" pattern="[A-Za-z\s]{2,}" title="El nombre debe contener solo letras y al menos 2 caracteres" required>
            <br>
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" pattern="[A-Za-z\s]{2,}" title="Los apellidos deben contener solo letras y al menos 2 caracteres" required>
            <br>
            <label for="sexo">Sexo:</label>
            <select id="sexo" name="sexo" required>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
            </select>
            <br>
            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            <br>
            <label for="username">Nombre de usuario:</label>
            <input type="text" id="username" name="username"   required>
            <br>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Registrar</button>
        </form>
        <br>
        <form method="get" action="menu.php">
    <button type="submit">Regresar</button>
</form>
    </div>
</body>
</html>
