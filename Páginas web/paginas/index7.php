<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "examen";

$user=$_POST['usuario'];

$conexion=mysqli_connect($servername, $username, $password, $dbname);
if (!$conexion) 
{
    echo "Error en la conexión: " . mysqli_connect_error();
}
else
{
    $consulta = "SELECT * FROM usuarios WHERE usuario='$user'";
    $resultado = mysqli_query($conexion, $consulta);
    if (mysqli_num_rows($resultado) >0){
        header("Refresh: 2; URL=flecharoja.php"); // Espera 2 segundos antes de redirigir
        exit;
    } else {
        echo "Usuario incorrecto";
    }
    mysqli_free_result($resultado);
    mysqli_close($conexion);
}
?>