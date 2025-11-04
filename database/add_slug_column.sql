-- Agregar columna slug a la tabla proyecto
-- Ejecutar este script en SQL Server Management Studio o Azure Data Studio

USE Katalyst_;
GO

-- 1. Agregar la columna slug (permite NULL temporalmente)
ALTER TABLE proyecto 
ADD slug VARCHAR(255) NULL;
GO

-- 2. Generar slugs para proyectos existentes
-- Reemplaza espacios por guiones y convierte a minúsculas
UPDATE proyecto 
SET slug = LOWER(
    REPLACE(
        REPLACE(
            REPLACE(
                REPLACE(
                    REPLACE(
                        REPLACE(nom_proyecto, ' ', '-'),
                        'á', 'a'),
                    'é', 'e'),
                'í', 'i'),
            'ó', 'o'),
        'ú', 'u')
)
WHERE slug IS NULL;
GO

-- 3. Manejar slugs duplicados agregando un número al final
DECLARE @id_proyecto INT;
DECLARE @slug VARCHAR(255);
DECLARE @base_slug VARCHAR(255);
DECLARE @counter INT;
DECLARE @new_slug VARCHAR(255);

DECLARE slug_cursor CURSOR FOR
    SELECT id_proyecto, slug
    FROM proyecto
    WHERE slug IN (
        SELECT slug
        FROM proyecto
        GROUP BY slug
        HAVING COUNT(*) > 1
    )
    ORDER BY id_proyecto;

OPEN slug_cursor;
FETCH NEXT FROM slug_cursor INTO @id_proyecto, @slug;

WHILE @@FETCH_STATUS = 0
BEGIN
    SET @base_slug = @slug;
    SET @counter = 1;
    SET @new_slug = @base_slug + '-' + CAST(@counter AS VARCHAR);
    
    -- Buscar un slug único
    WHILE EXISTS (SELECT 1 FROM proyecto WHERE slug = @new_slug AND id_proyecto != @id_proyecto)
    BEGIN
        SET @counter = @counter + 1;
        SET @new_slug = @base_slug + '-' + CAST(@counter AS VARCHAR);
    END
    
    -- Actualizar el proyecto con el nuevo slug único
    UPDATE proyecto 
    SET slug = @new_slug
    WHERE id_proyecto = @id_proyecto;
    
    FETCH NEXT FROM slug_cursor INTO @id_proyecto, @slug;
END

CLOSE slug_cursor;
DEALLOCATE slug_cursor;
GO

-- 4. Hacer la columna NOT NULL y agregar índice único
ALTER TABLE proyecto 
ALTER COLUMN slug VARCHAR(255) NOT NULL;
GO

-- 5. Crear índice único en la columna slug
CREATE UNIQUE INDEX idx_proyecto_slug ON proyecto(slug);
GO

-- Verificar los resultados
SELECT id_proyecto, nom_proyecto, slug 
FROM proyecto 
ORDER BY id_proyecto;
GO
