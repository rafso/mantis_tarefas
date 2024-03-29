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
	# $Id: news_add.php,v 1.32.22.1 2007-10-13 22:34:00 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'news_api.php' );
	require_once( $t_core_path.'print_api.php' );

	form_security_validate( 'news_add' );

	access_ensure_project_level( config_get( 'manage_news_threshold' ) );

	$f_view_state	= gpc_get_int( 'view_state' );
	$f_headline		= gpc_get_string( 'headline' );
	$f_announcement	= gpc_get_bool( 'announcement' );
	$f_body			= gpc_get_string( 'body' );

	$t_news_id = news_create( helper_get_current_project(), auth_get_current_user_id(), $f_view_state, $f_announcement, $f_headline, $f_body );

	$t_news_row = news_get_row( $t_news_id );
?>
<?php html_page_top1() ?>
<?php html_page_top2() ?>

<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( 'news_menu_page.php', lang_get( 'proceed' ) );

	echo '<br /><br />';

	print_news_entry_from_row( $t_news_row );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
