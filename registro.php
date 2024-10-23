<?php
include 'config.php'; // Archivo de conexión a la base de datos

// Si el formulario de registro es enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insertar el nuevo usuario en la base de datos
    $query = "INSERT INTO medicos (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $nombre, $email, $password);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Hubo un error al registrar al usuario.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/StyleIndexRegistro.css">
    <title>MedEx - Registro</title>
</head>
<body>
    <div class="container">
        <!-- Sección izquierda con el logo centrado -->
        <div class="left-side">
            <img src="Medex.png" alt="Logo de MedEx">
        </div>
        
        <!-- Sección derecha con el formulario -->
        <div class="right-side">
            <h2>Registro</h2>
            <form method="POST" action="registro.php">
                <input type="text" name="nombre" placeholder="Nombre de usuario" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Contraseña" required minlength="3">
                <button type="submit">Registrarse</button>
            </form>
            
            <form action="index.php" method="get">
                <button type="submit">Volver</button>
            </form>
        </div>
    </div>

    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            var password = document.querySelector('input[name="password"]').value;
            
            if (password.length < 3) {
                event.preventDefault();
                alert('La contraseña debe tener al menos 3 caracteres.');
            }
        });
    </script>
    <script>
    const inputs = document.querySelectorAll('input');
    const form = document.querySelector('form');

    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            form.classList.add('active');
        });

        input.addEventListener('blur', () => {
            form.classList.remove('active');
        });
    });
</script>
</body>
</html>


