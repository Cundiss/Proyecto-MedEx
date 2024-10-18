<?php
session_start();
if (!isset($_SESSION['medico_id'])) {
    header("Location: index.php");
    exit;
}

// Conexión a la base de datos
include 'config.php';

// Obtener el medico_id de la sesión
$medico_id = $_SESSION['medico_id'];

// Obtener datos del médico logueado
$sql_medico = "SELECT nombre, email FROM medicos WHERE medico_id = $medico_id";
$result_medico = $conn->query($sql_medico);
$medico = $result_medico->fetch_assoc();

// Definir el rango de horas
$horarioInicio = "08:00";
$horarioFin = "18:00";

// Función para generar intervalos de tiempo de media hora
function generarIntervalos($inicio, $fin) {
    $intervalos = [];
    $horaActual = strtotime($inicio);
    $horaFin = strtotime($fin);

    while ($horaActual < $horaFin) {
        $intervalos[] = date("H:i", $horaActual); 
        $horaActual = strtotime('+30 minutes', $horaActual);
    }
    return $intervalos;
}

// Definir el mes y el año actual (o los que se están visualizando)
$mes = isset($_GET['mes']) ? $_GET['mes'] : date("m");
$año = isset($_GET['año']) ? $_GET['año'] : date("Y");

// Consultar los turnos ocupados de la base de datos para el médico logueado
$sql = "SELECT t.fecha, t.horario, p.nombre, p.apellido 
        FROM turnos t 
        JOIN pacientes p ON t.paciente_id = p.paciente_id 
        WHERE p.medico_id = ? AND MONTH(t.fecha) = ? AND YEAR(t.fecha) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $medico_id, $mes, $año);
$stmt->execute();
$result = $stmt->get_result();

$turnos_ocupados = [];
while ($row = $result->fetch_assoc()) {
    $dia = date("j", strtotime($row['fecha']));
    $hora_turno = date("H:i", strtotime($row['horario']));
    $turnos_ocupados[$dia][] = ['hora' => $hora_turno, 'paciente' => $row['nombre'] . ' ' . $row['apellido']];
}

// Obtener el número de días en el mes actual
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mes, $año);

// Calcular el día de la semana del primer día del mes
$firstDayOfMonth = mktime(0, 0, 0, $mes, 1, $año);
$dayOfWeek = date("w", $firstDayOfMonth); // 0 (Domingo) a 6 (Sábado)
?>
<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Turnos</title>
    <link rel="stylesheet" href="Styles/StyleCalendario.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<!-- Mostrar el mes y año actual -->
<div class="calendar-header">
    <h2><?php echo date("F", mktime(0, 0, 0, $mes, 1, $año)) . " " . $año; ?></h2>
    <form method="get" action="calendario.php">
        <input type="hidden" name="año" value="<?php echo $año; ?>">
        <button class="buscar-btn" type="submit" name="mes" value="<?php echo $mes == 1 ? 12 : $mes - 1; ?>" 
                onclick="this.form.año.value = '<?php echo $mes == 1 ? $año - 1 : $año; ?>'">
            Mes anterior
        </button>
        <button class="buscar-btn" type="submit" name="mes" value="<?php echo $mes == 12 ? 1 : $mes + 1; ?>" 
                onclick="this.form.año.value = '<?php echo $mes == 12 ? $año + 1 : $año; ?>'">
            Mes siguiente
        </button>
    </form>
</div>


<div class="calendar-container">
    <table class="calendar">
        <tr>
            <th>Dom</th>
            <th>Lun</th>
            <th>Mar</th>
            <th>Mié</th>
            <th>Jue</th>
            <th>Vie</th>
            <th>Sáb</th>
        </tr>
        <tr>
        <?php
        $currentDay = 1;
        $currentWeekDay = 0;

        // Espacios vacíos antes del primer día del mes
        for ($i = 0; $i < $dayOfWeek; $i++) {
            echo "<td></td>";
            $currentWeekDay++;
        }

        // Días del mes
        while ($currentDay <= $daysInMonth) {
            if ($currentWeekDay == 7) {
                echo "</tr><tr>"; // Nueva fila
                $currentWeekDay = 0;
            }

            echo "<td class='day-cell'>";
            echo "<span>$currentDay</span>";

            // Generar los intervalos de media hora
            $intervalos = generarIntervalos($horarioInicio, $horarioFin);

            // Mostrar los turnos disponibles
            echo "<ul class='turnos-list'>";
            foreach ($intervalos as $intervalo) {
                // Buscar si el intervalo está ocupado
                $turnosDelDia = isset($turnos_ocupados[$currentDay]) ? $turnos_ocupados[$currentDay] : [];
                $turno_encontrado = false;
                $pacientes_en_turno = [];

                foreach ($turnosDelDia as $turno) {
                    // Comparamos si el turno cae dentro de los 30 minutos antes o después del intervalo
                    $hora_turno = strtotime($turno['hora']);
                    $intervalo_inicio = strtotime($intervalo);
                    $intervalo_fin = strtotime('+30 minutes', $intervalo_inicio);

                    if ($hora_turno >= $intervalo_inicio && $hora_turno < $intervalo_fin) {
                        $turno_encontrado = true;
                        $pacientes_en_turno[] = "{$turno['hora']} - {$turno['paciente']}";
                    }
                }

                if ($turno_encontrado) {
                    // Mostrar como ocupado con los pacientes y sus horas exactas
                    $pacientes_lista = implode("<br>", $pacientes_en_turno);
                    echo "<li class='ocupado' onclick='mostrarPacientes(\"$pacientes_lista\")'>Ocupado: $intervalo</li>";
                } else {
                    // Mostrar como libre
                    echo "<li>Libre: $intervalo</li>";
                }
            }
            echo "</ul>";

            echo "</td>";

            $currentDay++;
            $currentWeekDay++;
        }

        // Espacios vacíos después del último día del mes
        while ($currentWeekDay < 7) {
            echo "<td></td>";
            $currentWeekDay++;
        }
        ?>
        </tr>
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
// Función para mostrar la ventana flotante con pacientes y sus horarios usando SweetAlert2
function mostrarPacientes(pacientes) {
    Swal.fire({
        title: 'Pacientes con turno',
        html: `<p>${pacientes}</p>`,
        showCloseButton: true,
        showConfirmButton: false
    });
}
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

</body>
</html>

<?php
$conn->close();
?>

