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
	# $Id: manage_proj_user_remove.php,v 1.9.2.1 2007-10-13 22:33:45 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	auth_reauthenticate();

	$f_project_id = gpc_get_int( 'project_id' );
	$f_user_id = gpc_get_int( 'user_id', 0 );

	# We should check both since we are in the project section and an
	#  admin might raise the first threshold and not realize they need
	#  to raise the second
	access_ensure_project_level( config_get( 'manage_project_threshold' ), $f_project_id );
	access_ensure_project_level( config_get( 'project_user_threshold' ), $f_project_id );

	if ( 0 == $f_user_id ) {
		# Confirm with the user
		helper_ensure_confirmed( lang_get( 'remove_all_users_sure_msg' ), lang_get( 'remove_all_users_button' ) );

		form_security_validate( 'manage_proj_user_remove' );

		project_remove_all_users( $f_project_id );
	}
	else {
		$t_user = user_get_row( $f_user_id );
		# Confirm with the user
		helper_ensure_confirmed( lang_get( 'remove_user_sure_msg' ) .
			'<br/>' . lang_get( 'username' ) . ': ' . $t_user['username'],
			lang_get( 'remove_user_button' ) );

		form_security_validate( 'manage_proj_user_remove' );

		project_remove_user( $f_project_id, $f_user_id );
	}

	$t_redirect_url = 'manage_proj_edit_page.php?project_id=' . $f_project_id;

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
