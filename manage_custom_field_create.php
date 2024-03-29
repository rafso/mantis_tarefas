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
	# $Id: manage_custom_field_create.php,v 1.17.2.1 2007-10-13 22:33:26 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'custom_field_api.php' );

	auth_reauthenticate();

	form_security_validate('manage_custom_field_create');

	access_ensure_global_level( config_get( 'manage_custom_fields_threshold' ) );

	$f_name	= gpc_get_string( 'name' );

	$t_field_id = custom_field_create( $f_name );

	if ( ON == config_get( 'custom_field_edit_after_create' ) ) {
		$t_redirect_url = "manage_custom_field_edit_page.php?field_id=$t_field_id";
	} else {
		$t_redirect_url = 'manage_custom_field_page.php';
	}

	html_page_top1();
	html_meta_redirect( $t_redirect_url );
	html_page_top2();

	echo '<br />';
	echo '<div align="center">';

	echo lang_get( 'operation_successful' ) . '<br />';

	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );

	echo '</div>';

	html_page_bottom1( __FILE__ );
?>
