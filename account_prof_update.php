<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2008  Mantis Team   - mantisbt-dev@lists.sourceforge.net

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
	# $Id: account_prof_update.php,v 1.29.2.1 2007-10-13 22:32:18 giallu Exp $
	# --------------------------------------------------------

	# This page updates the users profile information then redirects to
	# account_prof_menu_page.php

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'profile_api.php' );

	form_security_validate('profile_update');

	auth_ensure_user_authenticated();

	current_user_ensure_unprotected();

	$f_action = gpc_get_string('action');

	switch ( $f_action ) {
		case 'edit':
			$f_profile_id = gpc_get_int( 'profile_id' );
			print_header_redirect( 'account_prof_edit_page.php?profile_id=' . $f_profile_id );
			break;

		case 'add':
			$f_platform		= gpc_get_string( 'platform' );
			$f_os			= gpc_get_string( 'os' );
			$f_os_build		= gpc_get_string( 'os_build' );
			$f_description	= gpc_get_string( 'description' );

			$t_user_id		= gpc_get_int( 'user_id' );
			if ( ALL_USERS != $t_user_id ) {
				$t_user_id = auth_get_current_user_id();
			}

			if ( ALL_USERS == $t_user_id ) {
				access_ensure_global_level( config_get( 'manage_global_profile_threshold' ) );
			} else {
				access_ensure_global_level( config_get( 'add_profile_threshold' ) );
			}

			profile_create( $t_user_id, $f_platform, $f_os, $f_os_build, $f_description );

			if ( ALL_USERS == $t_user_id ) {
				print_header_redirect( 'manage_prof_menu_page.php' );
			} else {
				print_header_redirect( 'account_prof_menu_page.php' );
			}
			break;

		case 'update':
			$f_profile_id = gpc_get_int( 'profile_id' );
			$f_platform = gpc_get_string( 'platform' );
			$f_os = gpc_get_string( 'os' );
			$f_os_build = gpc_get_string( 'os_build' );
			$f_description = gpc_get_string( 'description' );

			if ( profile_is_global( $f_profile_id ) ) {
				access_ensure_global_level( config_get( 'manage_global_profile_threshold' ) );

				profile_update( ALL_USERS, $f_profile_id, $f_platform, $f_os, $f_os_build, $f_description );
				print_header_redirect( 'manage_prof_menu_page.php' );
			} else {
				profile_update( auth_get_current_user_id(), $f_profile_id, $f_platform, $f_os, $f_os_build, $f_description );
				print_header_redirect( 'account_prof_menu_page.php' );
			}
			break;

		case 'delete':
			$f_profile_id = gpc_get_int( 'profile_id' );
			if ( profile_is_global( $f_profile_id ) ) {
				access_ensure_global_level( config_get( 'manage_global_profile_threshold' ) );

				profile_delete( ALL_USERS, $f_profile_id );
				print_header_redirect( 'manage_prof_menu_page.php' );
			} else {
				profile_delete( auth_get_current_user_id(), $f_profile_id );
				print_header_redirect( 'account_prof_menu_page.php' );
			}
			break;

		case 'make_default':
			$f_profile_id = gpc_get_int( 'profile_id' );
			current_user_set_pref( 'default_profile', $f_profile_id );
			print_header_redirect( 'account_prof_menu_page.php' );
			break;
	}
