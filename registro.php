<?php
include 'config.php'; // Archivo de conexión a la base de datos

// Si el formulario de registro es enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol = $_POST['rol']; // Obtenemos el rol seleccionado (medico o secretario)

    // Insertar el nuevo usuario en la base de datos
    $query = "INSERT INTO medicos (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $nombre, $email, $password, $rol);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Hubo un error al registrar al usuario.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/StyleIndexRegistro.css">
    <title>MedEx - Registro</title>
</head>
<body>
    <h2>Registro</h2>
    <form method="POST" action="registro.php">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <select name="rol" required>
            <option value="medico">Médico</option>
            <option value="secretario">Secretario</option>
        </select>
        <button type="submit">Registrarse</button>
    </form>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
</body>
</html>

