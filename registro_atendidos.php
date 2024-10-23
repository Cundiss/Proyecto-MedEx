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

// Obtener datos del médico logueado
$sql_medico = "SELECT nombre, email FROM medicos WHERE medico_id = $medico_id";
$result_medico = $conn->query($sql_medico);
$medico = $result_medico->fetch_assoc();

// Buscar pacientes atendidos por nombre o apellido
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Añadir el campo `aplazado` a la consulta
$sql_atendidos = "SELECT * FROM atendidos 
                  WHERE medico_id = $medico_id 
                  AND (nombre LIKE '%$search%' OR apellido LIKE '%$search%' OR dni LIKE '%$search%')
                  ORDER BY fecha_atencion DESC";
$atendidos = $conn->query($sql_atendidos);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/StylePapelera.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Registro de Atendidos</title>
</head>
<body>
<header>
    <nav class="nav">
        <a href="turnos.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'turnos.php') ? 'activo' : ''; ?>">Turnos</a>
        <a href="pacientes.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'pacientes.php') ? 'activo' : ''; ?>">Pacientes</a>

        <!-- Reemplazo de "Inicio" por imagen -->
        <a href="inicio.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'inicio.php') ? 'activo' : ''; ?>">
            <img src="icon.png" alt="Inicio">
        </a>

        <a href="calendario.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'calendario.php') ? 'activo' : ''; ?>">Calendario</a>
        
        <div class="dropdown">
            <a class="dropbtn">Cuenta</a>
            <div class="dropdown-content">
                <p><strong>Nombre:</strong> <?= $medico['nombre']; ?></p>
                <p><strong>Email:</strong> <?= $medico['email']; ?></p>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
</header>

<div class="container">
    <h2>Pacientes Atendidos</h2>

    <!-- Buscador de pacientes -->
    <form method="get" action="registro_atendidos.php">
        <input type="text" name="search" placeholder="Buscar por nombre o apellido" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="buscar-btn">Buscar</button>
        <a href="registro_atendidos.php"><button class="buscar-btn" type="button">Quitar Filtro</button></a>
    </form>

    <!-- Tabla de pacientes atendidos -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>DNI</th>
                <th>Fecha de Atención</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $atendidos->fetch_assoc()): ?>
                <!-- Agregar clase 'aplazado' si el paciente está aplazado -->
                <tr class="<?= $row['aplazado'] == 1 ? 'aplazado' : '' ?>">
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['apellido'] ?></td>
                    <td><?= $row['dni'] ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($row['fecha_atencion'])) ?></td>
                    <td>
                        <a href="?delete_atendido=<?= $row['atendido_id'] ?>" class='delete-btn'>Borrar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

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
// SweetAlert2 para confirmar eliminación
document.querySelectorAll('.delete-btn').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const url = this.getAttribute('href');
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Este paciente será eliminado de los registros atendidos',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, borrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
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


<?php
// Borrar un paciente atendido individualmente
if (isset($_GET['delete_atendido'])) {
    $atendido_id = $_GET['delete_atendido'];
    $sql_delete = "DELETE FROM atendidos WHERE atendido_id = $atendido_id AND medico_id = $medico_id";
    if ($conn->query($sql_delete) === TRUE) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Paciente eliminado',
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location.href = 'registro_atendidos.php';
            });
        </script>";
    } else {
        echo "Error al eliminar el paciente atendido: " . $conn->error;
    }
}

$conn->close();
?>
</body>
</html>

