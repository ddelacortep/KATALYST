-- Script para arreglar la columna id_proyecto y hacerla IDENTITY (auto-incremental)

-- Paso 1: Primero, necesitas eliminar la restricción de clave primaria si existe
IF EXISTS (SELECT * FROM sys.key_constraints WHERE type = 'PK' AND OBJECT_NAME(parent_object_id) = 'Proyecto')
BEGIN
    DECLARE @pkName NVARCHAR(128)
    SELECT @pkName = name FROM sys.key_constraints WHERE type = 'PK' AND OBJECT_NAME(parent_object_id) = 'Proyecto'
    EXEC('ALTER TABLE Proyecto DROP CONSTRAINT ' + @pkName)
END
GO

-- Paso 2: Eliminar las restricciones de clave foránea en tablas relacionadas
-- Si tienes FK en la tabla Participar u otras tablas
IF EXISTS (SELECT * FROM sys.foreign_keys WHERE name = 'FK_Participar_Proyecto')
BEGIN
    ALTER TABLE Participar DROP CONSTRAINT FK_Participar_Proyecto
END
GO

-- Paso 3: Crear una nueva tabla con la estructura correcta
CREATE TABLE Proyecto_NEW (
    id_proyecto INT IDENTITY(1,1) PRIMARY KEY,
    nom_proyecto NVARCHAR(255) NOT NULL
)
GO

-- Paso 4: Copiar los datos existentes (si los hay)
SET IDENTITY_INSERT Proyecto_NEW ON
IF EXISTS (SELECT * FROM Proyecto)
BEGIN
    INSERT INTO Proyecto_NEW (id_proyecto, nom_proyecto)
    SELECT id_proyecto, nom_proyecto FROM Proyecto
END
SET IDENTITY_INSERT Proyecto_NEW OFF
GO

-- Paso 5: Eliminar la tabla antigua y renombrar la nueva
DROP TABLE Proyecto
GO

EXEC sp_rename 'Proyecto_NEW', 'Proyecto'
GO

-- Paso 6: Recrear las restricciones de clave foránea
-- Ajusta esto según tus tablas relacionadas
IF EXISTS (SELECT * FROM sys.tables WHERE name = 'Participar')
BEGIN
    ALTER TABLE Participar 
    ADD CONSTRAINT FK_Participar_Proyecto 
    FOREIGN KEY (id_proyecto) REFERENCES Proyecto(id_proyecto)
END
GO

-- Verificar que la columna ahora es IDENTITY
SELECT 
    c.name AS ColumnName,
    COLUMNPROPERTY(object_id('Proyecto'), c.name, 'IsIdentity') AS IsIdentity
FROM sys.columns c
WHERE c.object_id = OBJECT_ID('Proyecto') AND c.name = 'id_proyecto'
GO
