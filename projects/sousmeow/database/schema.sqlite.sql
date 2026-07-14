-- SousMeow schema, SQLite dialect. Kept in lockstep with
-- schema.mysql.sql; scripts/seed.php picks the file for the configured
-- driver. Timestamps are UTC 'YYYY-MM-DD HH:MM:SS' strings everywhere.

CREATE TABLE IF NOT EXISTS users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT NOT NULL,
    email         TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    role          TEXT NOT NULL DEFAULT 'user',
    simulation    INTEGER NOT NULL DEFAULT 0,
    email_verified_at        TEXT NULL,
    verification_token_hash  TEXT NULL,
    verification_expires_at  TEXT NULL,
    verification_sent_at     TEXT NULL,
    pending_email            TEXT NULL,
    pending_email_token_hash TEXT NULL,
    pending_email_expires_at TEXT NULL,
    password_changed_at      TEXT NULL,
    onboarding_completed_at  TEXT NULL,
    bio                      TEXT NULL,
    website                  TEXT NULL,
    avatar_url               TEXT NULL,
    preferred_ai             TEXT NULL,
    ai_experience_level      TEXT NULL,
    timezone                 TEXT NULL,
    theme_preference         TEXT NULL DEFAULT 'system',
    created_at    TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token_hash TEXT NOT NULL,
    expires_at TEXT NOT NULL,
    used_at    TEXT NULL,
    created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_reset_user ON password_reset_tokens (user_id);
CREATE INDEX IF NOT EXISTS idx_reset_hash ON password_reset_tokens (token_hash);

CREATE TABLE IF NOT EXISTS rate_events (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    event_key  TEXT NOT NULL,
    created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_rate_events_key ON rate_events (event_key, created_at);

-- Discovery taxonomy. Categories are the stable primary spine (one per
-- publicly visible Cookbook); Collections are flexible discovery views.
-- Declared before cookbooks so the primary_category_id foreign key
-- resolves on a fresh install. Table names carry a sousmeow_ prefix: the
-- production database is shared with other apps on the same hosting
-- account, and generic names like "categories" collide with tables those
-- apps already own. Every other SousMeow table predates this and has
-- never collided, so only these three are prefixed. Kept in lockstep here
-- even though local SQLite dev has no such collision, for consistency.
CREATE TABLE IF NOT EXISTS sousmeow_categories (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    slug          TEXT NOT NULL UNIQUE,
    name          TEXT NOT NULL,
    short_name    TEXT NULL,
    tagline       TEXT NOT NULL,
    description   TEXT NOT NULL,
    outcomes_json TEXT NOT NULL,                    -- JSON array of exactly three outcomes
    accent        TEXT NOT NULL DEFAULT 'terracotta', -- allowlisted key, never a hex
    icon_key      TEXT NULL,
    sort_order    INTEGER NOT NULL DEFAULT 0,
    is_visible    INTEGER NOT NULL DEFAULT 1,
    created_at    TEXT NOT NULL,
    updated_at    TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS sousmeow_collections (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    slug              TEXT NOT NULL UNIQUE,
    name              TEXT NOT NULL,
    tagline           TEXT NOT NULL,
    description       TEXT NOT NULL,
    accent            TEXT NOT NULL DEFAULT 'sage',
    collection_type   TEXT NOT NULL DEFAULT 'editorial', -- editorial | dynamic | attribute
    min_display_count INTEGER NOT NULL DEFAULT 1,
    sort_order        INTEGER NOT NULL DEFAULT 0,
    is_visible        INTEGER NOT NULL DEFAULT 1,
    created_at        TEXT NOT NULL,
    updated_at        TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS cookbooks (
    id                   INTEGER PRIMARY KEY AUTOINCREMENT,
    slug                 TEXT NOT NULL UNIQUE,
    title                TEXT NOT NULL,
    tagline              TEXT NOT NULL,
    description          TEXT NOT NULL,
    category             TEXT NOT NULL,        -- legacy display string, rollback only (not read after migration)
    primary_category_id  INTEGER NULL REFERENCES sousmeow_categories(id) ON DELETE SET NULL,
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
CREATE INDEX IF NOT EXISTS idx_cookbooks_primary_category ON cookbooks (primary_category_id);

CREATE TABLE IF NOT EXISTS sousmeow_cookbook_collections (
    cookbook_id   INTEGER NOT NULL REFERENCES cookbooks(id)             ON DELETE CASCADE,
    collection_id INTEGER NOT NULL REFERENCES sousmeow_collections(id)  ON DELETE CASCADE,
    position      INTEGER NOT NULL DEFAULT 0,
    is_featured   INTEGER NOT NULL DEFAULT 0,
    created_at    TEXT NOT NULL,
    PRIMARY KEY (cookbook_id, collection_id)
);
CREATE INDEX IF NOT EXISTS idx_cookbook_collections_collection ON sousmeow_cookbook_collections (collection_id, position);

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
    output_contract TEXT,                -- JSON section list the prompt requests (NULL = no contract)
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
    evidence_keys TEXT,                  -- JSON list of output_contract section keys (NULL = manual review)
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

CREATE TABLE IF NOT EXISTS simulation_runs (
    pacific_date   TEXT NOT NULL PRIMARY KEY,
    users_active   INTEGER NOT NULL DEFAULT 0,
    actions_count  INTEGER NOT NULL DEFAULT 0,
    executed_at    TEXT NOT NULL
);
