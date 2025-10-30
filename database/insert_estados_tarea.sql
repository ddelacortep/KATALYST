-- Script para insertar estados de tareas por defecto en SQL Server
USE Katalyst_;
GO

-- Insertar estados básicos
INSERT INTO estado_tarea (id_estados, nom_estado) VALUES (1, 'Pendiente');
INSERT INTO estado_tarea (id_estados, nom_estado) VALUES (2, 'En Progreso');
INSERT INTO estado_tarea (id_estados, nom_estado) VALUES (3, 'Completada');

SELECT * FROM estado_tarea;
GO

PRINT 'Estados de tarea insertados correctamente.';
