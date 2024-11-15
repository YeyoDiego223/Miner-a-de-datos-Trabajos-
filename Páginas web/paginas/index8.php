<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "acceso";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['libro'];
    buscar_libro($conn, $titulo);
}

function buscar_libro($conn, $titulo) {
    $sql = "SELECT * FROM libros WHERE nombre LIKE ?";
    $like_titulo = $titulo . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $like_titulo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>  CODIGO  </th><th>   Nombre del Libro    </th><th>   Editorial   </th><th>   Precio  </th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>  " . $row["codigo"]. "   </td>";
            echo "<td>  " . $row["nombre"]. "   </td>";
            echo "<td>  " . $row["editorial"]. "    </td>";
            echo "<td>  " . $row["precio"]. "   </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No encontramos libros que empiecen con esa letra :(</p>";
    }
    
    $stmt->close();
}

$conn->close();
?>