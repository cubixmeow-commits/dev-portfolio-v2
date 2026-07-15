-- Rally schema, SQLite dialect. Kept in lockstep with schema.mysql.sql.
-- Timestamps are UTC 'YYYY-MM-DD HH:MM:SS' strings everywhere.
-- Every table uses the rly_ prefix.

CREATE TABLE IF NOT EXISTS rly_users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT NOT NULL,
    username      TEXT NOT NULL UNIQUE,
    email         TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role          TEXT NOT NULL DEFAULT 'user',
    simulation    INTEGER NOT NULL DEFAULT 0,
    timezone      TEXT NOT NULL DEFAULT 'UTC',
    status        TEXT NOT NULL DEFAULT 'active',
    avatar_url    TEXT NULL,
    created_at    TEXT NOT NULL,
    updated_at    TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS rly_rate_events (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    event_key  TEXT NOT NULL,
    created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_rly_rate_events_key ON rly_rate_events (event_key, created_at);

CREATE TABLE IF NOT EXISTS rly_metric_types (
    id                     INTEGER PRIMARY KEY AUTOINCREMENT,
    slug                   TEXT NOT NULL UNIQUE,
    name                   TEXT NOT NULL,
    unit                   TEXT NOT NULL,
    display_unit           TEXT NULL,
    classification         TEXT NOT NULL DEFAULT 'performance',
    scoring_strategy       TEXT NOT NULL DEFAULT 'daily_wins',
    higher_wins            INTEGER NOT NULL DEFAULT 1,
    default_length_days    INTEGER NOT NULL DEFAULT 14,
    default_tie_threshold  INTEGER NOT NULL DEFAULT 100,
    description            TEXT NOT NULL DEFAULT '',
    context_note           TEXT NULL,
    is_active              INTEGER NOT NULL DEFAULT 1,
    created_at             TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS rly_data_sources (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    slug         TEXT NOT NULL UNIQUE,
    name         TEXT NOT NULL,
    source_class TEXT NOT NULL,
    is_active    INTEGER NOT NULL DEFAULT 1,
    created_at   TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS rly_matches (
    id                      INTEGER PRIMARY KEY AUTOINCREMENT,
    metric_type_id          INTEGER NOT NULL,
    player_a_user_id        INTEGER NOT NULL,
    player_b_user_id        INTEGER NOT NULL,
    player_a_source_id      INTEGER NOT NULL,
    player_b_source_id      INTEGER NULL,
    created_by_user_id      INTEGER NOT NULL,
    start_date              TEXT NOT NULL,
    length_days             INTEGER NOT NULL DEFAULT 14,
    timezone                TEXT NOT NULL,
    tie_threshold           INTEGER NOT NULL DEFAULT 100,
    competition_type        TEXT NOT NULL DEFAULT 'classic',
    baseline_tie_threshold  REAL NULL,
    status                  TEXT NOT NULL DEFAULT 'invited',
    invitation_status       TEXT NOT NULL DEFAULT 'pending',
    created_at              TEXT NOT NULL,
    updated_at              TEXT NOT NULL,
    completed_at            TEXT NULL,
    FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id),
    FOREIGN KEY (player_a_user_id) REFERENCES rly_users(id),
    FOREIGN KEY (player_b_user_id) REFERENCES rly_users(id),
    FOREIGN KEY (player_a_source_id) REFERENCES rly_data_sources(id),
    FOREIGN KEY (player_b_source_id) REFERENCES rly_data_sources(id),
    FOREIGN KEY (created_by_user_id) REFERENCES rly_users(id)
);
CREATE INDEX IF NOT EXISTS idx_rly_matches_player_a ON rly_matches (player_a_user_id);
CREATE INDEX IF NOT EXISTS idx_rly_matches_player_b ON rly_matches (player_b_user_id);
CREATE INDEX IF NOT EXISTS idx_rly_matches_status ON rly_matches (status);

CREATE TABLE IF NOT EXISTS rly_match_days (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    match_id         INTEGER NOT NULL,
    day_number       INTEGER NOT NULL,
    competition_date TEXT NOT NULL,
    status           TEXT NOT NULL DEFAULT 'scheduled',
    settles_at       TEXT NOT NULL,
    official_at      TEXT NULL,
    created_at       TEXT NOT NULL,
    updated_at       TEXT NOT NULL,
    UNIQUE (match_id, day_number),
    UNIQUE (match_id, competition_date),
    FOREIGN KEY (match_id) REFERENCES rly_matches(id) ON DELETE CASCADE
);
CREATE INDEX IF NOT EXISTS idx_rly_match_days_status ON rly_match_days (status);

CREATE TABLE IF NOT EXISTS rly_match_day_results (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    match_day_id      INTEGER NOT NULL,
    user_id           INTEGER NOT NULL,
    data_source_id    INTEGER NOT NULL,
    metric_value      INTEGER NOT NULL,
    is_manual         INTEGER NOT NULL DEFAULT 0,
    ingested_at       TEXT NOT NULL,
    source_record_key TEXT NULL,
    created_at        TEXT NOT NULL,
    updated_at        TEXT NOT NULL,
    UNIQUE (match_day_id, user_id),
    FOREIGN KEY (match_day_id) REFERENCES rly_match_days(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES rly_users(id),
    FOREIGN KEY (data_source_id) REFERENCES rly_data_sources(id)
);
CREATE INDEX IF NOT EXISTS idx_rly_results_source_key ON rly_match_day_results (source_record_key);

CREATE TABLE IF NOT EXISTS rly_user_metric_days (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id           INTEGER NOT NULL,
    metric_type_id    INTEGER NOT NULL,
    data_source_id    INTEGER NOT NULL,
    observation_date  TEXT NOT NULL,
    metric_value      INTEGER NOT NULL,
    is_manual         INTEGER NOT NULL DEFAULT 0,
    source_record_key TEXT NULL,
    source_timezone   TEXT NULL,
    observed_at       TEXT NULL,
    ingested_at       TEXT NOT NULL,
    created_at        TEXT NOT NULL,
    updated_at        TEXT NOT NULL,
    UNIQUE (user_id, metric_type_id, data_source_id, observation_date),
    FOREIGN KEY (user_id) REFERENCES rly_users(id),
    FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id),
    FOREIGN KEY (data_source_id) REFERENCES rly_data_sources(id)
);
CREATE INDEX IF NOT EXISTS idx_rly_umd_user_metric_date
    ON rly_user_metric_days (user_id, metric_type_id, observation_date);
CREATE INDEX IF NOT EXISTS idx_rly_umd_source_key
    ON rly_user_metric_days (source_record_key);

CREATE TABLE IF NOT EXISTS rly_match_baselines (
    id                           INTEGER PRIMARY KEY AUTOINCREMENT,
    match_id                     INTEGER NOT NULL,
    user_id                      INTEGER NOT NULL,
    metric_type_id               INTEGER NOT NULL,
    baseline_mean                REAL NOT NULL,
    baseline_median              REAL NOT NULL,
    baseline_standard_deviation  REAL NOT NULL,
    baseline_minimum             INTEGER NOT NULL,
    baseline_maximum             INTEGER NOT NULL,
    sample_count                 INTEGER NOT NULL,
    window_start_date            TEXT NOT NULL,
    window_end_date              TEXT NOT NULL,
    calculated_at                TEXT NOT NULL,
    created_at                   TEXT NOT NULL,
    UNIQUE (match_id, user_id),
    FOREIGN KEY (match_id) REFERENCES rly_matches(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES rly_users(id),
    FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id)
);
