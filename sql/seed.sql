-- ─────────────────────────────────────────────────────────────────────────
-- SportsMeet — Seed Data
-- Run AFTER schema.sql:
--   docker exec -i sports-meetup-mysql-1 mysql -u root -psecret123 sports_meetup < sql/seed.sql
-- Or from inside the container:
--   mysql -u root -psecret123 sports_meetup < /sql/seed.sql
-- ─────────────────────────────────────────────────────────────────────────

USE sports_meetup;

-- ── Users ─────────────────────────────────────────────────────────────────
-- All passwords are:  Password1!
-- Hash generated with: password_hash('Password1!', PASSWORD_BCRYPT, ['cost' => 12])

-- Admin user (role = 'admin')
INSERT INTO users (username, email, password_hash, role, created_at) VALUES
    ('admin', 'admin@sportsmeet.local', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'admin', NOW() - INTERVAL 60 DAY)
ON DUPLICATE KEY UPDATE username = VALUES(username);

-- Regular users (role defaults to 'user')
INSERT INTO users (username, email, password_hash, created_at) VALUES
    ('alex_smith',   'alex@example.com',   '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', NOW() - INTERVAL 30 DAY),
    ('jordan_fc',    'jordan@example.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', NOW() - INTERVAL 25 DAY),
    ('sam_hoops',    'sam@example.com',    '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', NOW() - INTERVAL 20 DAY),
    ('riley_runner', 'riley@example.com',  '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', NOW() - INTERVAL 15 DAY),
    ('morgan_net',   'morgan@example.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', NOW() - INTERVAL 10 DAY)
ON DUPLICATE KEY UPDATE username = VALUES(username);

-- ── Events ────────────────────────────────────────────────────────────────
-- sport_type_id reference (from schema seed):
--  1=Airsoft  2=Paintball  3=Tennis  4=Football  5=Basketball  6=Volleyball  7=Badminton  8=Running

INSERT INTO events (title, description, sport_type_id, organizer_id, event_date, location, max_participants, requires_approval, skill_level, status, created_at)
VALUES
    (
        'Sunday 5-a-side Kickabout',
        'Casual Sunday football in the park — all welcome. Bibs provided, just bring boots and water. We usually play two 30-minute halves with a short break.',
        4,
        (SELECT id FROM users WHERE username = 'jordan_fc'),
        NOW() + INTERVAL 5 DAY,
        'Victoria Park, East London',
        10, 0, 'beginner', 'open', NOW() - INTERVAL 3 DAY
    ),
    (
        'Competitive Basketball — 3v3',
        'Competitive 3-on-3 half-court basketball. Intermediate level and above only. Bring your own water and prepare for a fast-paced game.',
        5,
        (SELECT id FROM users WHERE username = 'sam_hoops'),
        NOW() + INTERVAL 7 DAY,
        'Brixton Recreation Centre, London',
        6, 0, 'intermediate', 'open', NOW() - INTERVAL 5 DAY
    ),
    (
        'Morning Parkrun Group',
        'Friendly 5k group run every Saturday morning. We run at the pace of the slowest member — nobody gets left behind. Bring a QR code if you want an official time.',
        8,
        (SELECT id FROM users WHERE username = 'riley_runner'),
        NOW() + INTERVAL 3 DAY,
        'Hyde Park, London',
        30, 0, 'beginner', 'open', NOW() - INTERVAL 2 DAY
    ),
    (
        'Airsoft Skirmish — Urban Map',
        'Full-day airsoft skirmish on an urban close-quarters map. Approval required as minimum kit standards apply (no hire gear). Bring 500+ BBs.',
        1,
        (SELECT id FROM users WHERE username = 'alex_smith'),
        NOW() + INTERVAL 12 DAY,
        'Alpha 55 Airsoft, Birmingham',
        20, 1, 'advanced', 'open', NOW() - INTERVAL 7 DAY
    ),
    (
        'Tennis Doubles — Mixed',
        'Mixed doubles tennis, two-hour session. Three sets, winner stays on. Any level welcome — we will match pairs by ability on the day.',
        3,
        (SELECT id FROM users WHERE username = 'morgan_net'),
        NOW() + INTERVAL 4 DAY,
        'Regent''s Park Tennis Courts, London',
        4, 0, 'intermediate', 'open', NOW() - INTERVAL 1 DAY
    ),
    (
        'Beach Volleyball Tournament',
        'Round-robin beach volleyball — 4 teams of 3. Approved players only as we need even numbers. Food and drinks after. Sunscreen recommended.',
        6,
        (SELECT id FROM users WHERE username = 'morgan_net'),
        NOW() + INTERVAL 9 DAY,
        'Sandbanks Beach, Poole',
        12, 1, 'intermediate', 'open', NOW() - INTERVAL 4 DAY
    ),
    (
        'Beginner Badminton Session',
        'Relaxed badminton for beginners and people returning to the sport after a break. Rackets available to borrow. Coaching tips from our organiser.',
        7,
        (SELECT id FROM users WHERE username = 'sam_hoops'),
        NOW() + INTERVAL 2 DAY,
        'Islington Leisure Centre, London',
        8, 0, 'beginner', 'open', NOW() - INTERVAL 6 DAY
    ),
    (
        'Paintball — Woodland Scenario',
        'Woodland scenario paintball — attack and defend. Starter paint pack included in the venue fee. Bring old clothes you don''t mind getting dirty.',
        2,
        (SELECT id FROM users WHERE username = 'alex_smith'),
        NOW() + INTERVAL 14 DAY,
        'Combat Paintball, Surrey',
        16, 0, 'beginner', 'open', NOW() - INTERVAL 8 DAY
    )
