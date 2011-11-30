<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2007  Mantis Team   - mantisbt-dev@lists.sourceforge.net

# Mantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# Mantis is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Mantis.  If not, see <http://www.gnu.org/licenses/>.

	# Changes applied to 0.18 database

	# --------------------------------------------------------
	# $Id: 0_19_inc.php,v 1.14.16.1 2007-10-13 22:35:09 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	require( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'db_table_names_inc.php' );

	$upgrades = array();

	$upgrades[] = new SQLUpgrade(
			'bugnote-type',
			'Add note type column to bugnote',
			"ALTER TABLE $t_bugnote_table ADD note_type INT(7) default '0'" );

	$upgrades[] = new SQLUpgrade(
			'bugnote-attr',
			'Add note_attr column to bugnote',
			"ALTER TABLE $t_bugnote_table ADD note_attr VARCHAR(250) default ''" );

	$upgrades[] = new SQLUpgrade(
			'tokensdb-1',
			'Add mantis_tokens_table',
			"CREATE TABLE $t_tokens_table (
			  id int NOT NULL auto_increment,
			  owner int NOT NULL,
			  type int NOT NULL,
			  timestamp datetime NOT NULL,
			  expiry datetime NOT NULL,
			  value text NOT NULL,
			  PRIMARY KEY (id))"
		);

	$upgrades[] = new SQLUpgrade(
			'sticky-issues',
			'Add sticky column to bug table',
			"ALTER TABLE $t_bug_table ADD sticky TINYINT(1) default '0' NOT NULL" );

	$upgrades[] = new SQLUpgrade(
			'project-hierarchy',
			'Add project hierarchy table',
			"CREATE TABLE $t_project_hierarchy_table (
			  child_id  INT UNSIGNED NOT NULL,
			  parent_id INT UNSIGNED NOT NULL)"
			);

	$upgrades[] = new SQLUpgrade(
			'configdb-1',
			'Add mantis_config_table',
			"CREATE TABLE $t_config_table (
			  config_id VARCHAR(64) NOT NULL,
			  project_id INT DEFAULT 0,
			  user_id INT DEFAULT 0,
			  access INT DEFAULT 0,
			  type INT DEFAULT 90,
			  value text NOT NULL,
			  INDEX (config_id),
			  UNIQUE config ( config_id, project_id, user_id ) )"
		);

	$upgrades[] = new SQLUpgrade(
			'field_shorten-1',
			'shorten field names: lost_password_in_progress_count',
			"ALTER TABLE $t_user_table CHANGE lost_password_in_progress_count lost_password_request_count INT(2) DEFAULT '0' NOT NULL"
		);

	$upgrades[] = new SQLUpgrade(
			'field_naming-1',
			'DBMS compatibility: access is a reserved word',
			"ALTER TABLE $t_config_table CHANGE access access_reqd INT DEFAULT '0'"
		);

	$upgrades[] = new SQLUpgrade(
			'configdb-un',
			'Drop mantis_config_table unique key',
			"ALTER TABLE $t_config_table 
			    DROP INDEX config"
		);

	return $upgrades;
?>
