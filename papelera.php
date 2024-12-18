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

session_start();
// Obtener el medico_id de la sesión actual (esto debería estar configurado en tu sistema de autenticación)
if (!isset($_SESSION['medico_id'])) {
    // Si no está definida la sesión, redirigir al login
    header("Location: index.php");
    exit();
}
// Simular el medico_id de la sesión (aquí deberías obtener el medico_id de la sesión de tu sistema de autenticación)
$medico_id = $_SESSION['medico_id'];

// Consultar los datos del médico para mostrarlos en la sección "Cuenta"
$sql_medico = "SELECT nombre, email FROM medicos WHERE medico_id='$medico_id'";
$result_medico = $conn->query($sql_medico);
$medico = $result_medico->fetch_assoc();

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
    <link rel="stylesheet" href="Styles/StylePapelera.css">
    <title>Papelera de Pacientes</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>
<body>

<header>
    <nav class="nav">
        <div class="nav-scale">
        <a href="turnos.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'turnos.php') ? 'activo' : ''; ?>">Turnos</a>
        </div>
        <div class="nav-scale">
        <a href="pacientes.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'pacientes.php') ? 'activo' : ''; ?>">Pacientes</a>
        </div>

        <!-- Reemplazo de "Inicio" por imagen -->
         <div class="nav-scale">
        <a href="inicio.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'inicio.php') ? 'activo' : ''; ?>">
            <img src="icon.png" alt="Inicio">
        </a>
        </div>
        <div class="nav-scale">
        <a href="calendario.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'calendario.php') ? 'activo' : ''; ?>">Calendario</a>
        </div>
        
        <div class="dropdown">
            <div class="nav-scale">
            <a class="dropbtn">Cuenta</a>
            </div>
            <div class="dropdown-content">
                <p><strong>Nombre:</strong> <?= $medico['nombre']; ?></p>
                <p><strong>Email:</strong> <?= $medico['email']; ?></p>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
</header>

<h1>Papelera de Pacientes</h1>

<!-- Barra de búsqueda -->
<form method="GET" action="papelera.php">
    <input type="text" name="search" placeholder="Buscar por nombre o apellido" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
    <button class="buscar-btn" type="submit">Buscar</button>
    <a href="papelera.php"><button class="buscar-btn" type="button">Quitar Filtro</button></a>
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
                    <a class='update-btn' href='papelera.php?restore={$row['paciente_eliminado_id'] }'>Restaurar</a>
                   <a href='#' class='delete-btn' onclick='confirmDelete({$row['paciente_eliminado_id']})'>Eliminar definitivamente</a>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    var dropdown = document.querySelector('.dropdown');
    var dropbtn = document.querySelector('.dropbtn');

    // Agregar un evento de clic para mostrar/ocultar el menú
    dropbtn.addEventListener('click', function() {
        dropdown.classList.toggle('show'); // Alterna la clase 'show' para el menú
    });

    // Cerrar el menú si se hace clic fuera del dropdown
    window.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los formularios en la página
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                form.classList.add('active');
            });

            input.addEventListener('blur', () => {
                // Verificar si alguno de los inputs aún está enfocado
                const isFocused = Array.from(inputs).some(input => input === document.activeElement);
                if (!isFocused) {
                    form.classList.remove('active');
                }
            });
        });
    });
});
</script>


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

