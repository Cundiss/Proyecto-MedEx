<?php
$servername = "localhost"; // o la IP del servidor
$username = "root"; // usuario por defecto en XAMPP
$password = ""; // contraseña por defecto en XAMPP
$dbname = "medex";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
