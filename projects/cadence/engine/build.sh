#!/bin/sh
# Build cadence-engine.jar with nothing but javac and jar: no Maven,
# no Gradle, so the jar can be rebuilt on any box with a JDK 17+.
# The vendored MySQL Connector/J is unpacked into the jar so the
# result is a single runnable artifact.
set -eu

cd "$(dirname "$0")"

SRC=src/main/java
RES=src/main/resources
LIB=lib/mysql-connector-j-8.4.0.jar
OUT=build/classes
JAR=build/cadence-engine.jar

echo "Compiling..."
rm -rf "$OUT"
mkdir -p "$OUT"
javac --release 17 -encoding UTF-8 -cp "$LIB" -d "$OUT" \
  $(find "$SRC" -name '*.java')

echo "Bundling resources and the JDBC driver..."
cp "$RES"/*.txt "$OUT"/
# Unpack the connector inside the class output so one jar runs alone.
(cd "$OUT" && jar xf "../../$LIB")
rm -rf "$OUT/META-INF/MANIFEST.MF" "$OUT/META-INF"/*.SF "$OUT/META-INF"/*.RSA 2>/dev/null || true

echo "Packaging $JAR..."
jar cfe "$JAR" com.cadence.engine.cli.Main -C "$OUT" .

echo "Done: $(ls -lh "$JAR" | awk '{print $5}') $JAR"
