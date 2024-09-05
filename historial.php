<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medex";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el historial de un paciente
if (isset($_GET['paciente_id'])) {
    $paciente_id = $_GET['paciente_id'];
    $historial = $conn->query("SELECT * FROM historial WHERE paciente_id = $paciente_id");
}

// Agregar nuevo historial
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_historial'])) {
    $paciente_id = $_POST['paciente_id'];
    $medico_id = $_POST['medico_id'];
    $fecha = $_POST['fecha'];
    $detalle = $_POST['detalle'];

    $sql = "INSERT INTO historial (paciente_id, medico_id, fecha, detalle) VALUES ('$paciente_id', '$medico_id', '$fecha', '$detalle')";

    if ($conn->query($sql) === TRUE) {
        echo "<dialog id='modal' open>
                <p>Historial añadido con éxito</p>
              </dialog>
              <script>
                const modal = document.getElementById('modal');
                setTimeout(() => {
                  modal.close();
                }, 2000);
              </script>";
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
        echo "<dialog id='modal' open>
                <p>Historial actualizado con éxito</p>
              </dialog>
              <script>
                const modal = document.getElementById('modal');
                setTimeout(() => {
                  modal.close();
                }, 2000);
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
</head>
<body>

<h1>Historial Médico</h1>

<h2>Agregar nuevo historial</h2>
<form action="historial.php" method="post">
    <input type="hidden" name="paciente_id" value="<?= $paciente_id; ?>">
    <input type="hidden" name="medico_id" value="<?= $medico_id; ?>">
    <input type="date" name="fecha" required>
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
        <form action="historial.php" method="post">
            <td><?= $row['fecha']; ?></td>
            <td><textarea name="detalle"><?= $row['detalle']; ?></textarea></td>
            <td>
                <input type="hidden" name="historial_id" value="<?= $row['historial_id']; ?>">
                <input type="submit" name="guardar_historial" value="Guardar">
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
