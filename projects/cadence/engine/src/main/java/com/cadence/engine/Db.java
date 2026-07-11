package com.cadence.engine;

import java.io.FileInputStream;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Properties;

/**
 * JDBC connection factory. Reads engine.properties (db.host, db.port,
 * db.name, db.user, db.password) from an explicit path or from the
 * default locations relative to the working directory and the jar.
 */
public final class Db {

    private final Properties props;

    private Db(Properties props) {
        this.props = props;
    }

    public static Db fromConfig(String explicitPath) throws IOException {
        Path path = resolve(explicitPath);
        if (path == null) {
            throw new IOException(
                "engine.properties not found. Pass --db-config=/path/to/engine.properties "
                + "or create config/engine.properties (see config/engine.example.properties).");
        }
        Properties props = new Properties();
        try (FileInputStream in = new FileInputStream(path.toFile())) {
            props.load(in);
        }
        for (String key : new String[] {"db.host", "db.name", "db.user", "db.password"}) {
            if (props.getProperty(key) == null) {
                throw new IOException("engine.properties is missing required key: " + key);
            }
        }
        return new Db(props);
    }

    private static Path resolve(String explicitPath) {
        if (explicitPath != null && !explicitPath.isBlank()) {
            Path p = Paths.get(explicitPath);
            return Files.isRegularFile(p) ? p : null;
        }
        Path[] candidates = {
            Paths.get("config/engine.properties"),
            Paths.get("../config/engine.properties"),
            jarRelative("../../config/engine.properties"),
        };
        for (Path p : candidates) {
            if (p != null && Files.isRegularFile(p)) {
                return p;
            }
        }
        return null;
    }

    private static Path jarRelative(String relative) {
        try {
            Path jar = Paths.get(Db.class.getProtectionDomain().getCodeSource().getLocation().toURI());
            return jar.getParent().resolve(relative).normalize();
        } catch (Exception e) {
            return null;
        }
    }

    public String property(String key, String fallback) {
        return props.getProperty(key, fallback);
    }

    public Connection connect() throws SQLException {
        String url = String.format(
            "jdbc:mysql://%s:%s/%s?useSSL=false&allowPublicKeyRetrieval=true"
            + "&rewriteBatchedStatements=true&connectionTimeZone=UTC&characterEncoding=utf8",
            props.getProperty("db.host"),
            props.getProperty("db.port", "3306"),
            props.getProperty("db.name"));
        return DriverManager.getConnection(url, props.getProperty("db.user"), props.getProperty("db.password"));
    }
}
