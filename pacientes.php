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

// Simular el medico_id de la sesión (aquí deberías obtener el medico_id de la sesión de tu sistema de autenticación)
session_start();
$medico_id = $_SESSION['medico_id'];

// Añadir nuevo paciente
if (isset($_POST['add'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $edad = $_POST['edad'];
    $dni = $_POST['dni'];
    $mutual = $_POST['mutual'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // Insertar el nuevo paciente con el medico_id correspondiente
    $sql = "INSERT INTO pacientes (medico_id, nombre, apellido, edad, dni, mutual, email, telefono)
            VALUES ('$medico_id', '$nombre', '$apellido', '$edad', '$dni', '$mutual', '$email', '$telefono')";

    if ($conn->query($sql) === TRUE) {
        header("Location: pacientes.php?mensaje=añadido");
        exit(); // Para detener la ejecución del script
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

    // Asegurarse de que el paciente pertenece al médico antes de actualizar
    $sql = "UPDATE pacientes SET nombre='$nombre', apellido='$apellido', edad='$edad', dni='$dni', mutual='$mutual', email='$email', telefono='$telefono'
            WHERE paciente_id=$paciente_id AND medico_id='$medico_id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: pacientes.php?mensaje=actualizado");
        exit(); // Para detener la ejecución del script
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Borrar paciente (moverlo a la tabla "pacientes_eliminados")
if (isset($_GET['delete'])) {
    $paciente_id = $_GET['delete'];

    // Obtener los datos del paciente a eliminar
    $sql = "SELECT * FROM pacientes WHERE paciente_id=$paciente_id AND medico_id='$medico_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row) {
        // Mover el historial del paciente a la tabla "pacientes_eliminados_historial"
        $sql_historial = "SELECT * FROM historial WHERE paciente_id=$paciente_id";
        $result_historial = $conn->query($sql_historial);

        if ($result_historial->num_rows > 0) {
            while ($row_historial = $result_historial->fetch_assoc()) {
                $sql_move_historial = "INSERT INTO pacientes_eliminados_historial (historial_id, paciente_id, medico_id, detalle, fecha)
                                       VALUES ('{$row_historial['historial_id']}', '{$row_historial['paciente_id']}', '{$row_historial['medico_id']}', '{$row_historial['detalle']}', '{$row_historial['fecha']}')";
                $conn->query($sql_move_historial);
            }
        }

        // Insertar los datos en la tabla "pacientes_eliminados"
        $sql_insert = "INSERT INTO pacientes_eliminados (paciente_id, medico_id, nombre, apellido, edad, dni, mutual, email, telefono)
                       VALUES ('{$row['paciente_id']}', '{$row['medico_id']}', '{$row['nombre']}', '{$row['apellido']}', '{$row['edad']}', '{$row['dni']}', '{$row['mutual']}', '{$row['email']}', '{$row['telefono']}')";

        if ($conn->query($sql_insert) === TRUE) {
            // Eliminar el historial del paciente de la tabla "historial"
            $sql_delete_historial = "DELETE FROM historial WHERE paciente_id=$paciente_id";
            $conn->query($sql_delete_historial);

            // Eliminar el paciente de la tabla "pacientes"
            $sql_delete = "DELETE FROM pacientes WHERE paciente_id=$paciente_id AND medico_id='$medico_id'";
            if ($conn->query($sql_delete) === TRUE) {
                header("Location: pacientes.php?mensaje=borrado");
                exit(); // Para detener la ejecución del script
            } else {
                echo "Error al eliminar el paciente: " . $conn->error;
            }
        } else {
            echo "Error al mover el paciente a la papelera: " . $conn->error;
        }
    }
}

// Capturar el parámetro 'mensaje' de la URL para mostrar el modal correspondiente
$mensaje = '';
if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'añadido':
            $mensaje = "Nuevo paciente añadido con éxito";
            break;
        case 'actualizado':
            $mensaje = "Paciente actualizado con éxito";
            break;
        case 'borrado':
            $mensaje = "Paciente movido a la papelera con éxito";
            break;
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

<h1>Gestión de Pacientes</h1>

<!-- Formulario para añadir pacientes -->
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

<!-- Barra de búsqueda -->
<form method="GET" action="pacientes.php">
    <input type="text" name="search" placeholder="Buscar por nombre o apellido" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <button type="submit">Buscar</button>
    
    <!-- Botón "Quitar Filtro" como un enlace que actúa como botón -->
    <a href="pacientes.php" style="text-decoration: none;">
        <button type="button">Quitar Filtro</button>
    </a>
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
        <th>Historial</th>
        <th>Acciones</th>
    </tr>
    <?php
    $search_query = "";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $search_query = "AND (nombre LIKE '%$search%' OR apellido LIKE '%$search%' OR dni LIKE '%$search%')";
    }

    // Obtener los pacientes del médico que ha iniciado sesión
    $sql = "SELECT * FROM pacientes WHERE medico_id='$medico_id' $search_query";
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
                    <td><a href='turnos.php?paciente_id=$paciente_id'>Agendar Turno</a></td>
                    <td><a href='historial.php?paciente_id=$paciente_id'>Ver Historial</a></td>
                    <td>
                        <button type='submit' name='update'>Actualizar</button>
                        <a href='pacientes.php?delete=$paciente_id' class='button'>Eliminar</a>
                    </td>
                </form>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='10'>No se encontraron pacientes</td></tr>";
    }
    ?>
</table>

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

<?php $conn->close(); ?>

