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
	# $Id: manage_custom_field_update.php,v 1.19.2.1 2007-10-13 22:33:29 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'custom_field_api.php' );

	auth_reauthenticate();

	form_security_validate('manage_custom_field_update');

	access_ensure_global_level( config_get( 'manage_custom_fields_threshold' ) );

	$f_field_id						= gpc_get_int( 'field_id' );
	$f_return						= strip_tags( gpc_get_string( 'return', 'manage_custom_field_page.php' ) );
	$t_values['name']				= gpc_get_string( 'name' );
	$t_values['type']				= gpc_get_int( 'type' );
	$t_values['possible_values']	= gpc_get_string( 'possible_values' );
	$t_values['default_value']		= gpc_get_string( 'default_value' );
	$t_values['valid_regexp']		= gpc_get_string( 'valid_regexp' );
	$t_values['access_level_r']		= gpc_get_int( 'access_level_r' );
	$t_values['access_level_rw']	= gpc_get_int( 'access_level_rw' );
	$t_values['length_min']			= gpc_get_int( 'length_min' );
	$t_values['length_max']			= gpc_get_int( 'length_max' );
	$t_values['advanced']			= gpc_get_bool( 'advanced' );
	$t_values['display_report']	= gpc_get_bool( 'display_report' );
	$t_values['display_update']	= gpc_get_bool( 'display_update' );
	$t_values['display_resolved']	= gpc_get_bool( 'display_resolved' );
	$t_values['display_closed']		= gpc_get_bool( 'display_closed' );
	$t_values['require_report']		= gpc_get_bool( 'require_report' );
	$t_values['require_update']		= gpc_get_bool( 'require_update' );
	$t_values['require_resolved']	= gpc_get_bool( 'require_resolved' );
	$t_values['require_closed']		= gpc_get_bool( 'require_closed' );

	custom_field_update( $f_field_id, $t_values );

	html_page_top1();

	html_meta_redirect( $f_return );

	html_page_top2();

	echo '<br />';
	echo '<div align="center">';

	echo lang_get( 'operation_successful' ) . '<br />';

	print_bracket_link( $f_return, lang_get( 'proceed' ) );

	echo '</div>';

	html_page_bottom1( __FILE__ );
?>
