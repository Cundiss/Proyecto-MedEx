

<?php
// Incluir la conexión a la base de datos
include 'config.php';

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $edad = $_POST['edad'];
    $dni = $_POST['dni'];
    $mutual = $_POST['mutual'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // Insertar datos en la tabla "pacientes"
    $query = "INSERT INTO pacientes (nombre, apellido, edad, dni, mutual, email, telefono) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssissss", $nombre, $apellido, $edad, $dni, $mutual, $email, $telefono);

    // Ejecutar la consulta y verificar si fue exitosa
    if ($stmt->execute()) {
        $mensaje = "Paciente añadido exitosamente.";
    } else {
        $mensaje = "Error al añadir el paciente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Paciente</title>
    <link rel="stylesheet" href="styles.css"> <!-- Opcional: Archivo de estilos -->
</head>
<body>
<nav>
        <a href="#">Turnos</a>
        <a href="pacientes.php">Pacientes</a>
        <a href="inicio.php">Inicio</a>
        <a href="#">Calendario</a>
        <a href="#">Buscar</a>
    </nav>
    <h2>Agregar Nuevo Paciente</h2>

    <!-- Mostrar mensaje si se añadió correctamente -->
    <?php if (isset($mensaje)) { echo "<p>$mensaje</p>"; } ?>

    <form method="POST" action="pacientes.php">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" required><br>

        <label for="edad">Edad:</label>
        <input type="number" id="edad" name="edad" required><br>

        <label for="dni">DNI:</label>
        <input type="text" id="dni" name="dni" required><br>

        <label for="mutual">Mutual:</label>
        <input type="text" id="mutual" name="mutual"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email"><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono"><br>

        <button type="submit">Añadir Paciente</button>
    </form>
</body>
</html>
