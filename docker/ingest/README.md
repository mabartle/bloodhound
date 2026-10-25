# Bloodhound transport / ingestion image

This image is the Phase 1 dependency foundation for the original data path:

`BHCOL collector -> BHMM (SSH/SCP) -> BHDB processing -> MariaDB`

It contains OpenSSH client tools for the collector-to-BHMM transfer, gzip/file
utilities, Argus client/server packages for flow processing, tcpdump for
PCAP inspection, Python 3, and the Debian PyMySQL package for database imports.

**Safety behavior:** the container starts idle. It does not automatically
connect to collectors, move or delete files, process captures, or write to the
database. Those behaviors will be enabled only through reviewed scripts and
explicit configuration. This is intentional: the legacy `bhcol_transfer.sh`
runs a remote archive command after its SCP loop without proving that every
file transferred successfully, so it should not be run unchanged in the new
container.

## Build and start

From the repository root, after copying `.env.example` to `.env` and setting
unique local passwords:

```sh
docker compose --profile ingest build bh-ingest
docker compose --profile ingest up -d db bh-ingest
docker compose --profile ingest ps
docker compose exec bh-ingest sh -lc 'ssh -V; python3 --version; ra -h 2>&1 | head -n 2; argus -h 2>&1 | head -n 2'
```

The database is reachable from this Compose network as `db:3306`, not
`127.0.0.1:3307`. The existing host-side database port remains bound to
localhost only.

## Runtime directories and SSH keys

Create local runtime directories (these are ignored by Git):

```sh
mkdir -p runtime/bhmm/{inbound,processing,processed,archive}
mkdir -p secrets/bhmm-ssh
chmod 700 secrets/bhmm-ssh
```

Place the dedicated BHMM private key and a verified `known_hosts` file in
`secrets/bhmm-ssh/`. Do not copy a private key into the repository or image.
Use a dedicated least-privilege key/account and verify collector host keys
out of band; do not disable SSH host-key checking. The container runs as UID
10001 (`bloodhound`), so ensure the mounted key is readable only by that UID
and has mode `0600`; `known_hosts` should be readable by that UID.

The Compose service mounts the key directory read-only and provides separate
inbound, processing, processed, and archive directories. No collector network
or host filesystem is mounted.

## Current scope and next Phase 1 work

This is a dependency image, not yet the complete transfer scheduler. Before
turning on automated movement, implement and test a transfer wrapper that:

1. Reads the collector list and per-environment settings from mounted config.
2. Uses SSH/SCP with strict host-key checking and explicit remote paths.
3. Copies each file to a temporary name, verifies transfer success and size,
   then atomically renames it into inbound.
4. Archives a remote file only after the local copy is verified; never archive
   all remote files just because an SCP command was attempted.
5. Preserves collector identity, interface/collection metadata, filename,
   and transfer timestamps alongside every capture.
6. Sends captures through the same Argus/import pipeline and records the
   capture metadata in `run_tbl` before importing flow rows into `bh_table`.

Do not use real captures for initial tests. Start with the synthetic PCAP in
`tests/fixtures`, then test transfer behavior against a disposable test SSH
endpoint before connecting a real collector. Never expose MariaDB publicly.
