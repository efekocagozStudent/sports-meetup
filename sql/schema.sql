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

-- ── password_resets ──────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS password_resets (
    id         INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255)  NOT NULL,
    token      VARCHAR(64)   NOT NULL UNIQUE,
    expires_at DATETIME      NOT NULL,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pr_email (email),
    INDEX idx_pr_token (token)
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

-- ── Seed: users ───────────────────────────────────────────────────────────
-- All passwords are: Password1!
INSERT INTO users (id, username, email, password_hash, role, created_at) VALUES
    (1, 'efe',           'kocagozefe@gmail.com',        '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user',  '2026-04-05 02:24:23'),
    (2, 'eefe',          'efekocagozphotos@gmail.com',  '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user',  '2026-04-05 02:27:11'),
    (3, 'alex_smith',    'alex@example.com',            '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user',  '2026-03-06 05:29:07'),
    (4, 'jordan_fc',     'jordan@example.com',          '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user',  '2026-03-11 05:29:07'),
    (5, 'sam_hoops',     'sam@example.com',             '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user',  '2026-03-16 05:29:07'),
    (6, 'riley_runner',  'riley@example.com',           '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user',  '2026-03-21 05:29:07'),
    (7, 'morgan_net',    'morgan@example.com',          '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user',  '2026-03-26 05:29:07'),
    (8, 'admin',         'admin@sportsmeet.local',      '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'admin', '2026-04-05 07:05:02')
ON DUPLICATE KEY UPDATE username = VALUES(username);

-- ── Seed: events ──────────────────────────────────────────────────────────
INSERT INTO events (id, title, description, sport_type_id, organizer_id, event_date, location, max_participants, requires_approval, skill_level, status, created_at) VALUES
    (1, 'tennis',                          'tennis at amsterdam',                                                                                                                                                    3, 1, '2026-04-06 04:24:00', 'tennis amsterdam',                     6,  0, 'beginner',     'open', '2026-04-05 02:25:29'),
    (2, 'Sunday 5-a-side Kickabout',       'Casual Sunday football in the park — all welcome. Bibs provided, just bring boots and water. We usually play two 30-minute halves with a short break.',                   4, 4, '2026-04-10 05:33:51', 'Victoria Park, East London',            10, 0, 'beginner',     'open', '2026-04-02 05:33:51'),
    (3, 'Competitive Basketball — 3v3',    'Competitive 3-on-3 half-court basketball. Intermediate level and above only. Bring your own water and prepare for a fast-paced game.',                                    5, 5, '2026-04-12 05:33:51', 'Brixton Recreation Centre, London',     6,  0, 'intermediate', 'open', '2026-03-31 05:33:51'),
    (4, 'Morning Parkrun Group',           'Friendly 5k group run every Saturday morning. We run at the pace of the slowest member — nobody gets left behind.',                                                       8, 6, '2026-04-08 05:33:51', 'Hyde Park, London',                     30, 0, 'beginner',     'open', '2026-04-03 05:33:51'),
    (5, 'Airsoft Skirmish — Urban Map',    'Full-day airsoft skirmish on an urban close-quarters map. Approval required as minimum kit standards apply. Bring 500+ BBs.',                                             1, 3, '2026-04-17 05:33:51', 'Alpha 55 Airsoft, Birmingham',          20, 1, 'advanced',     'open', '2026-03-29 05:33:51'),
    (6, 'Tennis Doubles — Mixed',          'Mixed doubles tennis, two-hour session. Three sets, winner stays on. Any level welcome.',                                                                                 3, 7, '2026-04-09 05:33:51', 'Regent\'s Park Tennis Courts, London',  4,  0, 'intermediate', 'open', '2026-04-04 05:33:51'),
    (7, 'Beach Volleyball Tournament',     'Round-robin beach volleyball — 4 teams of 3. Approved players only as we need even numbers. Food and drinks after.',                                                      6, 7, '2026-04-14 05:33:51', 'Sandbanks Beach, Poole',                12, 1, 'intermediate', 'open', '2026-04-01 05:33:51'),
    (8, 'Beginner Badminton Session',      'Relaxed badminton for beginners and people returning to the sport after a break. Rackets available to borrow.',                                                           7, 5, '2026-04-07 05:33:51', 'Islington Leisure Centre, London',      8,  0, 'beginner',     'open', '2026-03-30 05:33:51'),
    (9, 'Paintball — Woodland Scenario',   'Woodland scenario paintball — attack and defend. Starter paint pack included. Bring old clothes you don\'t mind getting dirty.',                                          2, 3, '2026-04-19 05:33:51', 'Combat Paintball, Surrey',              16, 0, 'beginner',     'open', '2026-03-28 05:33:51')
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ── Seed: participants ────────────────────────────────────────────────────
INSERT INTO participants (id, event_id, user_id, status, joined_at) VALUES
    (1,  1, 2, 'approved', '2026-04-05 02:27:19'),
    (2,  2, 5, 'approved', '2026-04-03 05:35:08'),
    (3,  2, 6, 'approved', '2026-04-04 05:35:08'),
    (4,  2, 7, 'approved', '2026-04-04 05:35:08'),
    (5,  3, 3, 'approved', '2026-04-02 05:35:08'),
    (6,  3, 6, 'approved', '2026-04-03 05:35:08'),
    (7,  4, 7, 'approved', '2026-04-04 05:35:08'),
    (8,  4, 4, 'approved', '2026-04-03 05:35:08'),
    (9,  6, 4, 'approved', '2026-04-04 05:35:08'),
    (10, 7, 4, 'pending',  '2026-04-03 05:35:08'),
    (11, 7, 6, 'approved', '2026-04-02 05:35:08'),
    (12, 8, 3, 'approved', '2026-04-01 05:35:08'),
    (13, 8, 7, 'approved', '2026-04-03 05:35:08'),
    (14, 9, 5, 'approved', '2026-03-31 05:35:08'),
    (15, 9, 4, 'approved', '2026-04-02 05:35:08')
ON DUPLICATE KEY UPDATE status = VALUES(status);
