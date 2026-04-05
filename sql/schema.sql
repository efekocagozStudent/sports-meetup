-- ─────────────────────────────────────────────────────────────────────────
-- SportsMeet — Database Schema
-- Run this file once to set up all tables and seed sport types.
-- Usage: mysql -u root -p < sql/schema.sql
-- ─────────────────────────────────────────────────────────────────────────

CREATE DATABASE IF NOT EXISTS sports_meetup
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sports_meetup;

-- ── sport_types ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS sport_types (
    id          INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50)     NOT NULL,
    min_players TINYINT UNSIGNED NOT NULL DEFAULT 2,
    max_players TINYINT UNSIGNED NOT NULL DEFAULT 20,
    description TEXT,
    icon        VARCHAR(10)     NOT NULL DEFAULT '🏃'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── users ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)   NOT NULL UNIQUE,
    email         VARCHAR(255)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at    DATETIME      NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── events ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS events (
    id                INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    title             VARCHAR(100)   NOT NULL,
    description       TEXT           NOT NULL,
    sport_type_id     INT UNSIGNED   NOT NULL,
    organizer_id      INT UNSIGNED   NOT NULL,
    event_date        DATETIME       NOT NULL,
    location          VARCHAR(255)   NOT NULL,
    max_participants  SMALLINT UNSIGNED NOT NULL DEFAULT 10,
    requires_approval TINYINT(1)     NOT NULL DEFAULT 0,
    skill_level       ENUM('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
    status            ENUM('open','closed','cancelled') NOT NULL DEFAULT 'open',
    created_at        DATETIME       NOT NULL,
    CONSTRAINT fk_events_sport    FOREIGN KEY (sport_type_id) REFERENCES sport_types(id),
    CONSTRAINT fk_events_org      FOREIGN KEY (organizer_id)  REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── participants ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS participants (
    id        INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    event_id  INT UNSIGNED  NOT NULL,
    user_id   INT UNSIGNED  NOT NULL,
    status    ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved',
    joined_at DATETIME      NOT NULL,
    UNIQUE KEY uq_event_user (event_id, user_id),
    CONSTRAINT fk_part_event FOREIGN KEY (event_id) REFERENCES events(id)  ON DELETE CASCADE,
    CONSTRAINT fk_part_user  FOREIGN KEY (user_id)  REFERENCES users(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── notifications ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id         INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED  NOT NULL,
    type       VARCHAR(50)   NOT NULL,
    message    VARCHAR(255)  NOT NULL,
    link       VARCHAR(255)  NOT NULL DEFAULT '',
    is_read    TINYINT(1)    NOT NULL DEFAULT 0,
    created_at DATETIME      NOT NULL,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Seed: sport types ─────────────────────────────────────────────────────
INSERT INTO sport_types (name, min_players, max_players, description, icon) VALUES
    ('Airsoft',    6,  40, 'Tactical shooting sport using replica firearms.',    '🔫'),
    ('Paintball',  4,  30, 'Team combat sport using paint-filled pellets.',      '🎨'),
    ('Tennis',     2,   4, 'Racket sport played on a rectangular court.',        '🎾'),
    ('Football',  10,  22, 'The world\'s most popular team sport.',              '⚽'),
    ('Basketball', 6,  10, 'Fast-paced team sport played on a court.',           '🏀'),
    ('Volleyball', 6,  12, 'Team sport played over a high net.',                 '🏐'),
    ('Badminton',  2,   4, 'Racket sport using a shuttlecock.',                  '🏸'),
    ('Running',    2,  50, 'Group running events, fun-runs and races.',          '🏃')
ON DUPLICATE KEY UPDATE name = VALUES(name);
