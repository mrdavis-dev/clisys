# DB Init Scripts

MySQL auto-runs all `.sql` files in this directory alphabetically on the first container start (when the data volume is empty).

## Current setup

```
01_schema.sql   — Schema completo: todas las tablas, seeds y datos de demo
```

`01_schema.sql` es el esquema consolidado que incluye todo lo de las fases 1, 2 y 3. Crea las 13 tablas en el orden correcto de FKs, inserta los catálogos (specialties, modules, plans) y los datos de demo (Clínica Anguizola + usuario admin/admin123).

## Instancia fresca

```bash
docker compose up -d
# MySQL crea la BD, ejecuta 01_schema.sql y queda listo.
```

## Bases de datos existentes (fuera de Docker)

Para aplicar las fases a una BD existente, usar las migraciones en la raíz del proyecto:

```bash
mysql -u root -p tu_base_de_datos < migration_fase2.sql
mysql -u root -p tu_base_de_datos < migration_fase3.sql
```
