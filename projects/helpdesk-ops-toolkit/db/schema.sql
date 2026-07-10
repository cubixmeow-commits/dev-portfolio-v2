-- =====================================================================
-- Helpdesk Ops Toolkit — MySQL schema (v1)
-- All tables use the project-unique prefix `hot_` so this project can
-- share a database with other portfolio projects without collision.
-- Import:  mysql -u USER -p DBNAME < db/schema.sql
-- =====================================================================

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS hot_tickets;
DROP TABLE IF EXISTS hot_assets;
DROP TABLE IF EXISTS hot_kb_articles;
DROP TABLE IF EXISTS hot_users;
DROP TABLE IF EXISTS hot_departments;

SET foreign_key_checks = 1;

-- ---------------------------------------------------------------------
-- Departments — generic on purpose (department-agnostic demo).
-- ---------------------------------------------------------------------
CREATE TABLE hot_departments (
  id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name  VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_dept_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Users — staff, support agents, admins.
-- ---------------------------------------------------------------------
CREATE TABLE hot_users (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(80) NOT NULL,
  role          ENUM('staff','support_agent','admin') NOT NULL DEFAULT 'staff',
  department_id INT UNSIGNED NULL,
  PRIMARY KEY (id),
  KEY idx_users_role (role),
  CONSTRAINT fk_users_dept FOREIGN KEY (department_id)
    REFERENCES hot_departments (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Tickets — the core support queue.
-- ---------------------------------------------------------------------
CREATE TABLE hot_tickets (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title                VARCHAR(160) NOT NULL,
  description          TEXT NOT NULL,
  category             ENUM('hardware','software','network','account_access','other') NOT NULL DEFAULT 'other',
  priority             ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  status               ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  requester_name       VARCHAR(80) NOT NULL,
  requester_department VARCHAR(80) NOT NULL,
  assigned_to          VARCHAR(80) NULL,
  created_at           DATETIME NOT NULL,
  resolved_at          DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_tickets_status (status),
  KEY idx_tickets_category (category),
  KEY idx_tickets_priority (priority),
  KEY idx_tickets_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Assets — equipment inventory with a surplus/retire lifecycle.
-- ---------------------------------------------------------------------
CREATE TABLE hot_assets (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  asset_tag           VARCHAR(40) NOT NULL,
  type                ENUM('laptop','desktop','monitor','peripheral') NOT NULL DEFAULT 'laptop',
  make_model          VARCHAR(120) NOT NULL,
  assigned_to         VARCHAR(80) NULL,
  assigned_department VARCHAR(80) NULL,
  status              ENUM('in_use','surplus','repair','retired') NOT NULL DEFAULT 'in_use',
  acquired_date       DATE NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_asset_tag (asset_tag),
  KEY idx_assets_status (status),
  KEY idx_assets_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Knowledge base — end-user and technical articles (markdown body).
-- ---------------------------------------------------------------------
CREATE TABLE hot_kb_articles (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title      VARCHAR(160) NOT NULL,
  body       MEDIUMTEXT NOT NULL,
  category   VARCHAR(60) NOT NULL DEFAULT 'general',
  audience   ENUM('end_user','technical') NOT NULL DEFAULT 'end_user',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_kb_audience (audience),
  KEY idx_kb_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
