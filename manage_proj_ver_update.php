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
	# $Id: manage_proj_ver_update.php,v 1.31.2.1 2007-10-13 22:33:49 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'version_api.php' );

	auth_reauthenticate();
	form_security_validate( 'manage_proj_ver_update' );

	$f_version_id = gpc_get_int( 'version_id' );

	$t_version = version_get( $f_version_id );

	$f_date_order	= gpc_get_string( 'date_order' );
	$f_new_version	= gpc_get_string( 'new_version' );
	$f_description  = gpc_get_string( 'description' );
	$f_released     = gpc_get_bool( 'released' );

	access_ensure_project_level( config_get( 'manage_project_threshold' ), $t_version->project_id );

	if ( is_blank( $f_new_version ) ) {
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}

	$f_new_version	= trim( $f_new_version );

	$t_version->version = $f_new_version;
	$t_version->description = $f_description;
	$t_version->released = $f_released ? VERSION_RELEASED : VERSION_FUTURE;
	$t_version->date_order = $f_date_order;

	version_update( $t_version );

	$t_redirect_url = 'manage_proj_edit_page.php?project_id=' . $t_version->project_id;
?>
<?php
	html_page_top1();

	html_meta_redirect( $t_redirect_url );

	html_page_top2();
?>

<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';

	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
