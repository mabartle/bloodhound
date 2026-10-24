#!/usr/bin/env python3
"""Import Argus ra CSV output into Bloodhound's legacy-compatible bh_table.

Expected ra configuration: BHDB/SCRIPTS/excel.rarc.
The date/time format expands StartTime and LastTime into separate date/time
columns. SrcId is deliberately ignored; capid and procid are CLI arguments.

Install dependency:
    python3 -m pip install PyMySQL

Example (dry run):
    python3 tools/import_argus_csv.py /path/to/ra-output.csv --capid 1 --procid 1 --dry-run

Example (insert):
    DB_HOST=127.0.0.1 DB_PORT=3307 DB_USER=bloodhound_app DB_PASSWORD='...' \
      python3 tools/import_argus_csv.py /path/to/ra-output.csv --capid 1 --procid 1 --commit

The database connection settings can also be supplied with DB_NAME, defaulting
to bloodhound. This script does not create tables or modify run_tbl.
"""
from __future__ import annotations

import argparse
import csv
import os
import sys
from datetime import date, datetime
from decimal import Decimal, InvalidOperation

try:
    import pymysql
except ImportError:
    print("Missing dependency. Install with: python3 -m pip install PyMySQL", file=sys.stderr)
    raise SystemExit(2)

# ra data rows: SrcId, start-date, start-time, last-date, last-time, duration,
# src, dst, proto, sport, dport, bytes, src-bytes, dst-bytes, packets,
# src-packets, dst-packets, direction, trans, seq, flags.
EXPECTED_FIELDS = 21
NUMERIC_FIELDS = {"proto", "sport", "dport", "bytes", "sbytes", "dbytes",
                  "pkts", "spkts", "dpkts", "trans", "seq"}
INTEGER_DEFAULT_ZERO = {"proto", "bytes", "sbytes", "dbytes", "pkts",
                        "spkts", "dpkts", "trans", "seq"}
TARGET_COLUMNS = (
    "log_type, procid, capid, sdate, stime, ldate, ltime, dur, saddr, daddr, "
    "proto, sport, dport, bytes, sbytes, dbytes, pkts, spkts, dpkts, dir, "
    "trans, seq, flags"
)
INSERT_SQL = (
    f"INSERT INTO bh_table ({TARGET_COLUMNS}) VALUES ("
    + ", ".join(["%s"] * 23) + ")"
)


def blank_to_none(value: str | None):
    if value is None or value.strip() == "":
        return None
    return value.strip()


def parse_int(value: str | None, field: str):
    value = blank_to_none(value)
    if value is None:
        return 0 if field in INTEGER_DEFAULT_ZERO else None
    try:
        return int(value)
    except ValueError as exc:
        raise ValueError(f"{field} is not an integer: {value!r}") from exc


def parse_duration(value: str | None):
    value = blank_to_none(value)
    if value is None:
        return Decimal("0")
    try:
        result = Decimal(value)
    except InvalidOperation as exc:
        raise ValueError(f"duration is not numeric: {value!r}") from exc
    if result < 0:
        raise ValueError("duration cannot be negative")
    return result


