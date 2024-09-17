<?php
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

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el medico_id de la sesión
$medico_id = $_SESSION['medico_id'];

// Obtener el paciente de la URL (si viene de pacientes.php)
$paciente_id_selected = isset($_GET['paciente_id']) ? $_GET['paciente_id'] : '';
$nombre_paciente_selected = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$apellido_paciente_selected = isset($_GET['apellido']) ? $_GET['apellido'] : '';

// Variable para el mensaje de SweetAlert2
$mensaje = "";

// Agregar turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_turno'])) {
    $paciente_id = $_POST['paciente_id'];
    $fecha = $_POST['fecha'];
    $horario = $_POST['horario'];

    // Insertar turno solo para los pacientes del médico logueado
    $sql = "INSERT INTO turnos (paciente_id, fecha, horario) 
            SELECT p.paciente_id, '$fecha', '$horario' 
            FROM pacientes p 
            WHERE p.paciente_id = '$paciente_id' AND p.medico_id = '$medico_id'";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Nuevo turno añadido con éxito";
    } else {
        $mensaje = "Error al agregar el turno: " . $conn->error;
    }
}

// Editar turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $turno_id = $_POST['turno_id'];
    $fecha = $_POST['fecha'];
    $horario = $_POST['horario'];

    // Actualizar el turno solo si pertenece al médico logueado
    $sql = "UPDATE turnos t 
            JOIN pacientes p ON t.paciente_id = p.paciente_id 
            SET t.fecha='$fecha', t.horario='$horario' 
            WHERE t.turno_id='$turno_id' AND p.medico_id='$medico_id'";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Turno actualizado con éxito";
    } else {
        $mensaje = "Error al actualizar el turno: " . $conn->error;
    }
}

// Borrar turno
if (isset($_GET['borrar'])) {
    $turno_id = $_GET['borrar'];

    // Borrar solo si el turno pertenece a un paciente del médico logueado
    $sql = "DELETE t 
            FROM turnos t 
            JOIN pacientes p ON t.paciente_id = p.paciente_id 
            WHERE t.turno_id='$turno_id' AND p.medico_id='$medico_id'";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "Turno borrado con éxito";
    } else {
        $mensaje = "Error al borrar el turno: " . $conn->error;
    }
}

// Obtener pacientes del médico logueado
$pacientes = $conn->query("SELECT * FROM pacientes WHERE medico_id = '$medico_id'");

// Filtrar turnos por paciente, fecha y horario, solo para los pacientes del médico
$filter_query = "";
if (isset($_GET['buscar_paciente']) && !empty($_GET['buscar_paciente'])) {
    $busqueda_paciente = $_GET['buscar_paciente'];
    $filter_query .= " AND (p.nombre LIKE '%$busqueda_paciente%' OR p.apellido LIKE '%$busqueda_paciente%')";
}

if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
    $filter_query .= " AND t.fecha = '$fecha'";
}

if (isset($_GET['horario']) && !empty($_GET['horario'])) {
    $horario = $_GET['horario'];
    $filter_query .= " AND t.horario = '$horario'";
}

// Obtener turnos del médico con filtros aplicados
$sql = "SELECT t.turno_id, t.fecha, t.horario, p.nombre, p.apellido
        FROM turnos t
        JOIN pacientes p ON t.paciente_id = p.paciente_id
        WHERE p.medico_id = '$medico_id' $filter_query";

$turnos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos</title>
    <link rel="stylesheet" href="styles.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<nav>
    <a href="turnos.php">Turnos</a>
    <a href="pacientes.php">Pacientes</a>
    <a href="inicio.php">Inicio</a>
    <a href="calendario.php">Calendario</a>
    <a href="papelera.php">Papelera</a>
</nav>

<h1>Gestión de Turnos</h1>

<h2>Registrar Turno</h2>
<form action="turnos.php" method="post">
    <select name="paciente_id" required>
        <option value="">Seleccionar Paciente</option>
        <?php
        while($paciente = $pacientes->fetch_assoc()) {
            $selected = ($paciente['paciente_id'] == $paciente_id_selected) ? 'selected' : '';
            echo "<option value='{$paciente['paciente_id']}' $selected>{$paciente['nombre']} {$paciente['apellido']}</option>";
        }
        ?>
    </select>
    <input type="date" name="fecha" required>
    <input type="time" name="horario" required>
    <input type="submit" name="agregar_turno" value="Registrar Turno">
</form>

<h2>Filtrar Turnos</h2>
<form action="turnos.php" method="GET">
    <h3>Por Paciente</h3>
    <input type="text" name="buscar_paciente" placeholder="Buscar por nombre o apellido" value="<?= isset($_GET['buscar_paciente']) ? htmlspecialchars($_GET['buscar_paciente']) : '' ?>">
    
    <h3>Por Fecha y Horario</h3>
    <input type="date" name="fecha" placeholder="Fecha" value="<?= isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : '' ?>">
    <input type="time" name="horario" placeholder="Horario" value="<?= isset($_GET['horario']) ? htmlspecialchars($_GET['horario']) : '' ?>">
    
    <button type="submit">Buscar</button>
    <?php if (isset($_GET['buscar_paciente']) || isset($_GET['fecha']) || isset($_GET['horario'])): ?>
        <a href="turnos.php" class="button">Quitar Filtro</a>
    <?php endif; ?>
</form>

<h2>Turnos Registrados</h2>
<table>
    <tr>
        <th>Paciente</th>
        <th>Fecha</th>
        <th>Horario</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $turnos->fetch_assoc()): ?>
    <tr>
        <form action="turnos.php" method="post">
            <td><?= $row['nombre'] . " " . $row['apellido']; ?></td>
            <td><input type="date" name="fecha" value="<?= $row['fecha']; ?>"></td>
            <td><input type="time" name="horario" value="<?= $row['horario']; ?>"></td>
            <td>
                <input type="hidden" name="turno_id" value="<?= $row['turno_id']; ?>">
                <input type="submit" name="guardar" value="Guardar">
                <a href="turnos.php?borrar=<?= $row['turno_id']; ?>">Borrar</a>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>

<?php if ($turnos->num_rows == 0): ?>
    <p>No se encontraron turnos registrados.</p>
<?php endif; ?>

<!-- SweetAlert para notificaciones -->
<?php if ($mensaje): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '<?= $mensaje; ?>',
        showConfirmButton: false,
        timer: 2000
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
// Cerrar la conexión
$conn->close();
?>

