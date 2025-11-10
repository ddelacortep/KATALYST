-- Script para agregar el rol SuperAdmin
-- Este rol será exclusivo para el creador del proyecto

-- Insertar el rol SuperAdmin con id_rols = 3
INSERT INTO roles (id_rols, nom_rols) 
VALUES (3, 'SuperAdmin');

-- Verificar que se haya insertado correctamente
SELECT * FROM roles;
