<?php
// Iniciar sesión para capturar el id del médico
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medex";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

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

// Obtener el id del médico desde la sesión
if (isset($_SESSION['medico_id'])) {
    $medico_id = $_SESSION['medico_id'];
} else {
    die("Médico no autenticado.");
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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['borrar_historial'])) {
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
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para autocompletar la fecha con la actual
        window.onload = function() {
            var hoy = new Date().toISOString().split('T')[0];  // Obtener la fecha actual en formato YYYY-MM-DD
            document.getElementById('fecha').value = hoy;  // Establecer la fecha en el campo de fecha
        };
    </script>
</head>
<body>

<nav>
    <a href="turnos.php">Turnos</a>
    <a href="pacientes.php">Pacientes</a>
    <a href="inicio.php">Inicio</a>
    <a href="calendario.php">Calendario</a>
    <a href="papelera.php">Papelera</a>
</nav>

<h1>Historial Médico de <?= htmlspecialchars($nombre_completo); ?></h1>

<h2>Agregar nuevo historial</h2>
<form action="historial.php?paciente_id=<?= $paciente_id ?>" method="post">
    <input type="hidden" name="paciente_id" value="<?= $paciente_id; ?>">
    <input type="hidden" name="medico_id" value="<?= $medico_id; ?>">
    <input type="date" name="fecha" id="fecha" required>  <!-- Campo de fecha autocompletado -->
    <textarea name="detalle" placeholder="Detalles del historial" required></textarea>
    <input type="submit" name="agregar_historial" value="Agregar Historial">
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
                <input type="submit" name="guardar_historial" value="Guardar">
                <input type="submit" name="borrar_historial" value="Borrar" onclick="return confirm('¿Estás seguro de que deseas borrar este historial?');">
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


</body>
</html>

<?php
$conn->close();
?>
