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
	# $Id: tag_update.php,v 1.1.2.1 2007-10-13 22:34:45 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'tag_api.php' );

	form_security_validate( 'tag_update' );

	compress_enable();

	$f_tag_id = gpc_get_int( 'tag_id' );
	$t_tag_row = tag_get( $f_tag_id );

	if ( ! ( access_has_global_level( config_get( 'tag_edit_threshold' ) ) 
		|| ( auth_get_current_user_id() == $t_tag_row['user_id'] )
			&& access_has_global_level( config_get( 'tag_edit_own_threshold' ) ) ) ) 
	{
		access_denied();
	}

	if ( access_has_global_level( config_get( 'tag_edit_threshold' ) ) ) {
		$f_new_user_id = gpc_get_int( 'user_id', $t_tag_row['user_id'] );
	} else {
		$f_new_user_id = $t_tag_row['user_id'];
	}

	$f_new_name = gpc_get_string( 'name', $t_tag_row['name'] );
	$f_new_description = gpc_get_string( 'description', $t_tag_row['description'] );

	$t_update = false;

	if ( $t_tag_row['user_id'] != $f_new_user_id ) {
		user_ensure_exists( $f_new_user_id );
		$t_update = true;
	}

	if ( 	$t_tag_row['name'] != $f_new_name ||
			$t_tag_row['description'] != $f_new_description ) {

		$t_update = true;
	}

	tag_update( $f_tag_id, $f_new_name, $f_new_user_id, $f_new_description );
		
	$t_url = 'tag_view_page.php?tag_id='.$f_tag_id;
	print_successful_redirect( $t_url );
