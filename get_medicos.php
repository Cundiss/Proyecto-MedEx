<?php
// Conexión a la base de datos
require_once 'config.php';

$sql = "SELECT medico_id, nombre FROM medicos";
$result = $conn->query($sql);

$medicos = [];
while($row = $result->fetch_assoc()) {
    $medicos[] = $row;
}

echo json_encode($medicos);
?>
