<?php
session_start();
include 'config.php'; // Archivo de conexión a la base de datos

// Si el formulario de inicio de sesión es enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario existe (médico o secretario)
    $query = "SELECT * FROM medicos WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verificar la contraseña
        if (password_verify($password, $row['password'])) {
            // Guardar la sesión del usuario
            $_SESSION['medico_id'] = $row['medico_id'];
            $_SESSION['rol'] = $row['rol'];

            // Redirigir dependiendo del rol
            if ($row['rol'] === 'medico') {
                header("Location: inicio.php"); // Página principal de médicos
            } elseif ($row['rol'] === 'secretario') {
                header("Location: inicio-secretario.php"); // Página principal de secretarios
            }
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No existe un usuario registrado con ese email.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/StyleIndexRegistro.css">
    <title>MedEx - Inicio de Sesión</title>
</head>
<body>
    <div class="container">
        <!-- Sección izquierda con el logo centrado -->
        <div class="left-side">
            <img src="Medex.png" alt="Logo de MedEx">
        </div>
        
        <!-- Sección derecha con el formulario -->
        <div class="right-side">
            <h2>Inicio de Sesión</h2>
            <form method="POST" action="index.php">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Contraseña" required minlength="3">
    <button type="submit">Iniciar Sesión</button>
</form>

            <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

            <form method="GET" action="registro.php">
                <button type="submit">¿No tienes cuenta? Regístrate</button>
            </form>
        </div>
    </div>
</body>
</html>






