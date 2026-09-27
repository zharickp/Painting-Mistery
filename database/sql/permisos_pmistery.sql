-- =====================================================================
-- Painting Mistery — Hacer a `pmistery` (usuario de la aplicación)
-- DUEÑO de toda la base de datos painting_mistery.
--
-- Así Laravel puede crear, modificar y borrar tablas (migraciones) sin
-- volver a chocar con "must be owner of table ...".
-- NO convierte a pmistery en superusuario: solo manda sobre ESTA base.
--
-- CÓMO EJECUTARLO (una sola vez), en el servidor:
--     sudo -u postgres psql -d painting_mistery -f permisos_pmistery.sql
-- o en pgAdmin, conectado como postgres, Query Tool sobre painting_mistery.
-- =====================================================================

-- 1) Dueño de la base de datos y del esquema public
ALTER DATABASE painting_mistery OWNER TO pmistery;
ALTER SCHEMA public OWNER TO pmistery;

-- 2) Dueño de todas las tablas, vistas y secuencias del esquema public
DO $$
DECLARE
    r record;
BEGIN
    -- Tablas (sus secuencias de id pasan solas junto con la tabla)
    FOR r IN SELECT tablename FROM pg_tables
             WHERE schemaname = 'public' AND tableowner <> 'pmistery'
    LOOP
        EXECUTE format('ALTER TABLE public.%I OWNER TO pmistery', r.tablename);
        RAISE NOTICE 'Tabla %: ahora de pmistery', r.tablename;
    END LOOP;

    -- Vistas
    FOR r IN SELECT viewname FROM pg_views
             WHERE schemaname = 'public' AND viewowner <> 'pmistery'
    LOOP
        EXECUTE format('ALTER VIEW public.%I OWNER TO pmistery', r.viewname);
        RAISE NOTICE 'Vista %: ahora de pmistery', r.viewname;
    END LOOP;

    -- Secuencias sueltas (las que no pertenecen a una tabla)
    FOR r IN SELECT sequencename FROM pg_sequences
             WHERE schemaname = 'public' AND sequenceowner <> 'pmistery'
    LOOP
        EXECUTE format('ALTER SEQUENCE public.%I OWNER TO pmistery', r.sequencename);
        RAISE NOTICE 'Secuencia %: ahora de pmistery', r.sequencename;
    END LOOP;
END
$$;

-- 3) Verificación: esta consulta debe devolver 0 filas
SELECT 'tabla' AS tipo, tablename AS nombre, tableowner AS dueno
  FROM pg_tables WHERE schemaname = 'public' AND tableowner <> 'pmistery'
UNION ALL
SELECT 'secuencia', sequencename, sequenceowner
  FROM pg_sequences WHERE schemaname = 'public' AND sequenceowner <> 'pmistery';
