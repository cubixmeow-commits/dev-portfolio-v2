-- =====================================================================
-- Helpdesk Ops Toolkit — SQLite schema (LOCAL DEMO ONLY)
-- MySQL is the canonical target (see schema.sql). This mirror exists so
-- the PHP app can be run and screenshotted locally without a MySQL server
-- (ENUMs become TEXT; the app validates the allowed values in PHP).
-- Same `hot_` table prefix. Build with: php db/make_demo_db.php
-- =====================================================================

DROP TABLE IF EXISTS hot_tickets;
DROP TABLE IF EXISTS hot_assets;
DROP TABLE IF EXISTS hot_kb_articles;
DROP TABLE IF EXISTS hot_users;
DROP TABLE IF EXISTS hot_departments;

CREATE TABLE hot_departments (
  id   INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE hot_users (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  name          TEXT NOT NULL,
  role          TEXT NOT NULL DEFAULT 'staff',
  department_id INTEGER NULL
);

CREATE TABLE hot_tickets (
  id                   INTEGER PRIMARY KEY AUTOINCREMENT,
  title                TEXT NOT NULL,
  description          TEXT NOT NULL,
  category             TEXT NOT NULL DEFAULT 'other',
  priority             TEXT NOT NULL DEFAULT 'medium',
  status               TEXT NOT NULL DEFAULT 'open',
  requester_name       TEXT NOT NULL,
  requester_department TEXT NOT NULL,
  assigned_to          TEXT NULL,
  created_at           TEXT NOT NULL,
  resolved_at          TEXT NULL
);

CREATE TABLE hot_assets (
  id                  INTEGER PRIMARY KEY AUTOINCREMENT,
  asset_tag           TEXT NOT NULL UNIQUE,
  type                TEXT NOT NULL DEFAULT 'laptop',
  make_model          TEXT NOT NULL,
  assigned_to         TEXT NULL,
  assigned_department TEXT NULL,
  status              TEXT NOT NULL DEFAULT 'in_use',
  acquired_date       TEXT NULL
);

CREATE TABLE hot_kb_articles (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  title      TEXT NOT NULL,
  body       TEXT NOT NULL,
  category   TEXT NOT NULL DEFAULT 'general',
  audience   TEXT NOT NULL DEFAULT 'end_user',
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL
);
