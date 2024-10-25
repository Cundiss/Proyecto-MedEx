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
    
            // Redirigir con el parámetro "atendido" para mostrar la alerta
            header("Location: inicio.php?atendido=1");
            exit;
        }
    }
    
}
// Mover paciente a la sección "Atendidos" como aplazado
if (isset($_GET['aplazar'])) {
    $turno_id = $_GET['aplazar'];

    // Obtener los datos del turno a mover
    $sql = "SELECT t.*, p.nombre, p.apellido, p.dni 
            FROM turnos t
            JOIN pacientes p ON t.paciente_id = p.paciente_id 
            WHERE t.turno_id = $turno_id AND p.medico_id = $medico_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row) {
        // Insertar en la tabla "atendidos" con aplazado=1
        $sql_insert = "INSERT INTO atendidos (pacientes_id, medico_id, fecha_atencion, nombre, apellido, dni, motivo, aplazado)
                       VALUES ('{$row['paciente_id']}', '$medico_id', NOW(), '{$row['nombre']}', '{$row['apellido']}', '{$row['dni']}', '', 1)";

        if ($conn->query($sql_insert) === TRUE) {
            // Eliminar el turno de la tabla "turnos"
            $sql_delete = "DELETE FROM turnos WHERE turno_id=$turno_id";
            $conn->query($sql_delete);

            // Redirigir con el parámetro "aplazado" para mostrar la alerta
            header("Location: inicio.php?aplazado=1");
            exit;
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
            <!-- Cambiado el orden de los elementos -->
            <span class="pendiente-nombre"><?= $row['nombre'] ?> <?= $row['apellido'] ?></span>
            <span class="pendiente-dni"><?= $row['dni'] ?></span>
            <span class="pendiente-fecha"><?= date('d-m-Y', strtotime($row['fecha'])) ?> <?= $row['horario'] ?></span>
            <div class="btn-group">
            <a href="?atender=<?= $row['turno_id'] ?>" class="btn-atender">Atender</a>
            <!-- Aplazar -->
            <a href="?aplazar=<?= $row['turno_id'] ?>" class="btn-aplazar" data-id="<?= $row['turno_id'] ?>">Aplazar</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>


        <div class="column atendidos-box">
            <h3>Atendidos</h3>
            <?php while ($row = $atendidos->fetch_assoc()): ?>
    <div class="atendido-item" style="background-color: <?= $row['aplazado'] ? '#f8d7da' : 'transparent' ?>;">
        <span class="atendido-nombre"><?= $row['nombre'] ?> <?= $row['apellido'] ?></span>
        <span class="atendido-dni"><?= $row['dni'] ?></span>
        <span class="atendido-fecha"><?= date('d-m-Y', strtotime($row['fecha_atencion'])) ?></span>
        <a href="borrar_atendido" class="btn-borrar" data-id="<?= $row['atendido_id'] ?>">Borrar</a>
    </div>
<?php endwhile; ?>

            <button id="vaciarAtendidos" class="btn-vaciar">Vaciar Atendidos</button>


        </div>
    </div>
</div>
<form action="registro_atendidos.php" method="get">
    <button type="submit">Ir al registro de atendidos</button>
</form>
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
// Verificar si el parámetro "atendido" o "aplazado" está en la URL
const urlParams = new URLSearchParams(window.location.search);

if (urlParams.has('atendido')) {
    Swal.fire({
        icon: 'success',
        title: 'Paciente atendido',
        showConfirmButton: false,
        timer: 2000  // La alerta desaparecerá automáticamente después de 2 segundos
    });
}

if (urlParams.has('aplazado')) {
    Swal.fire({
        icon: 'success',
        title: 'Paciente aplazado',
        icon: 'success',
        showConfirmButton: false,
        timer: 2000  // La alerta desaparecerá automáticamente después de 2 segundos
    });
}
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los formularios en la página
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                form.classList.add('active');
            });

            input.addEventListener('blur', () => {
                // Verificar si alguno de los inputs aún está enfocado
                const isFocused = Array.from(inputs).some(input => input === document.activeElement);
                if (!isFocused) {
                    form.classList.remove('active');
                }
            });
        });
    });
});
</script>

<script>
    // SweetAlert2 para aplazar turnos
document.querySelectorAll('.btn-aplazar').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();  // Prevenir la acción predeterminada
        var turnoId = this.getAttribute('data-id');  // Obtener el ID del turno

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción aplazará el turno del paciente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, aplazar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir para aplazar el turno
                window.location.href = 'inicio.php?aplazar=' + turnoId;
            }
        });
    });
});

</script>
<script>
document.querySelectorAll('.btn-borrar').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        var atendidoId = this.getAttribute('data-id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará al paciente atendido.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir para eliminar el paciente atendido
                window.location.href = 'inicio.php?delete_atendido=' + atendidoId;
            }
        });
    });
});
</script>











<?php
// Borrar un paciente atendido individualmente
if (isset($_GET['delete_atendido'])) {
    $atendido_id = $_GET['delete_atendido'];
    $sql_delete = "DELETE FROM atendidos WHERE atendido_id=$atendido_id AND medico_id=$medico_id";
    
    if ($conn->query($sql_delete) === TRUE) {
        // Recargar la página después de borrar
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Paciente atendido eliminado',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location.href = 'inicio.php';
            });
        </script>";
    } else {
        echo "Error al borrar atendido: " . $conn->error;
    }
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