def parse_row(raw: list[str], line_number: int, capid: int, procid: int):
    # The observed output has 19, 20, and 21 fields. Until we establish whether
    # short rows omit only trailing fields or suppress interior empty fields,
    # reject them rather than risk silently shifting values into the wrong
    # database columns.
    if len(raw) != EXPECTED_FIELDS:
        raise ValueError(
            f"expected exactly 21 fields for safe mapping, got {len(raw)}; "
            "regenerate ra output with all fields preserved"
        )
    fields = raw
    (srcid, sdate, stime, ldate, ltime, dur, saddr, daddr, proto,
     sport, dport, total_bytes, src_bytes, dst_bytes, total_pkts,
     src_pkts, dst_pkts, direction, trans, seq, flags) = fields

    # Ignore the header if ra was run with names enabled.
    if srcid.strip().lower() in {"srcid", "sourceid"}:
        return None

    try:
        start_date = date.fromisoformat(sdate.strip())
        start_time = datetime.strptime(stime.strip(), "%H:%M:%S").time()
        last_date = date.fromisoformat(ldate.strip())
        last_time = datetime.strptime(ltime.strip(), "%H:%M:%S").time()
    except (ValueError, AttributeError) as exc:
        raise ValueError("invalid start/last date or time") from exc

    source = blank_to_none(saddr)
    destination = blank_to_none(daddr)
    if not source or not destination:
        raise ValueError("source or destination address is empty")

    sport_value = parse_int(sport, "sport")
    dport_value = parse_int(dport, "dport")
    # The legacy table allows NULL for sport/dport; keep unknown ports NULL.
    values = (
        "ARGUSLOG", procid, capid, start_date, start_time, last_date, last_time,
        parse_duration(dur), source, destination, parse_int(proto, "proto"),
        sport_value, dport_value, parse_int(total_bytes, "bytes"),
        parse_int(src_bytes, "sbytes"), parse_int(dst_bytes, "dbytes"),
        parse_int(total_pkts, "pkts"), parse_int(src_pkts, "spkts"),
        parse_int(dst_pkts, "dpkts"), blank_to_none(direction) or "-",
        parse_int(trans, "trans"), parse_int(seq, "seq"),
        blank_to_none(flags) or "-"
    )
    return values


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("csv_file", help="CSV output from ra")
    parser.add_argument("--capid", type=int, required=True, help="Bloodhound capture ID")
    parser.add_argument("--procid", type=int, required=True, help="Bloodhound processing ID")
    parser.add_argument("--dry-run", action="store_true", help="validate only; do not write")
    parser.add_argument("--commit", action="store_true", help="perform database inserts")
    args = parser.parse_args()

    if args.dry_run and args.commit:
        parser.error("choose either --dry-run or --commit, not both")
    if not args.dry_run and not args.commit:
        parser.error("no database changes made by default; specify --dry-run or --commit")

    rows, errors = [], []
    try:
        with open(args.csv_file, "r", newline="", encoding="utf-8", errors="replace") as stream:
            reader = csv.reader(stream)
            for line_number, raw in enumerate(reader, start=1):
                if not raw or all(not cell.strip() for cell in raw):
                    continue
                try:
                    parsed = parse_row(raw, line_number, args.capid, args.procid)
                    if parsed is not None:
                        rows.append(parsed)
                except ValueError as exc:
                    errors.append((line_number, str(exc), raw))
    except OSError as exc:
        parser.error(str(exc))

    for line_number, message, raw in errors:
        print(f"REJECT line {line_number}: {message}; fields={len(raw)}", file=sys.stderr)
    print(f"Valid rows: {len(rows)}; rejected rows: {len(errors)}")
    if errors:
        print("No rows inserted because at least one row failed validation.", file=sys.stderr)
        return 1
    if args.dry_run:
        print("Dry run complete; database unchanged.")
        return 0
    if not rows:
        print("No valid data rows found; nothing inserted.")
        return 0

    connection = None
    try:
        connection = pymysql.connect(
            host=os.getenv("DB_HOST", "127.0.0.1"),
            port=int(os.getenv("DB_PORT", "3307")),
            user=os.getenv("DB_USER", "bloodhound_app"),
            password=os.getenv("DB_PASSWORD", ""),
            database=os.getenv("DB_NAME", "bloodhound"),
            charset="utf8mb4",
            autocommit=False,
        )
        with connection.cursor() as cursor:
            cursor.executemany(INSERT_SQL, rows)
        connection.commit()
        print(f"Inserted {len(rows)} rows into bloodhound.bh_table "
              f"(capid={args.capid}, procid={args.procid}).")
    except Exception as exc:
        if connection:
            connection.rollback()
        print(f"Import failed; transaction rolled back: {exc}", file=sys.stderr)
        return 1
    finally:
        if connection:
            connection.close()
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
