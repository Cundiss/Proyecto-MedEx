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

// Añadir nuevo paciente
if (isset($_POST['add'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $edad = $_POST['edad'];
    $dni = $_POST['dni'];
    $mutual = $_POST['mutual'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    $sql = "INSERT INTO pacientes (nombre, apellido, edad, dni, mutual, email, telefono)
            VALUES ('$nombre', '$apellido', '$edad', '$dni', '$mutual', '$email', '$telefono')";

    if ($conn->query($sql) === TRUE) {
        echo "<dialog id='modal' open>
                <p>Nuevo paciente añadido</p>
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

// Editar paciente
if (isset($_POST['update'])) {
    $paciente_id = $_POST['paciente_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $edad = $_POST['edad'];
    $dni = $_POST['dni'];
    $mutual = $_POST['mutual'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    $sql = "UPDATE pacientes SET nombre='$nombre', apellido='$apellido', edad='$edad', dni='$dni', mutual='$mutual', email='$email', telefono='$telefono' WHERE paciente_id=$paciente_id";

    if ($conn->query($sql) === TRUE) {
        echo "<dialog id='modal' open>
                <p>Paciente $apellido actualizado</p>
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

// Borrar paciente
// Mover paciente a la tabla "pacientes_eliminados"
if (isset($_GET['delete'])) {
    $paciente_id = $_GET['delete'];

    // Obtener los datos del paciente a eliminar
    $sql = "SELECT * FROM pacientes WHERE paciente_id=$paciente_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $apellido = $row ? $row['apellido'] : '';

    if ($row) {
        // Insertar los datos en la tabla "pacientes_eliminados"
        $sql_insert = "INSERT INTO pacientes_eliminados (paciente_id, medico_id, nombre, apellido, edad, dni, mutual, email, telefono)
                       VALUES ('{$row['paciente_id']}', '{$row['medico_id']}', '{$row['nombre']}', '{$row['apellido']}', '{$row['edad']}', '{$row['dni']}', '{$row['mutual']}', '{$row['email']}', '{$row['telefono']}')";
        
        if ($conn->query($sql_insert) === TRUE) {
            // Eliminar el paciente de la tabla "pacientes"
            $sql_delete = "DELETE FROM pacientes WHERE paciente_id=$paciente_id";
            if ($conn->query($sql_delete) === TRUE) {
                echo "<dialog id='modal' open>
                        <p>Paciente {$apellido} movido a la papelera</p>
                      </dialog>
                      <script>
                        const modal = document.getElementById('modal');
                        setTimeout(() => {
                          modal.close();
                        }, 2000);
                      </script>";
            } else {
                echo "Error al eliminar el paciente: " . $conn->error;
            }
        } else {
            echo "Error al mover el paciente a la papelera: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Gestión de Pacientes</title>
</head>
<body>

<nav>
    <a href="turnos.php">Turnos</a>
    <a href="pacientes.php">Pacientes</a>
    <a href="inicio.php">Inicio</a>
    <a href="calendario.php">Calendario</a>
    <a href="papelera.php">Papelera</a>
</nav>

<h1>Gestión de Pacientes</h1>

<!-- Formulario de búsqueda -->
<form action="pacientes.php" method="GET">
    <input type="text" name="search" placeholder="Buscar por nombre, apellido o DNI" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <button type="submit">Buscar</button>
    <?php if (isset($_GET['search'])): ?>
        <a href="pacientes.php" class="button">Quitar Filtro</a>
    <?php endif; ?>
</form>

<form action="pacientes.php" method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellido" required>
    <input type="number" name="edad" placeholder="Edad" required>
    <input type="text" name="dni" placeholder="DNI" required>
    <input type="text" name="mutual" placeholder="Mutual" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="telefono" placeholder="Teléfono" required>
    <button type="submit" name="add">Añadir Paciente</button>
</form>

<h3>Pacientes Registrados</h3>
<table border="1">
    <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Edad</th>
        <th>DNI</th>
        <th>Mutual</th>
        <th>Email</th>
        <th>Teléfono</th>
        <th>Agendar Turno</th>
        <th>Acciones</th>
    </tr>
    <?php
    $search_query = "";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $search_query = "WHERE nombre LIKE '%$search%' OR apellido LIKE '%$search%' OR dni LIKE '%$search%'";
    }

    // Obtener los pacientes de la base de datos
    $sql = "SELECT * FROM pacientes $search_query";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $paciente_id = $row['paciente_id'];
            $nombre_completo = $row['nombre'] . ' ' . $row['apellido'];
            echo "<tr>
                <form action='pacientes.php' method='POST'>
                    <input type='hidden' name='paciente_id' value='{$row['paciente_id']}'>
                    <td><input type='text' name='nombre' value='{$row['nombre']}'></td>
                    <td><input type='text' name='apellido' value='{$row['apellido']}'></td>
                    <td><input type='number' name='edad' value='{$row['edad']}'></td>
                    <td><input type='text' name='dni' value='{$row['dni']}'></td>
                    <td><input type='text' name='mutual' value='{$row['mutual']}'></td>
                    <td><input type='email' name='email' value='{$row['email']}'></td>
                    <td><input type='text' name='telefono' value='{$row['telefono']}'></td>
                    <td><a href='turnos.php?paciente_id={$paciente_id}&nombre={$row['nombre']}&apellido={$row['apellido']}'>Agendar Turno</a></td>
                    <td>
                        <button type='submit' name='update'>Guardar</button>
                        <a href='pacientes.php?delete={$row['paciente_id']}' style='text-decoration: none; color: black;'>Borrar</a>
                    </td>
                </form>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No hay pacientes registrados</td></tr>";
    }
    ?>
</table>
</body>
</html>

