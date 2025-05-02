<?php
session_start();

require_once '../db.php';

$llamadas = [];
$incidentes = [];

try {
    $sql_llamadas = "SELECT l.id_llamada, l.estatus, 
                    CASE 
                        WHEN l.estatus = 'En curso' THEN 'status-encurso'
                        WHEN l.estatus = 'Finalizada' THEN 'status-finalizada'
                        ELSE 'status-atender'
                    END as clase_estatus
                    FROM llamadas l
                    JOIN usuarios u ON l.id_operador = u.id_usuario
                    ORDER BY l.fecha DESC LIMIT 10";
    
    $stmt_llamadas = $conn->prepare($sql_llamadas);
    $stmt_llamadas->execute();
    $llamadas = $stmt_llamadas->get_result()->fetch_all(MYSQLI_ASSOC);

    $sql_incidentes = "SELECT i.folio_incidente, i.hora_incidente, i.prioridad, 
                       CONCAT(u.nombre, ' ', u.apellido) as nombre_usuario,
                       CASE 
                           WHEN i.prioridad = 'Alta' THEN 'prioridad-alta'
                           WHEN i.prioridad = 'Media' THEN 'prioridad-media'
                           ELSE 'prioridad-baja'
                       END as clase_prioridad
                       FROM incidentes i
                       JOIN usuarios u ON i.id_usuario_reporta = u.id_usuario
                       ORDER BY i.hora_incidente DESC LIMIT 10";
    
    $stmt_incidentes = $conn->prepare($sql_incidentes);
    $stmt_incidentes->execute();
    $incidentes = $stmt_incidentes->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error = "Error al obtener datos: " . $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Supervisión</title>
    <link rel="stylesheet" href="supOp.css">
</head>
<body>
    <div class="blue-bar">
        <img src="../logo.png" alt="Logo" class="logo">
        Panel de Supervisión 
    </div>
    
    <div class="search-container">
        <form method="GET" class="search-box">
            <input type="text" name="busqueda" placeholder="Buscar en llamadas e incidentes...">
            <button type="submit">Buscar</button>
        </form>
    </div>
    
    <div class="dashboard-container">
        <div class="panel">
            <h3>Llamadas Recientes</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Llamada</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($llamadas as $llamada): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($llamada['id_llamada']); ?></td>
                        <td>
                            <span class="status-circle <?php echo htmlspecialchars($llamada['clase_estatus']); ?>"></span>
                            <?php echo htmlspecialchars($llamada['estatus']); ?>
                        </td>
                       
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="panel">
            <h3>Incidentes Recientes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Hora</th>
                        <th>Prioridad</th>
                        <th>Reportado por</th>
                        <th>Acciones</th>
                        <th>Calificar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidentes as $incidente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($incidente['folio_incidente']); ?></td>
                        <td><?php echo htmlspecialchars($incidente['hora_incidente']); ?></td>
                        <td>
                            <span class="priority-circle <?php echo htmlspecialchars($incidente['clase_prioridad']); ?>"></span>
                            <?php echo htmlspecialchars($incidente['prioridad']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($incidente['nombre_usuario']); ?></td>
                        <td>
                            <button class="action-btn" 
                                    onclick="window.location.href='detalle_incidente.php?id=<?php echo $incidente['folio_incidente']; ?>'">
                                Ver Detalle
                            </button>
                        </td>
                        <td>
                            <button class="action-btn" 
                                    onclick="window.location.href='../calificar.php?id=<?php echo $incidente['folio_incidente']; ?>'">
                                Calificar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>