<?php
// Conexión a la base de datos
require_once 'config.php';

// Verificar la sesión del médico
session_start();
if (!isset($_SESSION['medico_id'])) {
    header("Location: index.php");
    exit();
}

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

    $sql = "INSERT INTO pacientes (medico_id, nombre, apellido, edad, dni, mutual, email, telefono)
            VALUES ('$medico_id', '$nombre', '$apellido', '$edad', '$dni', '$mutual', '$email', '$telefono')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: pacientes-secretario.php?mensaje=añadido");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Editar paciente con múltiples médicos
if (isset($_POST['update'])) {
    $paciente_id = $_POST['paciente_id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $edad = $_POST['edad'];
    $dni = $_POST['dni'];
    $mutual = $_POST['mutual'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $medicos_seleccionados = explode(',', $_POST['medicos_seleccionados']); // Array de médicos seleccionados

    // Actualizar los datos del paciente
    $sql = "UPDATE pacientes SET nombre='$nombre', apellido='$apellido', edad='$edad', dni='$dni', mutual='$mutual', email='$email', telefono='$telefono' WHERE paciente_id='$paciente_id'";
    $conn->query($sql);

    // Borrar las asignaciones actuales y volver a asignar los médicos seleccionados
    $conn->query("DELETE FROM paciente_medico WHERE paciente_id='$paciente_id'");
    foreach ($medicos_seleccionados as $medico_id) {
        $conn->query("INSERT INTO paciente_medico (paciente_id, medico_id) VALUES ('$paciente_id', '$medico_id')");
    }

    header("Location: pacientes-secretario.php?mensaje=actualizado");
    exit();
}

// Borrar paciente (mover a la tabla "pacientes_eliminados")
if (isset($_GET['delete'])) {
    $paciente_id = $_GET['delete'];

    // Obtener los datos del paciente a eliminar
    $sql = "SELECT * FROM pacientes WHERE paciente_id=$paciente_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row) {
        // Insertar los datos en la tabla "pacientes_eliminados"
        $sql_insert = "INSERT INTO pacientes_eliminados (paciente_id, medico_id, nombre, apellido, edad, dni, mutual, email, telefono)
                       VALUES ('{$row['paciente_id']}', '{$row['medico_id']}', '{$row['nombre']}', '{$row['apellido']}', '{$row['edad']}', '{$row['dni']}', '{$row['mutual']}', '{$row['email']}', '{$row['telefono']}')";

        if ($conn->query($sql_insert) === TRUE) {
            // Eliminar el paciente de la tabla "pacientes"
            $sql_delete = "DELETE FROM pacientes WHERE paciente_id=$paciente_id";
            if ($conn->query($sql_delete) === TRUE) {
                header("Location: pacientes-secretario.php?mensaje=borrado");
                exit();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<nav>
    <a href="turnos.php">Turnos</a>
    <a href="pacientes-secretario.php">Pacientes</a>
    <a href="inicio-secretario.php">Inicio</a>
    <a href="calendario.php">Calendario</a>
    <a href="papelera.php">Papelera</a>
</nav>

<h1>Gestión de Pacientes</h1>

<!-- Formulario para añadir pacientes -->
<form action="pacientes-secretario.php" method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="text" name="apellido" placeholder="Apellido" required>
    <input type="number" name="edad" placeholder="Edad" required>
    <input type="text" name="dni" placeholder="DNI" required>
    <input type="text" name="mutual" placeholder="Mutual" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="telefono" placeholder="Teléfono" required>
    <select name="medico_id" required>
        <option value="">Seleccione Médico</option>
        <?php
        // Obtener la lista de médicos para la selección
        $sql_medicos = "SELECT medico_id, nombre FROM medicos";
        $result_medicos = $conn->query($sql_medicos);
        while ($medico = $result_medicos->fetch_assoc()) {
            echo "<option value='{$medico['medico_id']}'>{$medico['nombre']}</option>";
        }
        ?>
    </select>
    <button type="submit" name="add">Añadir Paciente</button>
</form>

<!-- Formulario de búsqueda -->
<form action="pacientes-secretario.php" method="GET">
    <input type="text" name="search" placeholder="Buscar por nombre, apellido o DNI" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <button type="submit">Buscar</button>
    <?php if (isset($_GET['search'])): ?>
        <a href="pacientes-secretario.php" class="button">Quitar Filtro</a>
    <?php endif; ?>
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
        <th>Asignar Médicos</th>
        <th>Acciones</th>
    </tr>
    <?php
    $search_query = "";
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $search_query = "AND (nombre LIKE '%$search%' OR apellido LIKE '%$search%' OR dni LIKE '%$search%')";
    }

    $sql = "SELECT * FROM pacientes WHERE 1 $search_query";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $paciente_id = $row['paciente_id'];
            echo "<tr>
                <form action='pacientes-secretario.php' method='POST'>
                    <input type='hidden' name='paciente_id' value='{$row['paciente_id']}'>
                    <td><input type='text' name='nombre' value='{$row['nombre']}'></td>
                    <td><input type='text' name='apellido' value='{$row['apellido']}'></td>
                    <td><input type='number' name='edad' value='{$row['edad']}'></td>
                    <td><input type='text' name='dni' value='{$row['dni']}'></td>
                    <td><input type='text' name='mutual' value='{$row['mutual']}'></td>
                    <td><input type='email' name='email' value='{$row['email']}'></td>
                    <td><input type='text' name='telefono' value='{$row['telefono']}'></td>
                    <!-- Botón para asignar múltiples médicos -->
                    <td><button type='button' class='asignar-medicos' data-paciente-id='$paciente_id'>Asignar Médicos</button></td>
                    <td>
                        <button type='submit' name='update'>Actualizar</button>
                        <a href='pacientes-secretario.php?delete={$row['paciente_id']}' onclick='return confirm(\"¿Estás seguro de eliminar este paciente?\")'>Borrar</a>
                    </td>
                </form>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='9'>No se encontraron pacientes</td></tr>";
    }
    ?>
</table>

<!-- Script para asignar médicos -->
<script>
document.querySelectorAll('.asignar-medicos').forEach(button => {
    button.addEventListener('click', function() {
        const pacienteId = this.getAttribute('data-paciente-id');

        Swal.fire({
            title: 'Asignar Médicos',
            html: `
                <select id="medicos-select" multiple>
                    <?php
                    $sql_medicos = "SELECT medico_id, nombre FROM medicos";
                    $result_medicos = $conn->query($sql_medicos);
                    while ($medico = $result_medicos->fetch_assoc()) {
                        echo "<option value='{$medico['medico_id']}'>{$medico['nombre']}</option>";
                    }
                    ?>
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: 'Asignar',
            preConfirm: () => {
                const medicosSeleccionados = Array.from(document.getElementById('medicos-select').selectedOptions).map(option => option.value);
                return medicosSeleccionados.join(',');
            }
        }).then(result => {
            if (result.isConfirmed) {
                const form = new FormData();
                form.append('paciente_id', pacienteId);
                form.append('medicos_seleccionados', result.value);

                fetch('pacientes-secretario.php', {
                    method: 'POST',
                    body: form
                }).then(() => {
                    Swal.fire('Asignado!', 'Los médicos han sido asignados.', 'success').then(() => {
                        window.location.reload();
                    });
                });
            }
        });
    });
});
</script>

<?php if (!empty($mensaje)): ?>
<script>
    Swal.fire({
        title: '<?php echo $mensaje; ?>',
        icon: 'success',
        confirmButtonText: 'Aceptar'
    });
</script>
<?php endif; ?>

</body>
</html>
