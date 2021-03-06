<?php
/**
 * i-MSCP CronJobs plugin
 * Copyright (C) 2014-2015 Laurent Declercq <l.declercq@nuxwin.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

return array(
	'up' => "
		CREATE TABLE IF NOT EXISTS cron_jobs (
			cron_job_id INT(10) unsigned AUTO_INCREMENT NOT NULL,
			cron_job_permission_id INT(10) unsigned DEFAULT NULL,
			cron_job_admin_id INT(10) unsigned DEFAULT NULL,
			cron_job_type ENUM('url', 'jailed', 'full') NOT NULL DEFAULT 'url',
			cron_job_notification VARCHAR(255) DEFAULT NULL,
			cron_job_minute VARCHAR(255) NOT NULL DEFAULT '*',
			cron_job_hour VARCHAR(255) NOT NULL DEFAULT '*',
			cron_job_dmonth VARCHAR(255) NOT NULL DEFAULT '*',
			cron_job_month VARCHAR(255) NOT NULL DEFAULT '*',
			cron_job_dweek VARCHAR(255) NOT NULL DEFAULT '*',
			cron_job_user VARCHAR(255) NOT NULL,
			cron_job_command TEXT,
			cron_job_status varchar(255) NOT NULL,
			PRIMARY KEY cron_job_id (cron_job_id),
			KEY cron_job_permission_id (cron_job_permission_id),
			KEY cron_job_admin_id (cron_job_admin_id),
			KEY cron_job_status (cron_job_status)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;
	",
	'down' => '
		DROP TABLE IF EXISTS cron_jobs
	'
);
