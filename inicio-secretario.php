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
    <title>MedEx</title>
</head>
<body>
<header>
    <nav class="nav">
        <a href="turnos.php">Turnos</a>
        <a href="pacientes-secretario.php">Pacientes</a>
        <a href="#">Inicio</a>
        <a href="calendario-secretario.php">Calendario</a>
        <a href="papelera.php">Papelera</a>
    </nav>
</header>


<div class="container">
    <div class="MedEx">
        <h1>MedEx</h1>
    </div>



<a href="logout.php">Cerrar Sesión</a>

<footer>
    <p>&copy; 2024 MedEx - Todos los derechos reservados</p>
</footer>

</body>
</html>

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
    $conn->query($sql_vaciar);
}

$conn->close();
?>
