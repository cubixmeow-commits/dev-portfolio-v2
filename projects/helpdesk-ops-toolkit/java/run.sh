#!/usr/bin/env bash
# Convenience runner for the weekly report. Set DB connection via env if the
# defaults don't match your setup:
#   HOT_DB_URL="jdbc:mysql://127.0.0.1:3306/helpdesk_ops" \
#   HOT_DB_USER=root HOT_DB_PASS=secret ./run.sh
set -euo pipefail
cd "$(dirname "$0")"

JAR="target/ticket-report.jar"
if [[ ! -f "$JAR" ]]; then
  echo "$JAR not found. Build it first with ./build.sh"
  exit 1
fi
exec java -jar "$JAR" --range weekly
