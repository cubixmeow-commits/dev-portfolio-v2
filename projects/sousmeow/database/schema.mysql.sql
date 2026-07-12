-- SousMeow schema, MySQL dialect (Hostinger). Kept in lockstep with
-- schema.sqlite.sql; scripts/seed.php picks the file for the configured
-- driver. Timestamps are UTC 'YYYY-MM-DD HH:MM:SS' strings everywhere.

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(120) NOT NULL,
    email         VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at    DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rate_events (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    event_key  VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL,
    KEY idx_rate_events_key (event_key, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cookbooks (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug           VARCHAR(120) NOT NULL UNIQUE,
    title          VARCHAR(190) NOT NULL,
    tagline        VARCHAR(255) NOT NULL,
    description    TEXT NOT NULL,
    category       VARCHAR(60) NOT NULL,
    audience       VARCHAR(255) NOT NULL,
    outcome        VARCHAR(255) NOT NULL,
    price_cents    INT UNSIGNED NULL,
    is_executable  TINYINT(1) NOT NULL DEFAULT 0,
    status         VARCHAR(20) NOT NULL DEFAULT 'coming_soon',
    accent         VARCHAR(20) NOT NULL DEFAULT 'terracotta',
    est_minutes    INT UNSIGNED NOT NULL DEFAULT 20,
    sort_order     INT UNSIGNED NOT NULL DEFAULT 100,
    created_at     DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS recipes (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cookbook_id      INT UNSIGNED NOT NULL,
    position         INT UNSIGNED NOT NULL,
    slug             VARCHAR(120) NOT NULL,
    title            VARCHAR(190) NOT NULL,
    summary          VARCHAR(255) NOT NULL,
    why_it_matters   TEXT NOT NULL,
    unlocks_text     VARCHAR(255) NOT NULL DEFAULT '',
    prompt_template  MEDIUMTEXT NULL,
    example_response MEDIUMTEXT NULL,
    est_minutes      INT UNSIGNED NOT NULL DEFAULT 5,
    UNIQUE KEY uq_recipes_pos (cookbook_id, position),
    UNIQUE KEY uq_recipes_slug (cookbook_id, slug),
    CONSTRAINT fk_recipes_cookbook FOREIGN KEY (cookbook_id) REFERENCES cookbooks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS recipe_checks (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT UNSIGNED NOT NULL,
    position  INT UNSIGNED NOT NULL,
    label     VARCHAR(190) NOT NULL,
    help      VARCHAR(255) NOT NULL DEFAULT '',
    UNIQUE KEY uq_checks_pos (recipe_id, position),
    CONSTRAINT fk_checks_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pantry_fields (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cookbook_id  INT UNSIGNED NOT NULL,
    position     INT UNSIGNED NOT NULL,
    field_key    VARCHAR(60) NOT NULL,
    label        VARCHAR(120) NOT NULL,
    type         VARCHAR(20) NOT NULL,
    help         VARCHAR(255) NOT NULL DEFAULT '',
    placeholder  VARCHAR(190) NOT NULL DEFAULT '',
    options      TEXT NULL,
    required     TINYINT(1) NOT NULL DEFAULT 1,
    sample_value TEXT NOT NULL,
    UNIQUE KEY uq_fields_key (cookbook_id, field_key),
    UNIQUE KEY uq_fields_pos (cookbook_id, position),
    CONSTRAINT fk_fields_cookbook FOREIGN KEY (cookbook_id) REFERENCES cookbooks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NOT NULL,
    cookbook_id     INT UNSIGNED NOT NULL,
    title           VARCHAR(190) NOT NULL,
    pantry_saved_at DATETIME NULL,
    completed_at    DATETIME NULL,
    created_at      DATETIME NOT NULL,
    updated_at      DATETIME NOT NULL,
    KEY idx_projects_user (user_id, updated_at),
    CONSTRAINT fk_projects_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_projects_cookbook FOREIGN KEY (cookbook_id) REFERENCES cookbooks(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pantry_values (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    project_id INT UNSIGNED NOT NULL,
    field_id   INT UNSIGNED NOT NULL,
    value      TEXT NOT NULL,
    UNIQUE KEY uq_pantry_value (project_id, field_id),
    CONSTRAINT fk_values_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_values_field FOREIGN KEY (field_id) REFERENCES pantry_fields(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS artifacts (
    id                  INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    project_id          INT UNSIGNED NOT NULL,
    recipe_id           INT UNSIGNED NOT NULL,
    status              VARCHAR(20) NOT NULL DEFAULT 'review',
    approved_version_id INT UNSIGNED NULL,
    created_at          DATETIME NOT NULL,
    updated_at          DATETIME NOT NULL,
    UNIQUE KEY uq_artifact (project_id, recipe_id),
    CONSTRAINT fk_artifacts_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_artifacts_recipe FOREIGN KEY (recipe_id) REFERENCES recipes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS artifact_versions (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    artifact_id INT UNSIGNED NOT NULL,
    version_no  INT UNSIGNED NOT NULL,
    content     MEDIUMTEXT NOT NULL,
    source      VARCHAR(20) NOT NULL,
    created_at  DATETIME NOT NULL,
    UNIQUE KEY uq_version_no (artifact_id, version_no),
    CONSTRAINT fk_versions_artifact FOREIGN KEY (artifact_id) REFERENCES artifacts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- A row here means the human confirmed this Quality Check against this
-- exact version. New versions start unconfirmed by construction.
CREATE TABLE IF NOT EXISTS artifact_checks (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    version_id INT UNSIGNED NOT NULL,
    check_id   INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    UNIQUE KEY uq_artifact_check (version_id, check_id),
    CONSTRAINT fk_ac_version FOREIGN KEY (version_id) REFERENCES artifact_versions(id) ON DELETE CASCADE,
    CONSTRAINT fk_ac_check FOREIGN KEY (check_id) REFERENCES recipe_checks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exports (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    project_id     INT UNSIGNED NOT NULL,
    filename       VARCHAR(190) NOT NULL,
    file_size      INT UNSIGNED NOT NULL DEFAULT 0,
    artifact_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at     DATETIME NOT NULL,
    CONSTRAINT fk_exports_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
