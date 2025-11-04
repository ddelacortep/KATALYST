-- ============================================================================
-- KATALYST - Script Completo de Base de Datos para SQL Server
-- ============================================================================
-- Fecha: 4 de noviembre de 2025
-- Descripción: Script completo para crear/recrear la base de datos KATALYST
-- con la estructura correcta para el funcionamiento del sistema de proyectos
-- ============================================================================

USE master;
GO

-- Crear base de datos si no existe
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'Katalyst_')
BEGIN
    CREATE DATABASE Katalyst_;
    PRINT 'Base de datos Katalyst_ creada';
END
GO

USE Katalyst_;
GO

-- ============================================================================
-- ELIMINAR TABLAS EXISTENTES (en orden correcto para evitar errores de FK)
-- ============================================================================
PRINT 'Eliminando tablas existentes...';

IF OBJECT_ID('Estado_Tarea', 'U') IS NOT NULL DROP TABLE Estado_Tarea;
IF OBJECT_ID('Tareas', 'U') IS NOT NULL DROP TABLE Tareas;
IF OBJECT_ID('Participar', 'U') IS NOT NULL DROP TABLE Participar;
IF OBJECT_ID('Proyecto', 'U') IS NOT NULL DROP TABLE Proyecto;
IF OBJECT_ID('Roles', 'U') IS NOT NULL DROP TABLE Roles;
IF OBJECT_ID('Usuario', 'U') IS NOT NULL DROP TABLE Usuario;

PRINT 'Tablas eliminadas correctamente';
GO

-- ============================================================================
-- 1. TABLA: Usuario
-- Descripción: Almacena los usuarios del sistema
-- ============================================================================
CREATE TABLE Usuario (
    id_usuario INT NOT NULL PRIMARY KEY,
    nom_usuario NVARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password NVARCHAR(255) NOT NULL,
    fecha_creacion DATETIME DEFAULT GETDATE(),
    CONSTRAINT CHK_Usuario_Email CHECK (email LIKE '%@%')
);
GO

PRINT 'Tabla Usuario creada';
GO

-- ============================================================================
-- 2. TABLA: Roles
-- Descripción: Define los roles disponibles en el sistema
-- Roles predefinidos: 1=Administrador, 2=Editor, 3=Visualizador
-- ============================================================================
CREATE TABLE Roles (
    id_rols INT NOT NULL PRIMARY KEY,
    nom_rols VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    fecha_creacion DATETIME DEFAULT GETDATE()
);
GO

PRINT 'Tabla Roles creada';
GO

-- ============================================================================
-- 3. TABLA: Proyecto
-- Descripción: Almacena los proyectos del sistema
-- ============================================================================
CREATE TABLE Proyecto (
    id_proyecto INT NOT NULL PRIMARY KEY,
    nom_proyecto VARCHAR(100) NOT NULL,
    descripcion VARCHAR(500) NULL,
    fecha_creacion DATETIME DEFAULT GETDATE(),
    fecha_actualizacion DATETIME DEFAULT GETDATE()
);
GO

PRINT 'Tabla Proyecto creada';
GO

-- ============================================================================
-- 4. TABLA: Participar (Relación Usuario-Proyecto-Rol)
-- Descripción: Tabla pivote que relaciona usuarios con proyectos y sus roles
-- ============================================================================
CREATE TABLE Participar (
    id_proyecto INT NOT NULL,
    id_usuario INT NOT NULL,
    id_rols INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT GETDATE(),
    CONSTRAINT PK_Participar PRIMARY KEY (id_proyecto, id_usuario),
    CONSTRAINT FK_Participar_Proyecto FOREIGN KEY (id_proyecto) 
        REFERENCES Proyecto(id_proyecto) ON DELETE CASCADE,
    CONSTRAINT FK_Participar_Usuario FOREIGN KEY (id_usuario) 
        REFERENCES Usuario(id_usuario) ON DELETE CASCADE,
    CONSTRAINT FK_Participar_Roles FOREIGN KEY (id_rols) 
        REFERENCES Roles(id_rols)
);
GO

