-- ============================================================================
-- KATALYST - Actualización de Roles (Simplificación a 2 roles)
-- ============================================================================
-- Fecha: 4 de noviembre de 2025
-- Descripción: Actualiza el sistema de roles a solo Administrador y Participante
-- ============================================================================

USE Katalyst_;
GO

PRINT '=== INICIANDO ACTUALIZACIÓN DE ROLES ===';
GO

-- ============================================================================
-- PASO 1: Verificar roles existentes
-- ============================================================================
PRINT 'Roles actuales:';
SELECT id_rols, nom_rols, descripcion FROM Roles ORDER BY id_rols;
GO

-- ============================================================================
-- PASO 2: Crear/Actualizar rol Participante (id=2)
-- ============================================================================
PRINT 'Paso 2: Asegurando que existe el rol Participante...';

IF NOT EXISTS (SELECT 1 FROM Roles WHERE id_rols = 2)
BEGIN
    INSERT INTO Roles (id_rols, nom_rols, descripcion) VALUES
    (2, 'Participante', 'Puede ver todas las tareas y crear tareas asignadas a sí mismo. Solo puede eliminar sus propias tareas.');
    PRINT 'Rol Participante creado';
END
ELSE
BEGIN
    UPDATE Roles 
    SET 
        nom_rols = 'Participante',
        descripcion = 'Puede ver todas las tareas y crear tareas asignadas a sí mismo. Solo puede eliminar sus propias tareas.'
    WHERE id_rols = 2;
    PRINT 'Rol Participante actualizado';
END
GO

-- ============================================================================
-- PASO 3: Convertir usuarios con rol Visualizador (3) a Participante (2)
-- ============================================================================
PRINT 'Paso 3: Convirtiendo usuarios con rol Visualizador a Participante...';

UPDATE Participar 
SET id_rols = 2 
WHERE id_rols = 3;

DECLARE @afectados INT = @@ROWCOUNT;
PRINT CAST(@afectados AS VARCHAR) + ' usuarios convertidos de Visualizador a Participante';
GO

-- ============================================================================
-- PASO 4: Eliminar rol Visualizador (3)
-- ============================================================================
PRINT 'Paso 4: Eliminando rol Visualizador...';

DELETE FROM Roles WHERE id_rols = 3;
PRINT 'Rol Visualizador eliminado';
GO

-- ============================================================================
-- PASO 5: Actualizar descripción del rol Administrador
-- ============================================================================
PRINT 'Paso 5: Actualizando descripción del rol Administrador...';

UPDATE Roles 
SET descripcion = 'Creador del proyecto. Puede crear y asignar tareas a cualquier usuario, y eliminar cualquier tarea.'
WHERE id_rols = 1;

PRINT 'Rol Administrador actualizado';
GO

-- ============================================================================
-- VERIFICACIÓN FINAL
-- ============================================================================
PRINT '=== VERIFICACIÓN DE CAMBIOS ===';

PRINT 'Roles finales en el sistema:';
SELECT id_rols, nom_rols, descripcion FROM Roles ORDER BY id_rols;

PRINT 'Distribución de usuarios por rol:';
SELECT 
    r.nom_rols as Rol,
    COUNT(*) as Total_Usuarios
FROM Participar p
INNER JOIN Roles r ON p.id_rols = r.id_rols
GROUP BY r.nom_rols, r.id_rols
ORDER BY r.id_rols;

PRINT '=== ACTUALIZACIÓN COMPLETADA EXITOSAMENTE ===';
GO
