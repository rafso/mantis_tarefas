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

	# --------------------------------------------------------
	# $Id: upgrade.php,v 1.16.2.1 2007-10-13 22:34:58 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	$g_skip_open_db = true;  # don't open the database in database_api.php
	require_once ( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'core.php' );
	$g_error_send_page_header = false; # suppress page headers in the error handler

    # @@@ upgrade list moved to the bottom of upgrade_inc.php

	$f_advanced = gpc_get_bool( 'advanced', false );

	$result = @db_connect( config_get_global( 'dsn', false ), config_get_global( 'hostname' ), config_get_global( 'db_username' ), config_get_global( 'db_password' ), config_get_global( 'database_name' ) );
	if ( false == $result ) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title> Mantis Administration - Upgrade Installation </title>
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
	<tr class="top-bar">
		<td class="links">
			[ <a href="upgrade_list.php">Back to Upgrade List</a> ]
			[ <a href="upgrade.php">Refresh view</a> ]
			[ <a href="upgrade.php?advanced=<?php echo ( $f_advanced ? 0 : 1 ) ?>"><?php echo ( $f_advanced ? 'Simple' : 'Advanced' ) ?></a> ]
		</td>
		<td class="title">
			Upgrade Installation
		</td>
	</tr>
</table>
<br /><br />
<p>Opening connection to database [<?php echo config_get_global( 'database_name' ) ?>] on host [<?php echo config_get_global( 'hostname' ) ?>] with username [<?php echo config_get_global( 'db_username' ) ?>] failed ( <?php echo db_error_msg() ?> ).</p>
</body>
<?php
        exit();
	}

	# check to see if the new installer was used
    if ( -1 != config_get( 'database_version', -1 ) ) {
		if ( OFF == $g_use_iis ) {
			header( 'Status: 302' );
		}
		header( 'Content-Type: text/html' );

		if ( ON == $g_use_iis ) {
			header( "Refresh: 0;url=install.php" );
		} else {
			header( "Location: install.php" );
		}
		exit; # additional output can cause problems so let's just stop output here
	}
?>
<html>
<head>
<title> Mantis Administration - Upgrade Installation </title>
<link rel="stylesheet" type="text/css" href="admin.css" />
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
	<tr class="top-bar">
		<td class="links">
			[ <a href="upgrade_list.php">Back to Upgrade List</a> ]
			[ <a href="upgrade.php">Refresh view</a> ]
			[ <a href="upgrade.php?advanced=<?php echo ( $f_advanced ? 0 : 1 ) ?>"><?php echo ( $f_advanced ? 'Simple' : 'Advanced' ) ?></a> ]
		</td>
		<td class="title">
			Upgrade Installation
		</td>
	</tr>
</table>
<br /><br />
<?php
	if ( ! db_table_exists( config_get( 'mantis_upgrade_table' ) ) ) {
        # Create the upgrade table if it does not exist
        $query = "CREATE TABLE " . config_get( 'mantis_upgrade_table' ) .
				  "(upgrade_id char(20) NOT NULL,
				  description char(255) NOT NULL,
				  PRIMARY KEY (upgrade_id))";

        $result = db_query( $query );
    }

	# link the data structures and upgrade list
	require_once ( 'upgrade_inc.php' );

	$upgrade_set->process_post_data( $f_advanced );
?>
</body>
</html>
