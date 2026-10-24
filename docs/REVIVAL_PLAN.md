# Bloodhound Revival Plan

## Goal

Restore the historical Bloodhound workflow in an isolated local Docker environment, record a reproducible baseline, then replace the legacy PHP reporting interface with a Python API and a modern frontend without silently changing report semantics.

## Repository baseline

- Reference branch: `develop`
- Reference commit inspected: `992fa473986911acd7e3d583a688ea122cb40244`
- Legacy web UI: `BH_WEB/` (PHP reports and frame-based navigation)
- Processing: `BHDB/SCRIPTS/bloodhound.py`, shell orchestration/import scripts, Argus configuration, and enrichment lists
- Database: `BHDB/SCRIPTS/BB_DB_SCHEMA.sql` (shared `bh_table` and processing metadata in `run_tbl`)
- Collection/transport: `BHCOL/` and `BHMM/`
- Historical install instructions assume Linux paths under `/opt`, cron, and packet-capture tooling.

## Findings to resolve before a successful restore

1. **Runtime age:** `bloodhound.py` uses Python 2 syntax and `MySQLdb`. Establish a compatible legacy runtime in a disposable container; do not begin by mechanically porting the whole script.
2. **Native tool dependencies:** processing calls `editcap`, `p0f`, `argus`, `ra`, and `tshark`. Pin/test their versions and verify expected output formats with a known, sanitized PCAP.
3. **Database compatibility:** the schema uses MyISAM and date partitions that stop at 2018-11-24. Test schema creation and inserts against a candidate MariaDB version; do not assume a current MySQL image will accept the DDL unchanged.
4. **Path assumptions:** scripts expect fixed paths such as `/opt/BB/INBOUND`, `/opt/BB/OUTBOUND`, and `/opt/BB/SCRIPTS`. Keep these paths inside the first compatibility image or introduce explicit configuration after baseline behavior is recorded.
5. **Secrets:** database credentials are embedded in several tracked files. Treat them as compromised if they were ever used outside a disposable lab; rotate them wherever still valid. New containers must use environment variables / Compose secrets and must not reuse the historical password.
6. **Data integrity and safety:** shell scripts build commands from filenames and processing is not reliably transactional. Use only trusted sample files, validate paths, and add clear failure/status handling before processing untrusted captures.
7. **Web security:** legacy PHP includes database credentials and some report code constructs SQL from request values. Keep the legacy UI bound to localhost only, with no public exposure, until reviewed.

## Target local development architecture

- **legacy-processor**: isolated compatibility image for original scripts and native Argus/Wireshark tooling; no inbound network capture by default.
- **database**: local MariaDB volume, initialized with a tested schema and disposable sample data.
- **api**: Python 3 + FastAPI, SQLAlchemy (or parameterized SQL during the first report ports), Pydantic validation, and pytest.
- **frontend**: React + TypeScript + Vite, calling the API rather than connecting directly to the database.
- **sample-data**: explicit read-only mount for sanitized PCAPs and expected-output fixtures.
- **secrets/config**: local `.env` excluded from Git; checked-in `.env.example` contains placeholders only.

Use Docker Compose first. Add authentication before any non-local deployment. Kubernetes is a later deployment target, not part of the restoration gate.

## Phased implementation

### Phase 0 — Preserve and document
- Keep `develop` unchanged.
- Work on `revival/docker-bootstrap`.
- Inventory PHP pages, query inputs, tables/columns, external tools, configuration files, and all file-system paths.
- Identify any real credentials in history and rotate them; do not copy secrets into the new implementation.

### Phase 1 — Reproducible local baseline
- Build a compatibility image that pins a supported base OS and legacy dependencies.
- Start database in a named Docker volume and test the schema in a disposable database.
- Use a known, sanitized PCAP; run the processing steps manually first, then through a wrapper.
- Record tool versions, logs, inserted row counts by `log_type`, and representative report outputs.
- Do not enable packet capture from the host or mount host-sensitive directories.

### Phase 2 — Make processing testable
- Separate orchestration from parsing and database writes.
- Replace shell-string execution with argument arrays/subprocess APIs, validate filenames, and check exit codes.
- Add a processing-run status record, structured logs, and retry-safe behavior.
- Preserve output fields and timestamps; add fixtures for empty captures, normal traffic, missing optional protocol data, and malformed input.

### Phase 3 — Port reports behind a Python API
- Begin with read-only health and metadata endpoints, then port one report at a time.
- Use validated request models and parameterized database queries.
- Build a report parity checklist comparing the old and new output against the same fixture database.
- Keep API queries read-only until report parity and access controls are tested.

### Phase 4 — Frontend and identity
- Build a React/TypeScript interface for report navigation, date/time filters, and result tables.
- Add authentication and server-side authorization before allowing non-local access.
- Never trust client-side checks alone; enforce permissions in the API.

### Phase 5 — Operational hardening and Kubernetes
- Add health/readiness checks, resource limits, persistent storage plans, backups, and recovery tests.
- Separate API, worker, and database lifecycle; run capture processing as bounded jobs.
- Create Kubernetes manifests only after Compose behavior and storage semantics are proven.

## Initial acceptance criteria

- Clean checkout can start the local services using documented commands.
- Schema initializes successfully in the selected database image.
- One sanitized PCAP can be processed from start to finish with captured exit codes and logs.
- Expected rows are present in the database and at least one original report can be reproduced.
- No live secrets are stored in source control.
- Services bind to localhost for the restoration phase and no host packet capture is enabled by default.

## Immediate next tasks

1. Inspect the complete processing script and all report queries to produce a dependency and data-flow map.
2. Select and verify a database image against the original DDL.
3. Add the smallest Compose environment and health checks.
4. Add a sanitized PCAP fixture and a repeatable smoke-test script.
5. Only then start API/frontend scaffolding.
