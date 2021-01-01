CREATE TABLE `acx_cronjobs` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
    `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `proc_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `last_run_time` datetime DEFAULT NULL,
    `next_run_time` datetime DEFAULT NULL,
    `cron_type` enum('minutely', 'hourly', 'daily', 'monthly', 'yearly') NOT NULL,
    `run_time` datetime DEFAULT NULL,
    `run_interval` int(11) DEFAULT NULL,
    `enabled` tinyint(1) DEFAULT 1,
    `updated` datetime DEFAULT NULL,
	`updated_by` int(11) DEFAULT NULL,
	`created` datetime NOT NULL,
	`created_by` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_slug`(`slug`),
    INDEX `idx_last_run_time`(`last_run_time`),
    INDEX `idx_next_run_time`(`next_run_time`),
    INDEX `idx_run_time`(`run_time`),
    INDEX `idx_run_interval`(`run_interval`),
    INDEX `idx_enabled`(`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

insert into `acx_cronjobs`(`slug`, `name`, `proc_name`, `cron_type`, `run_interval`, `enabled`, `updated`, `updated_by`, `created`, `created_by`) values
('timeout-transaction-clearing', 'Timeout Transaction Clearing', 'do_timeout_transaction_clearing', 'minutely', 2, 0, now(), 1, now(), 1),
('transaction-exceed-payment-time-limit-clearing', 'Transaction exceed payment time limit clearing', 'do_transaction_exceed_payment_time_limit_clearing', 'minutely', 2, 0, now(), 1, now(), 1),
('hourly-hkbo-report', 'Hourly HKBO report', 'do_hourly_hkbo_report', 'hourly', 1, 0, now(), 1, now(), 1),
('daily-hkbo-report', 'Daily HKBO report', 'do_daily_hkbo_report', 'daily', 1, 0, now(), 1, now(), 1);

