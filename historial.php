<?php
// Iniciar sesión para capturar el id del médico
session_start();
if (!isset($_SESSION['medico_id'])) {
    header("Location: index.php");
    exit;
}
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medex";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el id del médico desde la sesión
if (isset($_SESSION['medico_id'])) {
    $medico_id = $_SESSION['medico_id'];
} else {
    die("Médico no autenticado.");
}


// Consultar los datos del médico para mostrarlos en la sección "Cuenta"
$sql_medico = "SELECT nombre, email FROM medicos WHERE medico_id='$medico_id'";
$result_medico = $conn->query($sql_medico);
$medico = $result_medico->fetch_assoc();

// Obtener el nombre y apellido del paciente
if (isset($_GET['paciente_id'])) {
    $paciente_id = $_GET['paciente_id'];
    $paciente = $conn->query("SELECT nombre, apellido FROM pacientes WHERE paciente_id = $paciente_id");
    $paciente_info = $paciente->fetch_assoc();
    $nombre_completo = $paciente_info['nombre'] . ' ' . $paciente_info['apellido'];
    $historial = $conn->query("SELECT * FROM historial WHERE paciente_id = $paciente_id");
} else {
    die("Paciente no especificado.");
}


$mensaje = "";

// Agregar nuevo historial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_historial'])) {
    $paciente_id = $_POST['paciente_id'];
    $medico_id = $_POST['medico_id'];
    $fecha = $_POST['fecha'];  // Fecha tomada del formulario
    $detalle = $_POST['detalle'];

    $sql = "INSERT INTO historial (paciente_id, medico_id, fecha, detalle) VALUES ('$paciente_id', '$medico_id', '$fecha', '$detalle')";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Historial añadido con éxito";
        header("Location: historial.php?paciente_id=$paciente_id&mensaje=añadido");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Editar historial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_historial'])) {
    $historial_id = $_POST['historial_id'];
    $detalle = $_POST['detalle'];

    $sql = "UPDATE historial SET detalle='$detalle' WHERE historial_id='$historial_id'";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Historial actualizado con éxito";
        header("Location: historial.php?paciente_id=$paciente_id&mensaje=actualizado");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Borrar historial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_borrado'])) {
    $historial_id = $_POST['historial_id'];

    $sql = "DELETE FROM historial WHERE historial_id='$historial_id'";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Historial borrado con éxito";
        header("Location: historial.php?paciente_id=$paciente_id&mensaje=borrado");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Comprobar si hay un mensaje desde la redirección
if (isset($_GET['mensaje'])) {
    if ($_GET['mensaje'] == 'añadido') {
        $mensaje = "Historial añadido con éxito";
    } elseif ($_GET['mensaje'] == 'actualizado') {
        $mensaje = "Historial actualizado con éxito";
    } elseif ($_GET['mensaje'] == 'borrado') {
        $mensaje = "Historial borrado con éxito";
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Médico</title>
    <link rel="stylesheet" href="Styles/StyleHistorial.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para autocompletar la fecha con la actual
        window.onload = function() {
            var hoy = new Date().toISOString().split('T')[0];  // Obtener la fecha actual en formato YYYY-MM-DD
            document.getElementById('fecha').value = hoy;  // Establecer la fecha en el campo de fecha
        };

        // Función para confirmar eliminación con SweetAlert2
        function confirmarEliminacion(historial_id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás deshacer esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario de eliminación con POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'historial.php?paciente_id=<?= $paciente_id; ?>';

                    const inputHistorialId = document.createElement('input');
                    inputHistorialId.type = 'hidden';
                    inputHistorialId.name = 'historial_id';
                    inputHistorialId.value = historial_id;

                    const inputConfirmarBorrado = document.createElement('input');
                    inputConfirmarBorrado.type = 'hidden';
                    inputConfirmarBorrado.name = 'confirmar_borrado';
                    inputConfirmarBorrado.value = '1';

                    form.appendChild(inputHistorialId);
                    form.appendChild(inputConfirmarBorrado);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</head>
<body>

<header>
    <nav class="nav">
        <div class="nav-scale">
        <a href="turnos.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'turnos.php') ? 'activo' : ''; ?>">Turnos</a>
        </div>
        <div class="nav-scale">
        <a href="pacientes.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'pacientes.php') ? 'activo' : ''; ?>">Pacientes</a>
        </div>

        <!-- Reemplazo de "Inicio" por imagen -->
         <div class="nav-scale">
        <a href="inicio.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'inicio.php') ? 'activo' : ''; ?>">
            <img src="icon.png" alt="Inicio">
        </a>
        </div>
        <div class="nav-scale">
        <a href="calendario.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'calendario.php') ? 'activo' : ''; ?>">Calendario</a>
        </div>
        
        <div class="dropdown">
            <div class="nav-scale">
            <a class="dropbtn">Cuenta</a>
            </div>
            <div class="dropdown-content">
                <p><strong>Nombre:</strong> <?= $medico['nombre']; ?></p>
                <p><strong>Email:</strong> <?= $medico['email']; ?></p>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
</header>

<h1>Historial Médico de <?= htmlspecialchars($nombre_completo); ?></h1>

<h2>Agregar nuevo historial</h2>
<form action="historial.php?paciente_id=<?= $paciente_id ?>" method="post">
    <input type="hidden" name="paciente_id" value="<?= $paciente_id; ?>">
    <input type="hidden" name="medico_id" value="<?= $medico_id; ?>">
    <input type="date" name="fecha" id="fecha" required>  <!-- Campo de fecha autocompletado -->
    <textarea name="detalle" placeholder="Detalles del historial" required></textarea>
    <input class="buscar-btn" type="submit" name="agregar_historial" value="Agregar Historial">
</form>

<h2>Historial de Paciente</h2>
<table>
    <tr>
        <th>Fecha</th>
        <th>Detalle</th>
        <th>Acciones</th>
    </tr>
    <?php while ($row = $historial->fetch_assoc()): ?>
    <tr>
        <form action="historial.php?paciente_id=<?= $paciente_id ?>" method="post">
            <td><?= date('d-m-Y', strtotime($row['fecha'])); ?></td> <!-- Fecha formateada -->
            <td><textarea name="detalle"><?= htmlspecialchars($row['detalle']); ?></textarea></td>
            <td>
                <input type="hidden" name="historial_id" value="<?= $row['historial_id']; ?>">
                <input class="update-btn" type="submit" name="guardar_historial" value="Guardar">
                <!-- Botón modificado para confirmar con SweetAlert2 -->
                <button type="button" class="delete-btn" onclick="confirmarEliminacion(<?= $row['historial_id']; ?>)">Borrar</button>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>

<?php if ($mensaje): ?>
<script>
    Swal.fire({
      title: '<?= $mensaje; ?>',
      icon: 'success',
      timer: 2000,
      showConfirmButton: false
    });
</script>
<?php endif; ?>

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
// Guardar la posición del scroll antes de recargar la página
window.addEventListener('beforeunload', function () {
    localStorage.setItem('scrollPosition', window.scrollY);
});

// Restaurar la posición del scroll después de recargar la página
window.addEventListener('load', function () {
    const scrollPosition = localStorage.getItem('scrollPosition');
    if (scrollPosition) {
        window.scrollTo(0, scrollPosition);
        localStorage.removeItem('scrollPosition'); // Limpiar después de restaurar
    }
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

</body>
</html>

<?php
$conn->close();
?>
