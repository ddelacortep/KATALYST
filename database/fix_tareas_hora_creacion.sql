-- Script para solucionar la tabla Tareas en SQL Server
-- Ejecutar este script en SQL Server Management Studio o Azure Data Studio

USE Katalyst_;
GO

-- Opción 1: Agregar un valor por defecto a hora_creacion
-- Esta es la mejor opción si quieres mantener la columna
ALTER TABLE tareas
ADD CONSTRAINT DF_tareas_hora_creacion DEFAULT GETDATE() FOR hora_creacion;
GO

-- Verificar la estructura de la tabla
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'tareas'
ORDER BY ORDINAL_POSITION;
GO

-- Si prefieres hacer la columna nullable (Opción 2 - alternativa):
-- ALTER TABLE tareas
-- ALTER COLUMN hora_creacion DATETIME NULL;
-- GO

PRINT 'Script ejecutado correctamente. La columna hora_creacion ahora tiene un valor por defecto.';
