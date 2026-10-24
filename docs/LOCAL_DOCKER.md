# Local Docker bootstrap

This is a deliberately small first step: start an isolated database without running the legacy PHP UI or packet-processing scripts yet.

## Prerequisites

- Docker Desktop (Windows/macOS) or Docker Engine + the Compose plugin (Linux)
- A local clone of this repository

## Start

1. Copy `.env.example` to `.env`.
2. Replace both example passwords with unique local-only values. Do not commit `.env`.
3. Start the database:

   ```sh
   docker compose up -d db
   docker compose ps
   docker compose logs db
   ```

4. Connect locally on `127.0.0.1:3307` with a MariaDB client. The database is created empty.

## Important: schema not auto-loaded yet

Do **not** mount `BHDB/SCRIPTS/BB_DB_SCHEMA.sql` into the initialization directory as-is. It includes a historical grant/password and the `bh_table` partitions end in November 2018. Before loading it, prepare a local-only schema copy that removes embedded credentials and handles modern dates, then test that copy against the selected MariaDB version. Keep the historical source file unchanged for reference.

The legacy PHP app and processing scripts are intentionally not started by this Compose file. They are not yet safe to expose or run against arbitrary PCAPs.

## Stop and reset

Stop containers while retaining the database volume:

```sh
docker compose down
```

To delete the local database contents and start over, remove the volume too:

```sh
docker compose down -v
```

The second command is destructive. Do not run it if you need to keep local data.
