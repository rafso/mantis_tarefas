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
	# $Id: manage_proj_ver_delete.php,v 1.23.2.1 2007-10-13 22:33:47 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'version_api.php' );

	auth_reauthenticate();

	$f_version_id = gpc_get_int( 'version_id' );

	$t_version_info = version_get( $f_version_id );
	$t_redirect_url = 'manage_proj_edit_page.php?project_id=' . $t_version_info->project_id;

	access_ensure_project_level( config_get( 'manage_project_threshold' ), $t_version_info->project_id );

	# Confirm with the user
	helper_ensure_confirmed( lang_get( 'version_delete_sure' ) .
		'<br/>' . lang_get( 'version' ) . ': ' . $t_version_info->version,
		lang_get( 'delete_version_button' ) );

	form_security_validate( 'manage_proj_ver_delete' );
	version_remove( $f_version_id );

	html_page_top1();
	html_meta_redirect( $t_redirect_url );
	html_page_top2();
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ).'<br />';
	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>