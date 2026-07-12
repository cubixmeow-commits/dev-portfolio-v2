-- SousMeow schema, SQLite dialect. Kept in lockstep with
-- schema.mysql.sql; scripts/seed.php picks the file for the configured
-- driver. Timestamps are UTC 'YYYY-MM-DD HH:MM:SS' strings everywhere.

CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT NOT NULL,
    email         TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role          TEXT NOT NULL DEFAULT 'user',
    created_at    TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS rate_events (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    event_key  TEXT NOT NULL,
    created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_rate_events_key ON rate_events (event_key, created_at);

CREATE TABLE IF NOT EXISTS cookbooks (
    id                   INTEGER PRIMARY KEY AUTOINCREMENT,
    slug                 TEXT NOT NULL UNIQUE,
    title                TEXT NOT NULL,
    tagline              TEXT NOT NULL,
    description          TEXT NOT NULL,
    category             TEXT NOT NULL,
    audience             TEXT NOT NULL,
    outcome              TEXT NOT NULL,
    price_cents          INTEGER,              -- NULL means free
    is_executable        INTEGER NOT NULL DEFAULT 0,
    status               TEXT NOT NULL DEFAULT 'coming_soon', -- 'available' | 'coming_soon'
    accent               TEXT NOT NULL DEFAULT 'terracotta',
    difficulty           TEXT NOT NULL DEFAULT 'Intermediate', -- Beginner | Intermediate | Advanced
    est_minutes          INTEGER NOT NULL DEFAULT 20,
    demo_completed_runs  INTEGER NOT NULL DEFAULT 0,
    demo_avg_rating      REAL,
    sort_order           INTEGER NOT NULL DEFAULT 100,
    created_at           TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS cookbook_stages (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    cookbook_id INTEGER NOT NULL REFERENCES cookbooks(id) ON DELETE CASCADE,
    position    INTEGER NOT NULL,
    title       TEXT NOT NULL,
    summary     TEXT NOT NULL DEFAULT '',
    UNIQUE (cookbook_id, position)
);

CREATE TABLE IF NOT EXISTS recipes (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    cookbook_id     INTEGER NOT NULL REFERENCES cookbooks(id) ON DELETE CASCADE,
    stage_position  INTEGER,
    position        INTEGER NOT NULL,
    slug            TEXT NOT NULL,
    title           TEXT NOT NULL,
    summary         TEXT NOT NULL,
    why_it_matters  TEXT NOT NULL DEFAULT '',
    unlocks_text    TEXT NOT NULL DEFAULT '',
    prompt_template TEXT,                -- NULL for preview-only recipes
    example_response TEXT,               -- one realistic sample per runnable recipe
    est_minutes     INTEGER NOT NULL DEFAULT 5,
    UNIQUE (cookbook_id, position),
    UNIQUE (cookbook_id, slug)
);

CREATE TABLE IF NOT EXISTS recipe_checks (
    id        INTEGER PRIMARY KEY AUTOINCREMENT,
    recipe_id INTEGER NOT NULL REFERENCES recipes(id) ON DELETE CASCADE,
    position  INTEGER NOT NULL,
    label     TEXT NOT NULL,
    help      TEXT NOT NULL DEFAULT '',
    UNIQUE (recipe_id, position)
);

CREATE TABLE IF NOT EXISTS pantry_fields (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    cookbook_id  INTEGER NOT NULL REFERENCES cookbooks(id) ON DELETE CASCADE,
    position     INTEGER NOT NULL,
    field_key    TEXT NOT NULL,
    label        TEXT NOT NULL,
    type         TEXT NOT NULL,          -- text | textarea | select | multiselect | number | url
    help         TEXT NOT NULL DEFAULT '',
    placeholder  TEXT NOT NULL DEFAULT '',
    options      TEXT,                   -- JSON array for select/multiselect
    required     INTEGER NOT NULL DEFAULT 1,
    sample_value TEXT NOT NULL DEFAULT '',
    UNIQUE (cookbook_id, field_key),
    UNIQUE (cookbook_id, position)
);

CREATE TABLE IF NOT EXISTS projects (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id         INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    cookbook_id     INTEGER NOT NULL REFERENCES cookbooks(id),
    title           TEXT NOT NULL,
    pantry_saved_at TEXT,
    completed_at    TEXT,
    created_at      TEXT NOT NULL,
    updated_at      TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_projects_user ON projects (user_id, updated_at);

CREATE TABLE IF NOT EXISTS pantry_values (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id INTEGER NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
    field_id   INTEGER NOT NULL REFERENCES pantry_fields(id) ON DELETE CASCADE,
    value      TEXT NOT NULL DEFAULT '',
    UNIQUE (project_id, field_id)
);

CREATE TABLE IF NOT EXISTS artifacts (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id          INTEGER NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
    recipe_id           INTEGER NOT NULL REFERENCES recipes(id),
    status              TEXT NOT NULL DEFAULT 'review',  -- 'review' | 'approved'
    approved_version_id INTEGER,
    created_at          TEXT NOT NULL,
    updated_at          TEXT NOT NULL,
    UNIQUE (project_id, recipe_id)
);

CREATE TABLE IF NOT EXISTS artifact_versions (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    artifact_id INTEGER NOT NULL REFERENCES artifacts(id) ON DELETE CASCADE,
    version_no  INTEGER NOT NULL,
    content     TEXT NOT NULL,
    source      TEXT NOT NULL,           -- pasted | example | edited | restored
    created_at  TEXT NOT NULL,
    UNIQUE (artifact_id, version_no)
);

-- A row here means the human confirmed this Quality Check against this
-- exact version. New versions start unconfirmed by construction.
CREATE TABLE IF NOT EXISTS artifact_checks (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    version_id INTEGER NOT NULL REFERENCES artifact_versions(id) ON DELETE CASCADE,
    check_id   INTEGER NOT NULL REFERENCES recipe_checks(id) ON DELETE CASCADE,
    created_at TEXT NOT NULL,
    UNIQUE (version_id, check_id)
);

CREATE TABLE IF NOT EXISTS exports (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    project_id     INTEGER NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
    filename       TEXT NOT NULL,
    file_size      INTEGER NOT NULL DEFAULT 0,
    artifact_count INTEGER NOT NULL DEFAULT 0,
    created_at     TEXT NOT NULL
);
