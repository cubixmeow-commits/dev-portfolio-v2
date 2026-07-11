-- Cadence schema
-- MySQL 8.x / MariaDB 10.6+, InnoDB, utf8mb4.
-- Idempotent: safe to re-run against an existing database. All drops are
-- guarded and foreign key checks are suspended for the drop phase only.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ops_runs;
DROP TABLE IF EXISTS rate_limits;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS activity_events;
DROP TABLE IF EXISTS user_badges;
DROP TABLE IF EXISTS badges;
DROP TABLE IF EXISTS check_ins;
DROP TABLE IF EXISTS challenge_participants;
DROP TABLE IF EXISTS challenges;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS email_verifications;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email           VARCHAR(255)    NOT NULL,
  password_hash   VARCHAR(255)    NOT NULL,
  display_name    VARCHAR(60)     NOT NULL,
  handle          VARCHAR(30)     NOT NULL,
  avatar_seed     VARCHAR(40)     NOT NULL,
  bio             VARCHAR(160)    NULL,
  timezone        VARCHAR(64)     NOT NULL DEFAULT 'America/Los_Angeles',
  total_points    INT             NOT NULL DEFAULT 0,
  role            ENUM('member','admin') NOT NULL DEFAULT 'member',
  email_verified_at DATETIME      NULL,
  is_demo         TINYINT(1)      NOT NULL DEFAULT 0,
  last_active_at  DATETIME        NULL,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_handle (handle),
  KEY idx_users_is_demo (is_demo),
  KEY idx_users_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE email_verifications (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED NOT NULL,
  token_hash  CHAR(64)        NOT NULL,
  expires_at  DATETIME        NOT NULL,
  consumed_at DATETIME        NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_email_verifications_token (token_hash),
  KEY idx_email_verifications_user (user_id),
  CONSTRAINT fk_email_verifications_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED NOT NULL,
  token_hash  CHAR(64)        NOT NULL,
  expires_at  DATETIME        NOT NULL,
  consumed_at DATETIME        NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_password_resets_token (token_hash),
  KEY idx_password_resets_user (user_id),
  CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DB-backed PHP sessions. The data column holds the serialized session
-- payload so the PHP session handler can be fully database backed; see
-- ARCHITECTURE.md for the reasoning.
CREATE TABLE sessions (
  id           CHAR(64)        NOT NULL,
  user_id      BIGINT UNSIGNED NULL,
  ip           VARCHAR(45)     NOT NULL DEFAULT '',
  user_agent   VARCHAR(255)    NOT NULL DEFAULT '',
  data         MEDIUMTEXT      NULL,
  last_seen_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_sessions_user (user_id),
  KEY idx_sessions_last_seen (last_seen_at),
  CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE challenges (
  id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  title              VARCHAR(80)     NOT NULL,
  slug               VARCHAR(90)     NOT NULL,
  description        TEXT            NOT NULL,
  category           ENUM('fitness','mindfulness','nutrition','learning','creativity','lifestyle') NOT NULL,
  cadence            ENUM('daily')   NOT NULL DEFAULT 'daily',
  points_per_checkin INT             NOT NULL DEFAULT 10,
  start_date         DATE            NOT NULL,
  end_date           DATE            NOT NULL,
  cover_style        VARCHAR(30)     NOT NULL DEFAULT 'spruce',
  created_by         BIGINT UNSIGNED NULL,
  participant_count  INT             NOT NULL DEFAULT 0,
  is_featured        TINYINT(1)      NOT NULL DEFAULT 0,
  created_at         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_challenges_slug (slug),
  KEY idx_challenges_category (category),
  KEY idx_challenges_dates (start_date, end_date),
  KEY idx_challenges_popularity (participant_count),
  CONSTRAINT fk_challenges_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE challenge_participants (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  challenge_id      BIGINT UNSIGNED NOT NULL,
  user_id           BIGINT UNSIGNED NOT NULL,
  current_streak    INT             NOT NULL DEFAULT 0,
  longest_streak    INT             NOT NULL DEFAULT 0,
  points            INT             NOT NULL DEFAULT 0,
  joined_at         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_checkin_date DATE            NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_participant (challenge_id, user_id),
  KEY idx_participants_user (user_id),
  KEY idx_participants_points (challenge_id, points),
  CONSTRAINT fk_participants_challenge FOREIGN KEY (challenge_id) REFERENCES challenges (id) ON DELETE CASCADE,
  CONSTRAINT fk_participants_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE check_ins (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  participant_id BIGINT UNSIGNED NOT NULL,
  user_id        BIGINT UNSIGNED NOT NULL,
  challenge_id   BIGINT UNSIGNED NOT NULL,
  checkin_date   DATE            NOT NULL,
  note           VARCHAR(200)    NULL,
  points_awarded INT             NOT NULL DEFAULT 0,
  created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_checkin_per_day (participant_id, checkin_date),
  KEY idx_checkins_user_date (user_id, checkin_date),
  KEY idx_checkins_challenge_date (challenge_id, checkin_date),
  KEY idx_checkins_date (checkin_date),
  KEY idx_checkins_created (created_at),
  CONSTRAINT fk_checkins_participant FOREIGN KEY (participant_id) REFERENCES challenge_participants (id) ON DELETE CASCADE,
  CONSTRAINT fk_checkins_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_checkins_challenge FOREIGN KEY (challenge_id) REFERENCES challenges (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE badges (
  id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code           VARCHAR(40)     NOT NULL,
  name           VARCHAR(60)     NOT NULL,
  description    VARCHAR(160)    NOT NULL,
  icon           VARCHAR(40)     NOT NULL,
  criteria_type  ENUM('streak','total_checkins','challenges_completed','points','challenges_joined') NOT NULL,
  criteria_value INT             NOT NULL,
  created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_badges_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_badges (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id      BIGINT UNSIGNED NOT NULL,
  badge_id     BIGINT UNSIGNED NOT NULL,
  challenge_id BIGINT UNSIGNED NULL,
  earned_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_user_badge (user_id, badge_id, challenge_id),
  KEY idx_user_badges_user (user_id, earned_at),
  CONSTRAINT fk_user_badges_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_user_badges_badge FOREIGN KEY (badge_id) REFERENCES badges (id) ON DELETE CASCADE,
  CONSTRAINT fk_user_badges_challenge FOREIGN KEY (challenge_id) REFERENCES challenges (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_events (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id      BIGINT UNSIGNED NOT NULL,
  type         ENUM('checkin','joined','badge','streak_milestone','challenge_completed') NOT NULL,
  challenge_id BIGINT UNSIGNED NULL,
  badge_id     BIGINT UNSIGNED NULL,
  meta         JSON            NULL,
  created_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_events_created (created_at),
  KEY idx_events_user_created (user_id, created_at),
  KEY idx_events_challenge_created (challenge_id, created_at),
  CONSTRAINT fk_events_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_events_challenge FOREIGN KEY (challenge_id) REFERENCES challenges (id) ON DELETE CASCADE,
  CONSTRAINT fk_events_badge FOREIGN KEY (badge_id) REFERENCES badges (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id    BIGINT UNSIGNED NOT NULL,
  type       VARCHAR(40)     NOT NULL,
  title      VARCHAR(120)    NOT NULL,
  body       VARCHAR(255)    NOT NULL DEFAULT '',
  link       VARCHAR(255)    NULL,
  read_at    DATETIME        NULL,
  created_at DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notifications_user (user_id, read_at, created_at),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rate_limits (
  id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  bucket       VARCHAR(80)     NOT NULL,
  key_hash     CHAR(64)        NOT NULL,
  hits         INT             NOT NULL DEFAULT 1,
  window_start DATETIME        NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_rate_limit (bucket, key_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ops_runs (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tool              ENUM('seed','reset','report') NOT NULL,
  params            JSON            NULL,
  triggered_by      ENUM('cli','web') NOT NULL,
  triggered_by_user BIGINT UNSIGNED NULL,
  status            ENUM('running','success','failed') NOT NULL DEFAULT 'running',
  output_summary    TEXT            NULL,
  started_at        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  finished_at       DATETIME        NULL,
  PRIMARY KEY (id),
  KEY idx_ops_runs_started (started_at),
  CONSTRAINT fk_ops_runs_user FOREIGN KEY (triggered_by_user) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed badge set. Codes are stable identifiers used by application logic;
-- names and copy can change freely without touching code.
INSERT INTO badges (code, name, description, icon, criteria_type, criteria_value) VALUES
  ('first_step',    'First step',    'Logged your very first check-in.',                    'footprint', 'total_checkins',       1),
  ('week_one',      'Week one',      'Held a 7 day streak in a single challenge.',          'flame',     'streak',               7),
  ('iron_month',    'Iron month',    'Held a 30 day streak in a single challenge.',         'shield',    'streak',              30),
  ('century',       'Century',       'Held a 100 day streak in a single challenge.',        'laurel',    'streak',             100),
  ('collector',     'Collector',     'Joined 5 different challenges.',                      'stack',     'challenges_joined',    5),
  ('finisher',      'Finisher',      'Stayed to the end of a challenge you joined.',        'flag',      'challenges_completed', 1),
  ('point_machine', 'Point machine', 'Earned 5,000 total points across all challenges.',    'bolt',      'points',            5000);
