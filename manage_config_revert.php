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
	# $Id: manage_config_revert.php,v 1.4.2.1 2007-10-13 22:33:24 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	auth_reauthenticate();

	$f_project_id = gpc_get_int( 'project', 0 );
	$f_revert = gpc_get_string( 'revert', '' );
	$f_return = gpc_get_string( 'return' );

	$t_access = true;
	$t_revert_vars = explode( ',', $f_revert );
	array_walk( $t_revert_vars, 'trim' );
	foreach ( $t_revert_vars as $t_revert ) {
		$t_access &= access_has_project_level( config_get_access( $t_revert ), $f_project_id );
	}
	
	if ( ! $t_access ) {
		access_denied();
	}
	
	if ( '' != $f_revert ) {
		# Confirm with the user
		helper_ensure_confirmed( lang_get( 'config_delete_sure' ) . ': ' .
			string_html_specialchars( implode( ', ', $t_revert_vars ) ) . ' ' . lang_get( 'in_project' ) . ' ' . project_get_name( $f_project_id ),
			lang_get( 'delete_config_button' ) );
		
		form_security_validate('manage_config_revert');
		
		foreach ( $t_revert_vars as $t_revert ) {
			config_delete( $t_revert, null , $f_project_id );
		}
	}

	$t_redirect_url = $f_return;

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
