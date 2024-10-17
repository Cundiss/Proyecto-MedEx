<?php
session_start();
if (!isset($_SESSION['medico_id'])) {
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medex";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el medico_id de la sesión
$medico_id = $_SESSION['medico_id'];

// Obtener datos del médico logueado
$sql_medico = "SELECT nombre, email FROM medicos WHERE medico_id = $medico_id";
$result_medico = $conn->query($sql_medico);
$medico = $result_medico->fetch_assoc();

// Mover paciente a la sección "Atendidos"
if (isset($_GET['atender'])) {
    $turno_id = $_GET['atender'];

    // Obtener los datos del turno a mover
    $sql = "SELECT t.*, p.nombre, p.apellido, p.dni 
            FROM turnos t
            JOIN pacientes p ON t.paciente_id = p.paciente_id 
            WHERE t.turno_id = $turno_id AND p.medico_id = $medico_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row) {
        // Insertar en la tabla "atendidos"
        $sql_insert = "INSERT INTO atendidos (pacientes_id, medico_id, fecha_atencion, nombre, apellido, dni, motivo)
                       VALUES ('{$row['paciente_id']}', '$medico_id', NOW(), '{$row['nombre']}', '{$row['apellido']}', '{$row['dni']}', '')";

        if ($conn->query($sql_insert) === TRUE) {
            // Eliminar el turno de la tabla "turnos"
            $sql_delete = "DELETE FROM turnos WHERE turno_id=$turno_id";
            $conn->query($sql_delete);
        }
    }
}

// Obtener el próximo paciente (turno más cercano) del médico
$sql_proximo = "SELECT t.*, p.nombre, p.apellido, p.dni 
                FROM turnos t
                JOIN pacientes p ON t.paciente_id = p.paciente_id 
                WHERE p.medico_id = $medico_id 
                ORDER BY t.fecha ASC, t.horario ASC LIMIT 1";
$result_proximo = $conn->query($sql_proximo);
$proximo_paciente = $result_proximo->fetch_assoc();

// Obtener los pacientes pendientes del médico
$sql_pendientes = "SELECT t.*, p.nombre, p.apellido, p.dni 
                   FROM turnos t
                   JOIN pacientes p ON t.paciente_id = p.paciente_id 
                   WHERE p.medico_id = $medico_id 
                   ORDER BY t.fecha ASC, t.horario ASC";
$pendientes = $conn->query($sql_pendientes);

// Obtener los pacientes atendidos por el médico
$sql_atendidos = "SELECT * FROM atendidos 
                  WHERE medico_id = $medico_id 
                  ORDER BY fecha_atencion DESC";
$atendidos = $conn->query($sql_atendidos);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/StyleInicio.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
    <title>MedEx</title>
</head>
<body>
<header>
    <nav class="nav">
        <a href="turnos.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'turnos.php') ? 'activo' : ''; ?>">Turnos</a>
        <a href="pacientes.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'pacientes.php') ? 'activo' : ''; ?>">Pacientes</a>

        <!-- Reemplazo de "Inicio" por imagen -->
        <a href="inicio.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'inicio.php') ? 'activo' : ''; ?>">
            <img src="icon.png" alt="Inicio">
        </a>

        <a href="calendario.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'calendario.php') ? 'activo' : ''; ?>">Calendario</a>
        
        <div class="dropdown">
            <a class="dropbtn">Cuenta</a>
            <div class="dropdown-content">
                <p><strong>Nombre:</strong> <?= $medico['nombre']; ?></p>
                <p><strong>Email:</strong> <?= $medico['email']; ?></p>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
</header>




<div class="container">
    <div class="MedEx">
    <img src="MedexPNG.png" alt="Inicio">
    </div>

    <div class="columns">
        <div class="column pendientes-box">
            <h3>Pendientes</h3>
            <?php while ($row = $pendientes->fetch_assoc()): ?>
                <div class="pendiente-item">
                    <span><?= date('d-m-Y', strtotime($row['fecha'])) ?> <?= $row['horario'] ?></span>
                    <span><?= $row['nombre'] ?> <?= $row['apellido'] ?></span>
                    <span><?= $row['dni'] ?></span>
                    <a href="?atender=<?= $row['turno_id'] ?>" class="btn-atender">Atender</a>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="column atendidos-box">
            <h3>Atendidos</h3>
            <?php while ($row = $atendidos->fetch_assoc()): ?>
                <div class="atendido-item">
                    <span><?= $row['nombre'] ?> <?= $row['apellido'] ?></span>
                    <span><?= $row['dni'] ?></span>
                    <span><?= date('d-m-Y', strtotime($row['fecha_atencion'])) ?></span>
                    <a href="?delete_atendido=<?= $row['atendido_id'] ?>" class="btn-borrar">Borrar</a>
                </div>
            <?php endwhile; ?>
            <button id="vaciarAtendidos" class="btn-vaciar">Vaciar Atendidos</button>


        </div>
    </div>
</div>

<!--
<footer>
    <p>&copy; 2024 MedEx - Todos los derechos reservados</p>
</footer>
-->


</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var dropdown = document.querySelector('.dropdown');
    var dropbtn = document.querySelector('.dropbtn');

    // Agregar un evento de clic para mostrar/ocultar el menú
    dropbtn.addEventListener('click', function() {
        dropdown.classList.toggle('show'); // Alterna la clase 'show' para el menú
    });

    // Cerrar el menú si se hace clic fuera del dropdown
    window.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
});
</script>
<script>
// SweetAlert2 para vaciar atendidos
document.querySelector('.btn-vaciar').addEventListener('click', function() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esto eliminará todos los pacientes atendidos',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir al mismo archivo con el parámetro para vaciar
            window.location.href = 'inicio.php?vaciar_atendidos=1';
        }
    });
});



</script>




<?php
// Borrar un paciente atendido individualmente
if (isset($_GET['delete_atendido'])) {
    $atendido_id = $_GET['delete_atendido'];
    $sql_delete = "DELETE FROM atendidos WHERE atendido_id=$atendido_id AND medico_id=$medico_id";
    $conn->query($sql_delete);
}

// Vaciar todos los pacientes atendidos del médico
if (isset($_GET['vaciar_atendidos'])) {
    $sql_vaciar = "DELETE FROM atendidos WHERE medico_id=$medico_id";
    if ($conn->query($sql_vaciar) === TRUE) {
        // Mostrar mensaje de éxito con SweetAlert2
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: '¡Atendidos vaciados!',
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                // Recargar la página sin parámetros en la URL
                window.location.href = 'inicio.php';
            });
        </script>";
    } else {
        echo "Error al vaciar atendidos: " . $conn->error;
    }
}




$conn->close();
?>


