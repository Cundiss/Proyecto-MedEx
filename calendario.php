<?php
// Iniciar sesión si no está iniciada
session_start();

// Establecer la conexión con la base de datos
$conexion = new mysqli('localhost', 'root', '', 'medex');

// Obtener la fecha actual
$year = isset($_GET['year']) ? $_GET['year'] : date("Y");
$month = isset($_GET['month']) ? $_GET['month'] : date("m");

// Obtener el nombre del mes y la cantidad de días en el mes
$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date("t", $firstDayOfMonth);
$monthName = date("F", $firstDayOfMonth);

// Calcular en qué día de la semana empieza el mes (0=Domingo, 1=Lunes, etc.)
$dayOfWeek = date("w", $firstDayOfMonth);

// Obtener los turnos de la base de datos
$sql = "SELECT * FROM turnos 
        JOIN pacientes ON turnos.paciente_id = pacientes.paciente_id 
        WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ii', $month, $year);
$stmt->execute();
$result = $stmt->get_result();

// Crear un array para almacenar los turnos
$turnos = [];
while ($row = $result->fetch_assoc()) {
    $dia = date("j", strtotime($row['fecha']));
    $turnos[$dia][] = $row['nombre'] . ' ' . $row['apellido'];
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Turnos</title>
    <link rel="stylesheet" href="styles.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f7f7f7;
        margin: 0;
        padding: 0;
        /* Elimina display: flex y justify-content/align-items para evitar la superposición con el nav */
        min-height: 100vh;
    }

    /* Contenedor del calendario */
    .calendar-container {
        width: 80%;
        max-width: 900px;
        background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 80px auto 20px; /* Ajuste de margen para que el calendario esté centrado, pero debajo del nav */
    }

    .calendar-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .calendar-header h2 {
        margin: 0;
        font-size: 24px;
    }

    .calendar-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .calendar-nav a {
        text-decoration: none;
        padding: 10px 20px;
        background-color: #3498db;
        color: #fff;
        border-radius: 5px;
    }

    table.calendar {
        width: 100%;
        border-collapse: collapse;
    }

    table.calendar th, table.calendar td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        vertical-align: top;
    }

    table.calendar th {
        background-color: #3498db;
        color: #fff;
    }

    .day-cell {
        height: 100px;
    }

    .day-cell span {
        font-weight: bold;
    }

    .turnos-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .turnos-list li {
        background-color: #f1c40f;
        margin-top: 5px;
        padding: 3px;
        border-radius: 3px;
    }
</style>

</head>
<body>
<nav>
        <a href="turnos.php">Turnos</a>
        <a href="pacientes.php">Pacientes</a>
        <a href="inicio.php">Inicio</a>
        <a href="#">Calendario</a>
        <a href="#">Buscar</a>
    </nav>
    <div class="calendar-container">
        <div class="calendar-header">
            <h2><?= $monthName ?> <?= $year ?></h2>
        </div>
        <div class="calendar-nav">
            <a href="calendario.php?month=<?= $month - 1 ?>&year=<?= $year ?>">Mes Anterior</a>
            <a href="calendario.php?month=<?= $month + 1 ?>&year=<?= $year ?>">Mes Siguiente</a>
        </div>
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

                    if (isset($turnos[$currentDay])) {
                        echo "<ul class='turnos-list'>";
                        foreach ($turnos[$currentDay] as $turno) {
                            echo "<li>$turno</li>";
                        }
                        echo "</ul>";
                    }

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
