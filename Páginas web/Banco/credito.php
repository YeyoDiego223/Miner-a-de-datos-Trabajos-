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

$creditos_disponibles = [
    ["monto" => 1000, "prestamista" => "Banco A", "intereses" => "5%", "plazos" => "6 meses", "pago" => "semanal"],
    ["monto" => 5000, "prestamista" => "Banco B", "intereses" => "4%", "plazos" => "12 meses", "pago" => "mensual"],
    // Agrega más opciones de crédito aquí
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['credito_id'])) {
        $credito_id = $_POST['credito_id'];
        $credito_seleccionado = $creditos_disponibles[$credito_id];
        $nuevo_efectivo = $credito_seleccionado['monto'];

        $stmt_update = $conn->prepare("UPDATE usuarios SET efectivo = efectivo + ?, credito = ? WHERE username = ?");
        $stmt_update->bind_param("dss", $nuevo_efectivo, json_encode($credito_seleccionado), $username);

        if ($stmt_update->execute()) {
            header("Location: credito.php");
            exit();
        } else {
            echo "Error al actualizar el crédito.";
        }

        $stmt_update->close();
    } elseif (isset($_POST['monto_pago'])) {
        $monto_pago = $_POST['monto_pago'];

        if ($monto_pago <= $efectivo && $monto_pago > 0) {
            $nuevo_efectivo = $efectivo - $monto_pago;
            $nuevo_monto_credito = $credito['monto'] - $monto_pago;

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
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Créditos</title>
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
        <h2>Gestión de Créditos</h2>
        <div class="mensaje">
            <p>Efectivo actual: <?php echo htmlspecialchars($efectivo); ?></p>
        </div>
        <?php if ($credito): ?>
            <h2>Pago de Crédito</h2>
            <p>Monto del crédito: <?php echo htmlspecialchars($credito['monto']); ?></p>
            <form method="post" action="credito.php">
                <label for="monto_pago">Monto a abonar:</label>
                <input type="number" id="monto_pago" name="monto_pago" required>
                <br>
                <button type="submit">Abonar</button>
            </form>
        <?php else: ?>
            <h2>Seleccionar Crédito</h2>
            <?php foreach ($creditos_disponibles as $index => $credito_opcion): ?>
                <form method="post" action="credito.php">
                    <input type="hidden" name="credito_id" value="<?php echo $index; ?>">
                    <p>Monto: <?php echo $credito_opcion['monto']; ?></p>
                    <p>Prestamista: <?php echo $credito_opcion['prestamista']; ?></p>
                    <p>Intereses: <?php echo $credito_opcion['intereses']; ?></p>
                    <p>Plazos: <?php echo $credito_opcion['plazos']; ?></p>
                    <p>Pago: <?php echo $credito_opcion['pago']; ?></p>
                    <button type="submit">Seleccionar</button>
                </form>
                <br>
            <?php endforeach; ?>
        <?php endif; ?>
        <br>
        <form method="get" action="menu.php">
            <button type="submit">Regresar</button>
        </form>
    </div>
</body>
</html>