ON DUPLICATE KEY UPDATE title = VALUES(title);

-- ── Participants ──────────────────────────────────────────────────────────
INSERT IGNORE INTO participants (event_id, user_id, status, joined_at)
VALUES
    -- Sunday football: sam, riley and morgan joined
    ((SELECT id FROM events WHERE title = 'Sunday 5-a-side Kickabout'),    (SELECT id FROM users WHERE username = 'sam_hoops'),    'approved', NOW() - INTERVAL 2 DAY),
    ((SELECT id FROM events WHERE title = 'Sunday 5-a-side Kickabout'),    (SELECT id FROM users WHERE username = 'riley_runner'), 'approved', NOW() - INTERVAL 1 DAY),
    ((SELECT id FROM events WHERE title = 'Sunday 5-a-side Kickabout'),    (SELECT id FROM users WHERE username = 'morgan_net'),   'approved', NOW() - INTERVAL 1 DAY),

    -- Basketball: jordan and riley joined
    ((SELECT id FROM events WHERE title = 'Competitive Basketball — 3v3'), (SELECT id FROM users WHERE username = 'jordan_fc'),    'approved', NOW() - INTERVAL 3 DAY),
    ((SELECT id FROM events WHERE title = 'Competitive Basketball — 3v3'), (SELECT id FROM users WHERE username = 'riley_runner'), 'approved', NOW() - INTERVAL 2 DAY),

    -- Parkrun: alex, jordan, sam joined
    ((SELECT id FROM events WHERE title = 'Morning Parkrun Group'),        (SELECT id FROM users WHERE username = 'alex_smith'),   'approved', NOW() - INTERVAL 1 DAY),
    ((SELECT id FROM events WHERE title = 'Morning Parkrun Group'),        (SELECT id FROM users WHERE username = 'jordan_fc'),    'approved', NOW() - INTERVAL 1 DAY),
    ((SELECT id FROM events WHERE title = 'Morning Parkrun Group'),        (SELECT id FROM users WHERE username = 'sam_hoops'),    'approved', NOW() - INTERVAL 1 DAY),

    -- Airsoft: pending approvals from jordan and sam
    ((SELECT id FROM events WHERE title = 'Airsoft Skirmish — Urban Map'), (SELECT id FROM users WHERE username = 'jordan_fc'),    'pending',  NOW() - INTERVAL 1 DAY),
    ((SELECT id FROM events WHERE title = 'Airsoft Skirmish — Urban Map'), (SELECT id FROM users WHERE username = 'sam_hoops'),    'pending',  NOW()),

    -- Badminton: alex and jordan joined
    ((SELECT id FROM events WHERE title = 'Beginner Badminton Session'),   (SELECT id FROM users WHERE username = 'alex_smith'),   'approved', NOW() - INTERVAL 5 DAY),
    ((SELECT id FROM events WHERE title = 'Beginner Badminton Session'),   (SELECT id FROM users WHERE username = 'jordan_fc'),    'approved', NOW() - INTERVAL 4 DAY);
