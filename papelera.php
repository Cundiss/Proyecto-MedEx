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

// Restaurar paciente
if (isset($_GET['restore'])) {
    $paciente_eliminado_id = $_GET['restore'];

    // Obtener los datos del paciente eliminado
    $sql = "SELECT * FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $apellido = $row ? $row['apellido'] : '';

    if ($row) {
        // Insertar los datos en la tabla "pacientes"
        $sql_restore = "INSERT INTO pacientes (medico_id, nombre, apellido, edad, dni, mutual, email, telefono)
                        VALUES ('{$row['medico_id']}', '{$row['nombre']}', '{$row['apellido']}', '{$row['edad']}', '{$row['dni']}', '{$row['mutual']}', '{$row['email']}', '{$row['telefono']}')";

        if ($conn->query($sql_restore) === TRUE) {
            // Eliminar el registro de la tabla "pacientes_eliminados"
            $sql_delete = "DELETE FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id";
            $conn->query($sql_delete);
            echo "<dialog id='modal' open>
                    <p>Paciente {$apellido} restaurado</p>
                  </dialog>
                  <script>
                    const modal = document.getElementById('modal');
                    setTimeout(() => {
                      modal.close();
                    }, 2000);
                  </script>";
        } else {
            echo "Error al restaurar el paciente: " . $conn->error;
        }
    }
}

// Borrar paciente definitivamente
if (isset($_GET['delete'])) {
    $paciente_eliminado_id = $_GET['delete'];

    // Obtener el apellido del paciente antes de eliminar
    $sql = "SELECT apellido FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $apellido = $row ? $row['apellido'] : '';

    $sql_delete = "DELETE FROM pacientes_eliminados WHERE paciente_eliminado_id=$paciente_eliminado_id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<dialog id='modal' open>
                <p>Paciente {$apellido} eliminado definitivamente</p>
              </dialog>
              <script>
                const modal = document.getElementById('modal');
                setTimeout(() => {
                  modal.close();
                }, 2000);
              </script>";
    } else {
        echo "Error al eliminar definitivamente el paciente: " . $conn->error;
    }
}

// Barra de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Si hay un criterio de búsqueda, modifica la consulta
if ($search) {
    $sql = "SELECT * FROM pacientes_eliminados WHERE nombre LIKE '%$search%' OR apellido LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM pacientes_eliminados";
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
                    <a href='papelera.php?delete={$row['paciente_eliminado_id']}' style='color: red;'>Eliminar definitivamente</a>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No hay pacientes eliminados</td></tr>";
    }
    ?>
</table>

</body>
</html>