PRINT 'Tabla Participar creada';
GO

-- ============================================================================
-- 5. TABLA: Tareas
-- Descripción: Almacena las tareas de los proyectos
-- ============================================================================
CREATE TABLE Tareas (
    id_tarea INT NOT NULL PRIMARY KEY,
    nom_tarea VARCHAR(100) NOT NULL,
    id_proyecto INT NOT NULL,
    id_usuario INT NULL, -- NULL = tarea sin asignar
    id_estados INT NULL, -- NULL = sin estado asignado aún
    fecha_creacion DATETIME DEFAULT GETDATE(),
    fecha_actualizacion DATETIME DEFAULT GETDATE(),
    CONSTRAINT FK_Tareas_Proyecto FOREIGN KEY (id_proyecto) 
        REFERENCES Proyecto(id_proyecto) ON DELETE CASCADE,
    CONSTRAINT FK_Tareas_Usuario FOREIGN KEY (id_usuario) 
        REFERENCES Usuario(id_usuario) ON DELETE SET NULL
);
GO

PRINT 'Tabla Tareas creada';
GO

-- ============================================================================
-- 6. TABLA: Estado_Tarea
-- Descripción: Almacena los estados de las tareas
-- Nota: Cada tarea puede tener múltiples estados (historial)
-- ============================================================================
CREATE TABLE Estado_Tarea (
    id_estado INT NOT NULL PRIMARY KEY,
    nom_estat VARCHAR(50) NOT NULL,
    id_tarea INT NOT NULL,
    fecha_creacion DATETIME DEFAULT GETDATE(),
    CONSTRAINT FK_EstadoTarea_Tarea FOREIGN KEY (id_tarea) 
        REFERENCES Tareas(id_tarea) ON DELETE CASCADE
);
GO

PRINT 'Tabla Estado_Tarea creada';
GO

-- ============================================================================
-- AGREGAR FOREIGN KEY de Tareas a Estado_Tarea
-- (Se agrega después para evitar referencia circular)
-- ============================================================================
ALTER TABLE Tareas
ADD CONSTRAINT FK_Tareas_Estado FOREIGN KEY (id_estados) 
    REFERENCES Estado_Tarea(id_estado) ON DELETE SET NULL;
GO

PRINT 'Relación circular Tareas-Estado_Tarea configurada';
GO

-- ============================================================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- ============================================================================
PRINT 'Creando índices...';

-- Índices en Usuario
CREATE INDEX IDX_Usuario_Email ON Usuario(email);
CREATE INDEX IDX_Usuario_Nombre ON Usuario(nom_usuario);

-- Índices en Proyecto
CREATE INDEX IDX_Proyecto_Nombre ON Proyecto(nom_proyecto);

-- Índices en Participar
CREATE INDEX IDX_Participar_Usuario ON Participar(id_usuario);
CREATE INDEX IDX_Participar_Proyecto ON Participar(id_proyecto);
CREATE INDEX IDX_Participar_Rol ON Participar(id_rols);

-- Índices en Tareas
CREATE INDEX IDX_Tareas_Proyecto ON Tareas(id_proyecto);
CREATE INDEX IDX_Tareas_Usuario ON Tareas(id_usuario);
CREATE INDEX IDX_Tareas_Estado ON Tareas(id_estados);

-- Índices en Estado_Tarea
CREATE INDEX IDX_EstadoTarea_Tarea ON Estado_Tarea(id_tarea);

PRINT 'Índices creados correctamente';
GO

-- ============================================================================
-- DATOS INICIALES - ROLES
-- ============================================================================
PRINT 'Insertando roles predefinidos...';

INSERT INTO Roles (id_rols, nom_rols, descripcion) VALUES
(1, 'Administrador', 'Creador del proyecto. Puede crear y asignar tareas a cualquier usuario, y eliminar cualquier tarea.'),
(2, 'Participante', 'Puede ver todas las tareas y crear tareas asignadas a sí mismo. Solo puede eliminar sus propias tareas.');
GO

