-- Rally schema, MySQL dialect (Hostinger). Kept in lockstep with
-- schema.sqlite.sql; scripts/seed.php picks the file for the configured
-- driver. Timestamps are UTC 'YYYY-MM-DD HH:MM:SS' strings everywhere.
-- Every table uses the rly_ prefix.

CREATE TABLE IF NOT EXISTS rly_users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(120) NOT NULL,
    username      VARCHAR(60) NOT NULL,
    email         VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          VARCHAR(20) NOT NULL DEFAULT 'user',
    simulation    TINYINT(1) NOT NULL DEFAULT 0,
    timezone      VARCHAR(64) NOT NULL DEFAULT 'UTC',
    status        VARCHAR(20) NOT NULL DEFAULT 'active',
    avatar_url    VARCHAR(255) NULL,
    created_at    DATETIME NOT NULL,
    updated_at    DATETIME NOT NULL,
    UNIQUE KEY uq_rly_users_email (email),
    UNIQUE KEY uq_rly_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rly_rate_events (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    event_key  VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    KEY idx_rly_rate_events_key (event_key, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rly_metric_types (
    id                     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug                   VARCHAR(60) NOT NULL,
    name                   VARCHAR(120) NOT NULL,
    unit                   VARCHAR(40) NOT NULL,
    display_unit           VARCHAR(40) NULL,
    classification         VARCHAR(40) NOT NULL DEFAULT 'performance',
    scoring_strategy       VARCHAR(40) NOT NULL DEFAULT 'daily_wins',
    higher_wins            TINYINT(1) NOT NULL DEFAULT 1,
    default_length_days    INT UNSIGNED NOT NULL DEFAULT 14,
    default_tie_threshold  INT UNSIGNED NOT NULL DEFAULT 100,
    description            TEXT NOT NULL,
    context_note           TEXT NULL,
    is_active              TINYINT(1) NOT NULL DEFAULT 1,
    created_at             DATETIME NOT NULL,
    UNIQUE KEY uq_rly_metric_types_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rly_data_sources (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug         VARCHAR(60) NOT NULL,
    name         VARCHAR(120) NOT NULL,
    source_class VARCHAR(40) NOT NULL,
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    created_at   DATETIME NOT NULL,
    UNIQUE KEY uq_rly_data_sources_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rly_matches (
    id                  INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    metric_type_id      INT UNSIGNED NOT NULL,
    player_a_user_id    INT UNSIGNED NOT NULL,
    player_b_user_id    INT UNSIGNED NOT NULL,
    player_a_source_id  INT UNSIGNED NOT NULL,
    player_b_source_id  INT UNSIGNED NULL,
    created_by_user_id  INT UNSIGNED NOT NULL,
    start_date          DATE NOT NULL,
    length_days         INT UNSIGNED NOT NULL DEFAULT 14,
    timezone            VARCHAR(64) NOT NULL,
    tie_threshold       INT UNSIGNED NOT NULL DEFAULT 100,
    status              VARCHAR(20) NOT NULL DEFAULT 'invited',
    invitation_status   VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at          DATETIME NOT NULL,
    updated_at          DATETIME NOT NULL,
    completed_at        DATETIME NULL,
    KEY idx_rly_matches_player_a (player_a_user_id),
    KEY idx_rly_matches_player_b (player_b_user_id),
    KEY idx_rly_matches_status (status),
    CONSTRAINT fk_rly_matches_metric FOREIGN KEY (metric_type_id) REFERENCES rly_metric_types(id),
    CONSTRAINT fk_rly_matches_player_a FOREIGN KEY (player_a_user_id) REFERENCES rly_users(id),
    CONSTRAINT fk_rly_matches_player_b FOREIGN KEY (player_b_user_id) REFERENCES rly_users(id),
    CONSTRAINT fk_rly_matches_source_a FOREIGN KEY (player_a_source_id) REFERENCES rly_data_sources(id),
    CONSTRAINT fk_rly_matches_source_b FOREIGN KEY (player_b_source_id) REFERENCES rly_data_sources(id),
    CONSTRAINT fk_rly_matches_created_by FOREIGN KEY (created_by_user_id) REFERENCES rly_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rly_match_days (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    match_id         INT UNSIGNED NOT NULL,
    day_number       INT UNSIGNED NOT NULL,
    competition_date DATE NOT NULL,
    status           VARCHAR(20) NOT NULL DEFAULT 'scheduled',
    settles_at       DATETIME NOT NULL,
    official_at      DATETIME NULL,
    created_at       DATETIME NOT NULL,
    updated_at       DATETIME NOT NULL,
    UNIQUE KEY uq_rly_match_days_match_day (match_id, day_number),
    UNIQUE KEY uq_rly_match_days_match_date (match_id, competition_date),
    KEY idx_rly_match_days_status (status),
    CONSTRAINT fk_rly_match_days_match FOREIGN KEY (match_id) REFERENCES rly_matches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rly_match_day_results (
    id                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    match_day_id      INT UNSIGNED NOT NULL,
    user_id           INT UNSIGNED NOT NULL,
    data_source_id    INT UNSIGNED NOT NULL,
    metric_value      INT UNSIGNED NOT NULL,
    is_manual         TINYINT(1) NOT NULL DEFAULT 0,
    ingested_at       DATETIME NOT NULL,
    source_record_key VARCHAR(190) NULL,
    created_at        DATETIME NOT NULL,
    updated_at        DATETIME NOT NULL,
    UNIQUE KEY uq_rly_results_day_user (match_day_id, user_id),
    KEY idx_rly_results_source_key (source_record_key),
    CONSTRAINT fk_rly_results_day FOREIGN KEY (match_day_id) REFERENCES rly_match_days(id) ON DELETE CASCADE,
    CONSTRAINT fk_rly_results_user FOREIGN KEY (user_id) REFERENCES rly_users(id),
    CONSTRAINT fk_rly_results_source FOREIGN KEY (data_source_id) REFERENCES rly_data_sources(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
