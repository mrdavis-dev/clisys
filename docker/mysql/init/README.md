# DB Init Scripts

Place `.sql` files here to auto-run on first container start.

Order: files run alphabetically. Example setup:

```
01_schema.sql   — CREATE TABLE statements
02_seed.sql     — initial data (admin user, default clinic, etc.)
```

Copy migration files:
```bash
cp migration_fase2.sql docker/mysql/init/01_migration_fase2.sql
```
