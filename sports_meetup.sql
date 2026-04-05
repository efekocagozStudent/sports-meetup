-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 05, 2026 at 08:14 AM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sports_meetup`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `sport_type_id` int(10) UNSIGNED NOT NULL,
  `organizer_id` int(10) UNSIGNED NOT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `max_participants` smallint(5) UNSIGNED NOT NULL DEFAULT 10,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `skill_level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `status` enum('open','closed','cancelled') NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `sport_type_id`, `organizer_id`, `event_date`, `location`, `max_participants`, `requires_approval`, `skill_level`, `status`, `created_at`) VALUES
(1, 'tennis', 'tennis at amsterdam', 3, 1, '2026-04-06 04:24:00', 'tennis amsterdam', 6, 0, 'beginner', 'open', '2026-04-05 02:25:29'),
(2, 'Sunday 5-a-side Kickabout', 'Casual Sunday football in the park — all welcome. Bibs provided, just bring boots and water. We usually play two 30-minute halves with a short break.', 4, 4, '2026-04-10 05:33:51', 'Victoria Park, East London', 10, 0, 'beginner', 'open', '2026-04-02 05:33:51'),
(3, 'Competitive Basketball — 3v3', 'Competitive 3-on-3 half-court basketball. Intermediate level and above only. Bring your own water and prepare for a fast-paced game.', 5, 5, '2026-04-12 05:33:51', 'Brixton Recreation Centre, London', 6, 0, 'intermediate', 'open', '2026-03-31 05:33:51'),
(4, 'Morning Parkrun Group', 'Friendly 5k group run every Saturday morning. We run at the pace of the slowest member — nobody gets left behind.', 8, 6, '2026-04-08 05:33:51', 'Hyde Park, London', 30, 0, 'beginner', 'open', '2026-04-03 05:33:51'),
(5, 'Airsoft Skirmish — Urban Map', 'Full-day airsoft skirmish on an urban close-quarters map. Approval required as minimum kit standards apply. Bring 500+ BBs.', 1, 3, '2026-04-17 05:33:51', 'Alpha 55 Airsoft, Birmingham', 20, 1, 'advanced', 'open', '2026-03-29 05:33:51'),
(6, 'Tennis Doubles — Mixed', 'Mixed doubles tennis, two-hour session. Three sets, winner stays on. Any level welcome.', 3, 7, '2026-04-09 05:33:51', 'Regent\'s Park Tennis Courts, London', 4, 0, 'intermediate', 'open', '2026-04-04 05:33:51'),
(7, 'Beach Volleyball Tournament', 'Round-robin beach volleyball — 4 teams of 3. Approved players only as we need even numbers. Food and drinks after.', 6, 7, '2026-04-14 05:33:51', 'Sandbanks Beach, Poole', 12, 1, 'intermediate', 'open', '2026-04-01 05:33:51'),
(8, 'Beginner Badminton Session', 'Relaxed badminton for beginners and people returning to the sport after a break. Rackets available to borrow.', 7, 5, '2026-04-07 05:33:51', 'Islington Leisure Centre, London', 8, 0, 'beginner', 'open', '2026-03-30 05:33:51'),
(9, 'Paintball — Woodland Scenario', 'Woodland scenario paintball — attack and defend. Starter paint pack included. Bring old clothes you don\'t mind getting dirty.', 2, 3, '2026-04-19 05:33:51', 'Combat Paintball, Surrey', 16, 0, 'beginner', 'open', '2026-03-28 05:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `joined_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `event_id`, `user_id`, `status`, `joined_at`) VALUES
(1, 1, 2, 'approved', '2026-04-05 02:27:19'),
(2, 2, 5, 'approved', '2026-04-03 05:35:08'),
(3, 2, 6, 'approved', '2026-04-04 05:35:08'),
(4, 2, 7, 'approved', '2026-04-04 05:35:08'),
(5, 3, 3, 'approved', '2026-04-02 05:35:08'),
(6, 3, 6, 'approved', '2026-04-03 05:35:08'),
(7, 4, 7, 'approved', '2026-04-04 05:35:08'),
(8, 4, 4, 'approved', '2026-04-03 05:35:08'),
(9, 6, 4, 'approved', '2026-04-04 05:35:08'),
(10, 7, 4, 'pending', '2026-04-03 05:35:08'),
(11, 7, 6, 'approved', '2026-04-02 05:35:08'),
(12, 8, 3, 'approved', '2026-04-01 05:35:08'),
(13, 8, 7, 'approved', '2026-04-03 05:35:08'),
(14, 9, 5, 'approved', '2026-03-31 05:35:08'),
(15, 9, 4, 'approved', '2026-04-02 05:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `sport_types`
--

CREATE TABLE `sport_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `min_players` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `max_players` tinyint(3) UNSIGNED NOT NULL DEFAULT 20,
  `description` text DEFAULT NULL,
  `icon` varchar(10) NOT NULL DEFAULT '?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sport_types`
--

INSERT INTO `sport_types` (`id`, `name`, `min_players`, `max_players`, `description`, `icon`) VALUES
(1, 'Airsoft', 6, 40, 'Tactical shooting sport using replica firearms.', '🔫'),
(2, 'Paintball', 4, 30, 'Team combat sport using paint-filled pellets.', '🎨'),
(3, 'Tennis', 2, 4, 'Racket sport played on a rectangular court.', '🎾'),
(4, 'Football', 10, 22, 'The world\'s most popular team sport.', '⚽'),
(5, 'Basketball', 6, 10, 'Fast-paced team sport played on a court.', '🏀'),
(6, 'Volleyball', 6, 12, 'Team sport played over a high net.', '🏐'),
(7, 'Badminton', 2, 4, 'Racket sport using a shuttlecock.', '🏸'),
(8, 'Running', 2, 50, 'Group running events, fun-runs and races.', '🏃');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'efe', 'kocagozefe@gmail.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user', '2026-04-05 02:24:23'),
(2, 'eefe', 'efekocagozphotos@gmail.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user', '2026-04-05 02:27:11'),
(3, 'alex_smith', 'alex@example.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user', '2026-03-06 05:29:07'),
(4, 'jordan_fc', 'jordan@example.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user', '2026-03-11 05:29:07'),
(5, 'sam_hoops', 'sam@example.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user', '2026-03-16 05:29:07'),
(6, 'riley_runner', 'riley@example.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user', '2026-03-21 05:29:07'),
(7, 'morgan_net', 'morgan@example.com', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'user', '2026-03-26 05:29:07'),
(8, 'admin', 'admin@sportsmeet.local', '$2y$12$dp54x.wZewSgM2ouzL9QeOlWYSHQWqK7RhC1EzRH7ABKOkliAlX6e', 'admin', '2026-04-05 07:05:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_events_sport` (`sport_type_id`),
  ADD KEY `fk_events_org` (`organizer_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notif_user` (`user_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_event_user` (`event_id`,`user_id`),
  ADD KEY `fk_part_user` (`user_id`);

--
-- Indexes for table `sport_types`
--
ALTER TABLE `sport_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `sport_types`
--
ALTER TABLE `sport_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_org` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_events_sport` FOREIGN KEY (`sport_type_id`) REFERENCES `sport_types` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fk_part_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_part_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
