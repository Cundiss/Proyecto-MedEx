<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "medex";
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Iniciar sesión para acceder a medico_id
session_start();
$medico_id = $_SESSION['medico_id'];

// Variable para almacenar mensajes
$mensaje = '';

// Restaurar paciente junto con su historial
if (isset($_GET['restore'])) {
    $paciente_eliminado_id = $_GET['restore'];

    // Obtener los datos del paciente eliminado
    $sql = "SELECT * FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id AND medico_id='$medico_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $apellido = $row ? $row['apellido'] : '';

    if ($row) {
        // Restaurar el paciente a la tabla "pacientes"
        $sql_restore = "INSERT INTO pacientes (medico_id, nombre, apellido, edad, dni, mutual, email, telefono)
                        VALUES ('{$row['medico_id']}', '{$row['nombre']}', '{$row['apellido']}', '{$row['edad']}', '{$row['dni']}', '{$row['mutual']}', '{$row['email']}', '{$row['telefono']}')";

        if ($conn->query($sql_restore) === TRUE) {
            // Obtener el paciente_id recién restaurado
            $nuevo_paciente_id = $conn->insert_id;

            // Mover el historial de "pacientes_eliminados_historial" a "historial"
            $sql_historial = "INSERT INTO historial (paciente_id, medico_id, fecha, detalle)
                              SELECT '$nuevo_paciente_id', medico_id, fecha, detalle
                              FROM pacientes_eliminados_historial
                              WHERE paciente_id={$row['paciente_id']}";

            if ($conn->query($sql_historial) === TRUE) {
                // Eliminar el historial de la tabla "pacientes_eliminados_historial"
                $sql_borrar_historial = "DELETE FROM pacientes_eliminados_historial WHERE paciente_id={$row['paciente_id']}";
                $conn->query($sql_borrar_historial);

                // Eliminar el paciente de la tabla "pacientes_eliminados"
                $sql_delete = "DELETE FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id AND medico_id='$medico_id'";
                $conn->query($sql_delete);

                $mensaje = "Paciente {$apellido} y su historial restaurados con éxito";
            } else {
                echo "Error al restaurar el historial: " . $conn->error;
            }
        } else {
            echo "Error al restaurar el paciente: " . $conn->error;
        }
    }
}

// Eliminar definitivamente el paciente junto con su historial
if (isset($_GET['delete'])) {
    $paciente_eliminado_id = $_GET['delete'];

    // Obtener el apellido del paciente antes de eliminar
    $sql = "SELECT apellido, paciente_id FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id AND medico_id='$medico_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $apellido = $row ? $row['apellido'] : '';
    $paciente_id = $row['paciente_id'];

    if ($row) {
        // Eliminar el historial del paciente de "pacientes_eliminados_historial"
        $sql_borrar_historial = "DELETE FROM pacientes_eliminados_historial WHERE paciente_id=$paciente_id";
        $conn->query($sql_borrar_historial);

        // Eliminar el paciente de "pacientes_eliminados"
        $sql_delete = "DELETE FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id AND medico_id='$medico_id'";
        if ($conn->query($sql_delete) === TRUE) {
            $mensaje = "Paciente {$apellido} y su historial eliminados definitivamente";
        } else {
            echo "Error al eliminar definitivamente el paciente: " . $conn->error;
        }
    }
}

// Barra de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Si hay un criterio de búsqueda, modifica la consulta para incluir el medico_id
if ($search) {
    $sql = "SELECT * FROM pacientes_eliminados WHERE medico_id='$medico_id' AND (nombre LIKE '%$search%' OR apellido LIKE '%$search%')";
} else {
    $sql = "SELECT * FROM pacientes_eliminados WHERE medico_id='$medico_id'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Papelera de Pacientes</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>
<body>

<nav>
    <a href="turnos.php">Turnos</a>
    <a href="pacientes.php">Pacientes</a>
    <a href="inicio.php">Inicio</a>
    <a href="calendario.php">Calendario</a>
    <a href="papelera.php">Papelera</a>
</nav>

<h1>Papelera de Pacientes</h1>

<!-- Barra de búsqueda -->
<form method="GET" action="papelera.php">
    <input type="text" name="search" placeholder="Buscar por nombre o apellido" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <button type="submit">Buscar</button>
    <a href="papelera.php"><button type="button">Quitar Filtro</button></a>
</form>

<h3>Pacientes Eliminados</h3>
<table border="1">
    <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Edad</th>
        <th>DNI</th>
        <th>Mutual</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Acciones</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['nombre']}</td>
                <td>{$row['apellido']}</td>
                <td>{$row['edad']}</td>
                <td>{$row['dni']}</td>
                <td>{$row['mutual']}</td>
                <td>{$row['email']}</td>
                <td>{$row['telefono']}</td>
                <td>
                    <a href='papelera.php?restore={$row['paciente_eliminado_id']}'>Restaurar</a>
                    <a href='#' onclick='confirmDelete({$row['paciente_eliminado_id']})' style='color: red;'>Eliminar definitivamente</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No hay pacientes eliminados</td></tr>";
    }
    ?>
</table>

<script>
// Confirmar antes de eliminar definitivamente
function confirmDelete(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'papelera.php?delete=' + id;
        }
    })
}

// Mostrar el mensaje de éxito con SweetAlert2
<?php if ($mensaje): ?>
Swal.fire({
    icon: 'success',
    title: '<?= $mensaje; ?>',
    showConfirmButton: false,
    timer: 2000
});
<?php endif; ?>
</script>

</body>
</html>

<?php $conn->close(); ?>

