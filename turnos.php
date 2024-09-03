<?php
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

// Agregar turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_turno'])) {
    $paciente_id = $_POST['paciente_id'];
    $fecha = $_POST['fecha'];
    $horario = $_POST['horario'];

    $sql = "INSERT INTO turnos (paciente_id, fecha, horario) VALUES ('$paciente_id', '$fecha', '$horario')";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo turno añadido con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Editar turno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $turno_id = $_POST['turno_id'];
    $fecha = $_POST['fecha'];
    $horario = $_POST['horario'];

    $sql = "UPDATE turnos SET fecha='$fecha', horario='$horario' WHERE turno_id='$turno_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Turno actualizado con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Borrar turno
if (isset($_GET['borrar'])) {
    $turno_id = $_GET['borrar'];

    $sql = "DELETE FROM turnos WHERE turno_id='$turno_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Turno borrado con éxito";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Obtener pacientes y turnos
$pacientes = $conn->query("SELECT * FROM pacientes");
$turnos = $conn->query("SELECT t.turno_id, t.fecha, t.horario, p.nombre, p.apellido FROM turnos t JOIN pacientes p ON t.paciente_id = p.paciente_id");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav>
        <a href="turnos.php">Turnos</a>
        <a href="pacientes.php">Pacientes</a>
        <a href="inicio.php">Inicio</a>
        <a href="calendario.php">Calendario</a>
        <a href="#">Buscar</a>
    </nav>

    <h1>Gestión de Turnos</h1>

    <h2>Registrar Turno</h2>
    <form action="turnos.php" method="post">
        <select name="paciente_id" required>
            <option value="">Seleccionar Paciente</option>
            <?php while($paciente = $pacientes->fetch_assoc()): ?>
                <option value="<?= $paciente['paciente_id']; ?>"><?= $paciente['nombre'] . " " . $paciente['apellido']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="date" name="fecha" required>
        <input type="time" name="horario" required>
        <input type="submit" name="agregar_turno" value="Registrar Turno">
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
</body>
</html>

<?php
$conn->close();
?>
