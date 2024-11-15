<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flecha Roja</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<style>
    body {
        background: rgb(138,14,14);
background: linear-gradient(90deg, rgba(138,14,14,1) 48%, rgba(255,0,7,1) 100%);
    }
    input {
        margin: 20px;
    }
    button {
        margin: 20px;
    }
    img {
        border-radius: 100%;
        min-height: 350px;
        max-height:350px;
        align-items: center;
        text-align:center;
        border: 5px black;
    }
    label {
        color:white;
    }
</style>
<body>
    <form action="">
        <center>
            <label for="">Nombre</label>
            <input type="text" id="nombre" pattern="[a-z,A-Z ]" required><br>
            <label for="">Apellido</label>
            <input type="text" id="apellido" pattern="[a-z,A-Z ]" required><br>
            <label for="">Destino</label>
            <select name="" id="ciudad" required><br>
            <option value="Tenancingo Degollado">Tenancingo Degollado</option>
            <option value="Tenango">Tenango</option>
            <option value="Villa Guerrero">Villa Guerrero</option>
            <option value="Santa Ana">Santa Ana</option>
            <option value="Toluca">Toluca</option>
            </select><br>
            <label for="">Fecha</label>
            <input type="date" id="fecha" required><br>
            <label for="">Asiento</label>
            <input type="number" id="asiento" pattern="[0-9]{,30}" required><br>
            <label for="">Precio</label>
            <input type="number" id="precio" required><br>
            <input type="checkbox" required>
            <label for="">Estas seguro de la compra</label><br>
            <button onclick="generatePDF()">GENERAR BOLETO</button>
        </center>
    </form>
    <center>
    <img src="flecha roja.jpg" alt="" height="450">
    <img src="autobus_flecha_roja.jfif" alt="">
    <img src="images.jfif" alt="">
    </center>
    <iframe id="pdfFrame" style="width: 100%; height: 500px;"></iframe>
    <center><button onclick="salir()">SALIR</button></center>
    <?php
    ?>
    <script>
        function salir() {
            header("index7.html");
        }

        function generatePDF() {
            const { jsPDF } = window.jspdf;

            // Crear una instancia de jsPDF
            const doc = new jsPDF();

            // Obtener el contenido del div
            const content1 = document.getElementById('nombre').value;
            const content2 = document.getElementById('apellido').value;
            const content3 = document.getElementById('ciudad').value;
            const content4 = document.getElementById('fecha').value;
            const content5 = document.getElementById('asiento').value;
            const content6 = document.getElementById('precio').value;
            // Agregar el contenido al PDF
            doc.text(content1, 10, 10);
            doc.text(content2, 10, 20);
            doc.text(content3, 10, 30);
            doc.text(content4, 10, 40);
            doc.text(content5, 10, 50);
            doc.text(content6, 10, 60);

            // Generar un objeto Blob a partir del PDF
            const pdfBlob = doc.output('blob');

            // Crear una URL temporal para el PDF
            const pdfUrl = URL.createObjectURL(pdfBlob);

            // Establecer la URL en el iframe para mostrar el PDF
            document.getElementById('pdfFrame').src = pdfUrl;
        }
    </script>
</body>
</html>