<?php
include 'config.php'; // Archivo de conexión a la base de datos

// Si el formulario de registro es enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insertar el nuevo médico en la base de datos
    $query = "INSERT INTO medicos (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $nombre, $email, $password);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Hubo un error al registrar al médico.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>MedEx - Registro</title>
</head>
<body>
    <h2>Registro de Médico</h2>
    <form method="POST" action="registro.php">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Registrarse</button>
    </form>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
</body>
</html>
