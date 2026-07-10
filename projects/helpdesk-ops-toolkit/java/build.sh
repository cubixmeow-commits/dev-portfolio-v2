#!/usr/bin/env bash
# Build the runnable ticket-report.jar.
#
# Preferred (bundles the MySQL driver):
#   ./build.sh              # uses Maven -> target/ticket-report.jar
#
# Maven fetches mysql-connector-j and the shade plugin from Maven Central.
set -euo pipefail
cd "$(dirname "$0")"

if command -v mvn >/dev/null 2>&1; then
  mvn -q clean package
  echo "Built: $(pwd)/target/ticket-report.jar"
  echo "Run:   java -jar target/ticket-report.jar --range weekly"
else
  echo "Maven not found. Manual compile (you must supply the driver at runtime):"
  echo "  javac -d out src/main/java/com/cubixmeow/helpdesk/TicketReportGenerator.java"
  echo "  java -cp out:/path/to/mysql-connector-j.jar com.cubixmeow.helpdesk.TicketReportGenerator --range weekly"
  exit 1
fi
