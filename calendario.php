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

// Definir el rango de horas (ajústalo según tus necesidades)
$horarioInicio = "08:00";
$horarioFin = "18:00";

// Función para generar intervalos de tiempo de media hora
function generarIntervalos($inicio, $fin) {
    $intervalos = [];
    $horaActual = strtotime($inicio);
    $horaFin = strtotime($fin);

    while ($horaActual < $horaFin) {
        $intervalos[] = date("H:i", $horaActual); // Formatear a H:i (sin segundos)
        $horaActual = strtotime('+30 minutes', $horaActual);
    }
    return $intervalos;
}

// Consultar los turnos ocupados de la base de datos
$sql = "SELECT fecha, horario FROM turnos";
$result = $conn->query($sql);

// Inicializar el array de turnos ocupados
$turnos_ocupados = [];

// Verificar si la consulta fue exitosa
if ($result && $result->num_rows > 0) {
    // Almacenar los turnos ocupados en un array
    while ($row = $result->fetch_assoc()) {
        $dia = date("j", strtotime($row['fecha'])); // Obtener el día del mes
        $hora_turno = date("H:i", strtotime($row['horario'])); // Formatear a H:i
        $turnos_ocupados[$dia][] = $hora_turno;
    }
}

// Definir el mes y el año actual
$mes = date("m");
$año = date("Y");

// Obtener el número de días en el mes actual
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mes, $año);

// Calcular el día de la semana del primer día del mes
$firstDayOfMonth = mktime(0, 0, 0, $mes, 1, $año);
$dayOfWeek = date("w", $firstDayOfMonth); // 0 (Domingo) a 6 (Sábado)

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Turnos Libres</title>
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
                // Verificar si el intervalo está ocupado
                if (isset($turnos_ocupados[$currentDay]) && in_array($intervalo, $turnos_ocupados[$currentDay])) {
                    // Si el horario está ocupado, mostrar como ocupado
                    echo "<li>Ocupado: $intervalo</li>";
                } else {
                    // Si no está ocupado, mostrar como libre
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
</body>
</html>

<?php
$conn->close();
?>
