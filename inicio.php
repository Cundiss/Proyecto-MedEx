<?php
session_start();
if (!isset($_SESSION['medico_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>MedEx</title>
</head>
<body>

    <nav>
        <a href="turnos.php">Turnos</a>
        <a href="pacientes.php">Pacientes</a>
        <a href="#">Inicio</a>
        <a href="calendario.php">Calendario</a>
        <a href="#">Buscar</a>
    </nav>

    <div class="container">
        <div class="proximo-paciente">
            <h2>Próximo Paciente</h2>
            <input type="text" placeholder="Paciente X" style="width: 100%; padding: 10px;">
        </div>

        <div class="columns">
            <div class="column pendientes">
                <h3>Pendientes</h3>
                <div>
                    <input type="checkbox">
                    <span>Horario</span>
                    <span>Nombre</span>
                    <span>DNI</span>
                </div>
                <div>
                    <input type="checkbox">
                    <span>08:00</span>
                    <span>Juan Pérez</span>
                    <span>12345678</span>
                </div>
                <div>
                    <input type="checkbox">
                    <span>08:30</span>
                    <span>María López</span>
                    <span>87654321</span>
                </div>
            </div>

            <div class="column atendidos">
                <h3>Atendidos</h3>
                <div>
                    <span>Paciente 1</span>
                </div>
                <div>
                    <span>Paciente 2</span>
                </div>
            </div>
        </div>
    </div>

    <a href="logout.php">Cerrar Sesión</a>

    <footer>
        <p>&copy; 2024 MedEx - Todos los derechos reservados</p>
    </footer>

</body>
</html>