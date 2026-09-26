-- =====================================================================
-- Spin the Wheel (Pangkabuhayan monthly raffle) + prize expense ledgers
-- Run this once against the local `dayongan` database AND the Railway
-- database. The CREATE TABLEs are safe to re-run; the two ALTER sections
-- in the middle are "run once per database" — they fail harmlessly with
-- "Duplicate column name" if repeated.
--
-- The winner → ledger link lives ONLY on `ledgers` (DrawID → spin_draws,
-- RefID = the member's UserID), mirroring how an activation payment is
-- linked through ActivationID. `spin_winners` carries no ledger pointer;
-- the app pairs a winner to the Nth Source-PK row of their UserID in the
-- draw (rows are booked one per spin, in spin order).
--
-- Equivalent Laravel migrations also exist:
--   database/migrations/2026_09_16_120000_create_spin_draws_table.php
--   database/migrations/2026_09_16_120100_create_spin_winners_table.php
--   database/migrations/2026_09_16_130000_add_announcement_id_to_spin_draws_table.php
--   database/migrations/2026_09_16_140050_add_draw_id_to_ledgers_table.php
-- =====================================================================

CREATE TABLE IF NOT EXISTS `spin_draws` (
  `DrawID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `draw_date` date NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `pangkabuhayan_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `prize_name` varchar(255) NOT NULL DEFAULT '5 kls Rice',
  `prize_amount` decimal(12,2) NOT NULL DEFAULT 275.00,
  `winners_target` int(10) unsigned NOT NULL DEFAULT 0,
  `allow_repeat_winners` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('Open','Completed') NOT NULL DEFAULT 'Open',
  `completed_at` timestamp NULL DEFAULT NULL,
  `AnnouncementID` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`DrawID`),
  UNIQUE KEY `spin_draws_draw_date_unique` (`draw_date`),
  KEY `spin_draws_announcementid_index` (`AnnouncementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `spin_winners` (
  `WinnerID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `DrawID` bigint(20) unsigned NOT NULL,
  `UserID` bigint(20) unsigned NOT NULL,
  `spin_order` int(10) unsigned NOT NULL,
  `qualified_invites` int(10) unsigned NOT NULL DEFAULT 0,
  `prize_name` varchar(255) DEFAULT NULL,
  `prize_amount` decimal(12,2) DEFAULT NULL,
  `DrawnBy` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`WinnerID`),
  KEY `spin_winners_drawid_spin_order_index` (`DrawID`,`spin_order`),
  KEY `spin_winners_userid_index` (`UserID`),
  CONSTRAINT `spin_winners_drawid_foreign` FOREIGN KEY (`DrawID`)
    REFERENCES `spin_draws` (`DrawID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- RUN ONCE — the Pangkabuhayan prize expense in `ledgers`
--
-- Every Spin the Wheel winner books exactly one row here:
--   Source        = 'PK'            (Pangkabuhayan prize)
--   RefID         = the member's UserID
--   amount        = negative prize, e.g. -275.00 (money going out)
--   DrawID        = the cycle that drew the prize  ← the link
--   ActivationID / WithdrawID = NULL
--   status        = 'Pending' until the office tags the prize as claimed,
--                   then 'Completed' (only then does it hit the books)
--
-- The application writes those rows itself; this is only the schema.
-- The statement below is the same change you already made locally, with
-- DEFAULT 'OT' kept as the table's original default — a ledger insert that
-- forgets its Source should not be silently booked as a prize expense.
-- ---------------------------------------------------------------------

ALTER TABLE `ledgers`
  MODIFY COLUMN `Source`
    ENUM('MF','DR','OV','CL','RF','PF','WD','TX','CF','PK','OT')
    NOT NULL DEFAULT 'OT';

-- MySQL creates the index `ledgers_drawid_foreign` on DrawID automatically.
ALTER TABLE `ledgers`
  ADD COLUMN `DrawID` bigint(20) unsigned DEFAULT NULL AFTER `withdrawID`,
  ADD CONSTRAINT `ledgers_drawid_foreign` FOREIGN KEY (`DrawID`)
    REFERENCES `spin_draws` (`DrawID`) ON DELETE SET NULL ON UPDATE CASCADE;


-- ---------------------------------------------------------------------
-- RUN ONCE — ONLY IF `spin_draws` was created before 2026-09-16 13:00
-- (`AnnouncementID` links a cycle to its dashboard announcement post).
-- ---------------------------------------------------------------------

-- ALTER TABLE `spin_draws`
--   ADD COLUMN `AnnouncementID` bigint(20) unsigned DEFAULT NULL AFTER `completed_at`,
--   ADD KEY `spin_draws_announcementid_index` (`AnnouncementID`);


-- ---------------------------------------------------------------------
-- OPTIONAL — draw day, prize and rules
-- The application falls back to a 19th draw day, 5 kls / ₱55 per kilo /
-- members-only when these rows do not exist, and an administrator can
-- change all of them from the wheel modal itself ("Prize & rules").
-- Only run this to pre-set them. The draw day must be 1–28.
-- ---------------------------------------------------------------------

-- INSERT INTO `settings` (`name`, `value`, `created_at`, `updated_at`)
-- VALUES
--   ('spin_draw_day', '19', NOW(), NOW()),
--   ('spin_prize_kilos', '5', NOW(), NOW()),
--   ('spin_prize_price_per_kilo', '55', NOW(), NOW()),
--   ('spin_members_only', 'true', NOW(), NOW()),
--   ('spin_claim_instructions', 'Present this email together with a valid government-issued ID at our office to claim your prize.', NOW(), NOW());


-- ---------------------------------------------------------------------
-- ONLY IF YOU ALSO PUSH THE MIGRATION FILES TO RAILWAY
-- Creating the tables by hand leaves Laravel's `migrations` table unaware
-- of them, so a later `php artisan migrate` would try to create the same
-- tables again. Run this to mark them as already applied.
--
-- The 140050 migration is guarded with Schema::hasColumn(), so it can be
-- left unmarked and run safely — it simply skips a column you already
-- added by hand.
-- ---------------------------------------------------------------------

-- INSERT INTO `migrations` (`migration`, `batch`)
-- VALUES
--   ('2026_09_16_120000_create_spin_draws_table', 99),
--   ('2026_09_16_120100_create_spin_winners_table', 99),
--   ('2026_09_16_130000_add_announcement_id_to_spin_draws_table', 99);
