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

// Mover paciente a la sección "Atendidos"
if (isset($_GET['atender'])) {
    $turno_id = $_GET['atender'];

    // Obtener los datos del turno a mover
    $sql = "SELECT t.*, p.nombre, p.apellido, p.dni 
            FROM turnos t
            JOIN pacientes p ON t.paciente_id = p.paciente_id 
            WHERE t.turno_id = $turno_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row) {
        // Insertar en la tabla "atendidos"
        $sql_insert = "INSERT INTO atendidos (pacientes_id, medico_id, fecha_atencion, nombre, apellido, dni, motivo)
                       VALUES ('{$row['paciente_id']}', '{$_SESSION['medico_id']}', NOW(), '{$row['nombre']}', '{$row['apellido']}', '{$row['dni']}', '')";

        if ($conn->query($sql_insert) === TRUE) {
            // Eliminar el turno de la tabla "turnos"
            $sql_delete = "DELETE FROM turnos WHERE turno_id=$turno_id";
            $conn->query($sql_delete);
        }
    }
}

// Obtener el próximo paciente (turno más cercano)
$sql_proximo = "SELECT t.*, p.nombre, p.apellido, p.dni 
                FROM turnos t
                JOIN pacientes p ON t.paciente_id = p.paciente_id 
                ORDER BY t.fecha ASC, t.horario ASC LIMIT 1";
$result_proximo = $conn->query($sql_proximo);
$proximo_paciente = $result_proximo->fetch_assoc();

// Obtener los pacientes pendientes
$sql_pendientes = "SELECT t.*, p.nombre, p.apellido, p.dni 
                   FROM turnos t
                   JOIN pacientes p ON t.paciente_id = p.paciente_id 
                   ORDER BY t.fecha ASC, t.horario ASC";
$pendientes = $conn->query($sql_pendientes);

// Obtener los pacientes atendidos
$sql_atendidos = "SELECT * FROM atendidos ORDER BY fecha_atencion DESC";
$atendidos = $conn->query($sql_atendidos);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>MedEx</title>
</head>
<body>

<nav>
    <a href="turnos.php">Turnos</a>
    <a href="pacientes.php">Pacientes</a>
    <a href="#">Inicio</a>
    <a href="calendario.php">Calendario</a>
    <a href="#">Buscar</a>
</nav>

<div class="container">
    <div class="proximo-paciente">
        <h2>Próximo Paciente</h2>
        <?php if ($proximo_paciente): ?>
            <div>
                <span><?= $proximo_paciente['horario'] ?></span>
                <span><?= $proximo_paciente['nombre'] ?> <?= $proximo_paciente['apellido'] ?></span>
                <span><?= $proximo_paciente['dni'] ?></span>
            </div>
        <?php else: ?>
            <p>No hay pacientes próximos.</p>
        <?php endif; ?>
    </div>

    <div class="columns">
        <div class="column pendientes">
            <h3>Pendientes</h3>
            <?php while ($row = $pendientes->fetch_assoc()): ?>
                <div>
                    <span><?= $row['fecha'] ?> <?= $row['horario'] ?></span>
                    <span><?= $row['nombre'] ?> <?= $row['apellido'] ?></span>
                    <span><?= $row['dni'] ?></span>
                    <a href="?atender=<?= $row['turno_id'] ?>">Atender</a>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="column atendidos">
            <h3>Atendidos</h3>
            <?php while ($row = $atendidos->fetch_assoc()): ?>
                <div>
                    <span><?= $row['nombre'] ?> <?= $row['apellido'] ?></span>
                    <span><?= $row['dni'] ?></span>
                    <span><?= $row['fecha_atencion'] ?></span>
                    <a href="?delete_atendido=<?= $row['atendido_id'] ?>">Borrar</a>
                </div>
            <?php endwhile; ?>
            <a href="?vaciar_atendidos=true">Vaciar Atendidos</a>
        </div>
    </div>
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
    $sql_delete = "DELETE FROM atendidos WHERE atendido_id=$atendido_id";
    $conn->query($sql_delete);
}

// Vaciar todos los pacientes atendidos
if (isset($_GET['vaciar_atendidos'])) {
    $sql_vaciar = "DELETE FROM atendidos";
    $conn->query($sql_vaciar);
}

$conn->close();
?>