PRINT 'Roles insertados: Administrador, Participante';
GO

-- ============================================================================
-- DATOS DE EJEMPLO (OPCIONAL - Comentar si no se desea)
-- ============================================================================
PRINT 'Insertando datos de ejemplo...';

-- Usuario de prueba
INSERT INTO Usuario (id_usuario, nom_usuario, email, password) VALUES
(1, 'ddelacortep', 'dani@katalyst.com', 'password123');
GO

-- Proyecto de prueba
INSERT INTO Proyecto (id_proyecto, nom_proyecto, descripcion) VALUES
(1, 'Proyecto Demo', 'Proyecto de demostración del sistema KATALYST');
GO

-- Asignar usuario como administrador del proyecto
INSERT INTO Participar (id_proyecto, id_usuario, id_rols) VALUES
(1, 1, 1); -- Usuario 1 es Administrador del Proyecto 1
GO

PRINT 'Datos de ejemplo insertados correctamente';
GO

-- ============================================================================
-- VISTAS ÚTILES
-- ============================================================================
PRINT 'Creando vistas...';

-- Vista: Tareas con información completa
CREATE VIEW vw_Tareas_Completas AS
SELECT 
    t.id_tarea,
    t.nom_tarea,
    p.id_proyecto,
    p.nom_proyecto,
    u.id_usuario,
    u.nom_usuario AS usuario_asignado,
    et.id_estado,
    et.nom_estat AS estado_actual,
    t.fecha_creacion,
    t.fecha_actualizacion
FROM Tareas t
INNER JOIN Proyecto p ON t.id_proyecto = p.id_proyecto
LEFT JOIN Usuario u ON t.id_usuario = u.id_usuario
LEFT JOIN Estado_Tarea et ON t.id_estados = et.id_estado;
GO

-- Vista: Participantes de proyectos con roles
CREATE VIEW vw_Participantes_Proyectos AS
SELECT 
    p.id_proyecto,
    pr.nom_proyecto,
    u.id_usuario,
    u.nom_usuario,
    u.email,
    r.id_rols,
    r.nom_rols,
    pa.fecha_asignacion
FROM Participar pa
INNER JOIN Proyecto pr ON pa.id_proyecto = pr.id_proyecto
INNER JOIN Usuario u ON pa.id_usuario = u.id_usuario
INNER JOIN Roles r ON pa.id_rols = r.id_rols;
GO

PRINT 'Vistas creadas correctamente';
GO

-- ============================================================================
-- STORED PROCEDURES ÚTILES
-- ============================================================================
PRINT 'Creando stored procedures...';

-- SP: Obtener siguiente ID para tabla (útil ya que no hay IDENTITY)
CREATE PROCEDURE sp_GetNextId
    @TableName NVARCHAR(50),
    @ColumnName NVARCHAR(50),
    @NextId INT OUTPUT
AS
BEGIN
    DECLARE @SQL NVARCHAR(MAX);
    SET @SQL = N'SELECT @NextId = ISNULL(MAX(' + @ColumnName + '), 0) + 1 FROM ' + @TableName;
    EXEC sp_executesql @SQL, N'@NextId INT OUTPUT', @NextId OUTPUT;
END;
GO

-- SP: Crear tarea con estado automático
CREATE PROCEDURE sp_CrearTarea
    @nom_tarea VARCHAR(100),
    @id_proyecto INT,
    @id_usuario INT = NULL,
    @id_tarea_out INT OUTPUT
