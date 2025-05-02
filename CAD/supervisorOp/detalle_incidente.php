<?php
    session_start();


    if (!isset($_GET['id'])) {
        header("Location: supervisorOp.php");
        exit();
    }

    $folio_incidente = $_GET['id'];

    require_once '../db.php';

    $sql = "SELECT i.*, 
                CONCAT(u.nombre, ' ', u.apellido) as nombre_usuario,
                GROUP_CONCAT(DISTINCT au.id_unidad) as unidades_asignadas,
                GROUP_CONCAT(DISTINCT tu.nombre_tipo) as tipos_unidades
            FROM incidentes i
            JOIN usuarios u ON i.id_usuario_reporta = u.id_usuario
            LEFT JOIN asignaciones_unidades au ON i.folio_incidente = au.id_incidente
            LEFT JOIN unidades un ON au.id_unidad = un.id_unidad
            LEFT JOIN tipos_unidad tu ON un.id_tipo_unidad = tu.id_tipo
            WHERE i.folio_incidente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $folio_incidente);
    $stmt->execute();
    $incidente = $stmt->get_result()->fetch_assoc();

    $conn->close();
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalle de Incidente</title>
        <link rel="stylesheet" href="detalle_incidente.css">
    </head>
    <body>
        <div class="blue-bar">
            <img src="../logo.png" alt="Logo" class="logo">
            Detalles del Incidente #<?php echo htmlspecialchars($folio_incidente); ?>
        </div>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Información General</h2>
                    <span class="priority-badge priority-<?php echo strtolower($incidente['prioridad']); ?>">
                        <?php echo htmlspecialchars($incidente['prioridad']); ?>
                    </span>
                </div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Reportado por</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incidente['nombre_usuario']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Fecha y Hora</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incidente['fecha_incidente']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Tipo de Auxilio</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incidente['tipo_auxilio']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Clasificación</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incidente['clasificacion']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Personas Involucradas</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incidente['num_personas']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Teléfono Reporte</div>
                        <div class="detail-value"><?php echo htmlspecialchars($incidente['telefono']); ?></div>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Ubicación</div>
                    <div class="detail-value"><?php echo htmlspecialchars($incidente['latitud'] . ', ' . $incidente['longitud']); ?>

                    
                </div>   
                
                <?php if ($incidente['unidades_asignadas']): ?>
                <div class="detail-item">
                    <div class="detail-label">Unidades Asignadas</div>
                    <div class="units-container">
                        <?php 
                        $unidades = explode(',', $incidente['unidades_asignadas']);
                        $tipos = explode(',', $incidente['tipos_unidades']);
                        foreach(array_combine($unidades, $tipos) as $unidad => $tipo): 
                        ?>
                        <span class="unit-badge"><?php echo htmlspecialchars($tipo); ?> #<?php echo htmlspecialchars($unidad); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="detail-item">
                    <div class="detail-label">Descripción del Incidente</div>
                    <div class="description-box">
                        <?php echo htmlspecialchars($incidente['quepaso']); ?>
                    </div>
                </div>
                
                <div class="btn-group">
                    <a href="supervisor.php" class="btn btn-secondary">Volver al Panel</a>
                </div>
            </div>
        </div>
    </body>
    </html>