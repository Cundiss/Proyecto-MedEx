<?php
session_start();
include 'config.php'; // Archivo de conexión a la base de datos

// Si el formulario de inicio de sesión es enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario existe
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

            // Redirigir a la página principal de médicos
            header("Location: inicio.php");
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
        <!-- Sección izquierda para el logo-->
        <div class="left-side">
            <img src="Medex.png" alt="Logo de MedEx">
        </div>
        
        <!-- Sección derecha para el formulario -->
        <div class="right-side">
            <h2>Inicio de Sesión</h2>
            <form method="POST" action="index.php">
    <input type="email" name="email" placeholder="Email" required value="<?php if (isset($email)) echo htmlspecialchars($email); ?>">
    <input type="password" name="password" placeholder="Contraseña" required minlength="3" value="<?php if (isset($password)) echo htmlspecialchars($password); ?>">
    <button type="submit">Iniciar Sesión</button>
</form>

            <!-- Aquí se mostrará el mensaje de error -->
            <?php if (isset($error)) { ?>
                <p style="color: white; font-size: 18px; font-weight: bold; animation: blink 3s; margin-top: 15px">
                    <?php echo $error; ?>
                </p>
            <?php } ?>

            <form method="GET" action="registro.php">
                <button type="submit">¿No tienes cuenta? Regístrate</button>
            </form>
        </div>
    </div>

    <!-- Script para prevenir envíos con contraseñas cortas -->
<script>
document.querySelector('form').addEventListener('submit', function(event) {
    var password = document.querySelector('input[name="password"]').value;
    
    if (password.length < 3) {
        event.preventDefault();
        alert('La contraseña debe tener al menos 3 caracteres.');
    }
});

</script>
<!-- Script para agrandar forms al pasar el puntero -->
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

<!-- Animación de parpadeo para el mensaje de error -->
<style>
        @keyframes blink {
    0%, 50%, 100% {
        opacity: 1;
    }
    25%, 75% {
        opacity: 0;
    }
}
    </style>


</body>
</html>