AS
BEGIN
    SET NOCOUNT ON;
    BEGIN TRANSACTION;
    
    BEGIN TRY
        -- Obtener siguiente ID para tarea
        DECLARE @nextTareaId INT;
        SELECT @nextTareaId = ISNULL(MAX(id_tarea), 0) + 1 FROM Tareas;
        
        -- Obtener siguiente ID para estado
        DECLARE @nextEstadoId INT;
        SELECT @nextEstadoId = ISNULL(MAX(id_estado), 0) + 1 FROM Estado_Tarea;
        
        -- Crear estado primero
        INSERT INTO Estado_Tarea (id_estado, nom_estat, id_tarea)
        VALUES (@nextEstadoId, 'Pendiente', @nextTareaId);
        
        -- Crear tarea
        INSERT INTO Tareas (id_tarea, nom_tarea, id_proyecto, id_usuario, id_estados)
        VALUES (@nextTareaId, @nom_tarea, @id_proyecto, @id_usuario, @nextEstadoId);
        
        SET @id_tarea_out = @nextTareaId;
        
        COMMIT TRANSACTION;
    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END;
GO

PRINT 'Stored procedures creados correctamente';
GO

-- ============================================================================
-- VERIFICACIÓN FINAL
-- ============================================================================
PRINT '';
PRINT '============================================================================';
PRINT 'VERIFICACIÓN DE LA INSTALACIÓN';
PRINT '============================================================================';

SELECT 'Tablas Creadas' AS Estado, COUNT(*) AS Total 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_TYPE = 'BASE TABLE' 
  AND TABLE_NAME IN ('Usuario', 'Roles', 'Proyecto', 'Participar', 'Tareas', 'Estado_Tarea');

SELECT 'Roles Insertados' AS Estado, COUNT(*) AS Total FROM Roles;

SELECT 'Usuarios Ejemplo' AS Estado, COUNT(*) AS Total FROM Usuario;

SELECT 'Proyectos Ejemplo' AS Estado, COUNT(*) AS Total FROM Proyecto;

PRINT '';
PRINT '============================================================================';
PRINT 'INSTALACIÓN COMPLETADA EXITOSAMENTE';
PRINT '============================================================================';
PRINT '';
PRINT 'Estructura de la base de datos:';
PRINT '  • Usuario (usuarios del sistema)';
PRINT '  • Roles (3 roles: Administrador, Editor, Visualizador)';
PRINT '  • Proyecto (proyectos)';
PRINT '  • Participar (relación usuario-proyecto-rol)';
PRINT '  • Tareas (tareas de proyectos)';
PRINT '  • Estado_Tarea (estados de las tareas)';
PRINT '';
PRINT 'Datos de ejemplo:';
PRINT '  • Usuario: ddelacortep (ID: 1)';
PRINT '  • Email: dani@katalyst.com';
PRINT '  • Password: password123';
PRINT '  • Proyecto Demo (ID: 1)';
PRINT '';
PRINT '============================================================================';
GO

-- ============================================================================
-- CONSULTAS ÚTILES PARA VERIFICACIÓN
-- ============================================================================
/*
-- Ver todos los usuarios
SELECT * FROM Usuario;

-- Ver todos los proyectos con sus participantes
SELECT * FROM vw_Participantes_Proyectos;

-- Ver todas las tareas con información completa
SELECT * FROM vw_Tareas_Completas;

-- Ver estructura de una tabla específica
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'Tareas'
ORDER BY ORDINAL_POSITION;

-- Ver todas las foreign keys
SELECT 
    fk.name AS FK_NAME,
    tp.name AS PARENT_TABLE,
    cp.name AS PARENT_COLUMN,
    tr.name AS REFERENCED_TABLE,
    cr.name AS REFERENCED_COLUMN
FROM sys.foreign_keys AS fk
INNER JOIN sys.tables AS tp ON fk.parent_object_id = tp.object_id
INNER JOIN sys.tables AS tr ON fk.referenced_object_id = tr.object_id
INNER JOIN sys.foreign_key_columns AS fkc ON fk.object_id = fkc.constraint_object_id
INNER JOIN sys.columns AS cp ON fkc.parent_column_id = cp.column_id AND fkc.parent_object_id = cp.object_id
INNER JOIN sys.columns AS cr ON fkc.referenced_column_id = cr.column_id AND fkc.referenced_object_id = cr.object_id
ORDER BY tp.name;
*/
