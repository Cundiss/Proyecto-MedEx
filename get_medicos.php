<?php
require_once 'config.php';

$paciente_id = $_GET['paciente_id'];

// Obtener todos los médicos
$sql_medicos = "SELECT medico_id, nombre FROM medicos";
$result_medicos = $conn->query($sql_medicos);

$medicos = [];
while ($medico = $result_medicos->fetch_assoc()) {
    $medicos[] = $medico;
}

// Obtener los médicos asignados a este paciente
$sql_medicos_asignados = "SELECT medico_id FROM paciente_medico WHERE paciente_id = '$paciente_id'";
$result_medicos_asignados = $conn->query($sql_medicos_asignados);

$medicos_asignados = [];
while ($medico_asignado = $result_medicos_asignados->fetch_assoc()) {
    $medicos_asignados[] = $medico_asignado['medico_id'];
}

// Devolver los médicos y los médicos asignados como JSON
echo json_encode([
    'medicos' => $medicos,
    'medicos_asignados' => $medicos_asignados
]);
?>
