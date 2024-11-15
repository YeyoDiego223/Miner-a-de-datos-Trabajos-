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
$stmt = $conn->prepare("SELECT efectivo, credito FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($efectivo, $credito_json);
$stmt->fetch();
$stmt->close();

$credito = json_decode($credito_json, true);
$monto_credito = $credito['monto'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['abonar'])) {
    $abonar = $_POST['abonar'];

    if ($abonar == "total") {
        $monto_a_abonar = $monto_credito;
    } else {
        $monto_a_abonar = ($credito['pago'] == "semanal") ? $monto_credito / (4 * 6) : $monto_credito / 12;
    }

    if ($efectivo >= $monto_a_abonar) {
        $nuevo_efectivo = $efectivo - $monto_a_abonar;
        $nuevo_monto_credito = $monto_credito - $monto_a_abonar;

        if ($nuevo_monto_credito <= 0) {
            $credito = null;
        } else {
            $credito['monto'] = $nuevo_monto_credito;
        }

        $stmt_update = $conn->prepare("UPDATE usuarios SET efectivo = ?, credito = ? WHERE username = ?");
        $stmt_update->bind_param("dss", $nuevo_efectivo, json_encode($credito), $username);

        if ($stmt_update->execute()) {
            header("Location: credito.php");
            exit();
        } else {
            echo "Error al abonar el crédito.";
        }

        $stmt_update->close();
    } else {
        echo "Saldo insuficiente para abonar.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Abonar Crédito</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Abonar Crédito</h2>
        <form method="post" action="pago_credito.php">
            <label for="abonar">¿Cuánto quieres abonar?</label>
            <select id="abonar" name="abonar">
                <option value="total">Abonar Total: <?php echo htmlspecialchars($monto_credito); ?></option>
                <option value="parcial">Pago Semanal/Mensual</option>
            </select>
            <br>
            <button type="submit">Abonar</button>
        </form>
        <br>
        <form method="get" action="menu.php">
            <button type="submit">Regresar</button>
        </form>
    </div>
</body>
</html>
