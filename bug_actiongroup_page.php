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
	# $Id: bug_actiongroup_page.php,v 1.55.2.1 2007-10-13 22:32:34 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	# This page allows actions to be performed on an array of bugs

	require_once( 'core.php' );

	require_once( $t_core_path.'bug_group_action_api.php' );

	auth_ensure_user_authenticated();

	$f_action = gpc_get_string( 'action', '' );
	$f_bug_arr = gpc_get_int_array( 'bug_arr', array() );

	# redirects to all_bug_page if nothing is selected
	if ( is_blank( $f_action ) || ( 0 == sizeof( $f_bug_arr ) ) ) {
		print_header_redirect( 'view_all_bug_page.php' );
	}

	# run through the issues to see if they are all from one project
	$t_project_id = ALL_PROJECTS;
	$t_multiple_projects = false;
	foreach( $f_bug_arr as $t_bug_id ) {
		$t_bug = bug_get( $t_bug_id );
		if ( $t_project_id != $t_bug->project_id ) {
			if ( ( $t_project_id != ALL_PROJECTS ) && !$t_multiple_projects ) {
				$t_multiple_projects = true;
			} else {
				$t_project_id = $t_bug->project_id;
			}
		}
	}
	if ( $t_multiple_projects ) {
		$t_project_id = ALL_PROJECTS;
	}
	# override the project if necessary
	if( $t_project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the bug we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_project_id;
	}

	$t_finished = false;
	$t_request = '';

	$t_external_action_prefix = 'EXT_';
	if ( strpos( $f_action, $t_external_action_prefix ) === 0 ) {
		$t_form_page = 'bug_actiongroup_ext_page.php';
		require_once( $t_form_page );
		exit;
	}

	$t_custom_group_actions = config_get( 'custom_group_actions' );
	
	foreach( $t_custom_group_actions as $t_custom_group_action ) {
		if ( $f_action == $t_custom_group_action['action'] ) {
			require_once( $t_custom_group_action['form_page'] );
			exit;
		}
	}

	# Check if user selected to update a custom field.
	$t_custom_fields_prefix = 'custom_field_';
	if ( strpos( $f_action, $t_custom_fields_prefix ) === 0 ) {
		$t_custom_field_id = (int)substr( $f_action, strlen( $t_custom_fields_prefix ) );
		$f_action = 'CUSTOM';
	}

	switch ( $f_action )  {
		# Use a simple confirmation page, if close or delete...
		case 'CLOSE' :
			$t_finished 			= true;
			$t_question_title 		= lang_get( 'close_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'close_group_bugs_button' );
			$t_form_name			= 'bug_close';
			break;

		case 'DELETE' :
			$t_finished 			= true;
			$t_question_title		= lang_get( 'delete_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'delete_group_bugs_button' );
			$t_form_name			= 'bug_delete';
			break;

		case 'SET_STICKY' :
			$t_finished 			= true;
			$t_question_title		= lang_get( 'set_sticky_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'set_sticky_group_bugs_button' );
			$t_form_name			= 'bug_set_sticky';
			break;

		# ...else we define the variables used in the form
		case 'MOVE' :
			$t_question_title 		= lang_get( 'move_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'move_group_bugs_button' );
			$t_form					= 'project_id';
			$t_form_name			= 'bug_move';
			break;

		case 'COPY' :
			$t_question_title 		= lang_get( 'copy_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'copy_group_bugs_button' );
			$t_form					= 'project_id';
			$t_form_name			= 'bug_copy';
			break;

		case 'ASSIGN' :
			$t_question_title 		= lang_get( 'assign_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'assign_group_bugs_button' );
			$t_form 				= 'assign';
			$t_form_name			= 'bug_assign';
			break;

		case 'RESOLVE' :
			$t_question_title 		= lang_get( 'resolve_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'resolve_group_bugs_button' );
			$t_form 				= 'resolution';
			$t_request 				= 'resolution'; # the "request" vars allow to display the adequate list
			if ( ALL_PROJECTS != $t_project_id ) {
				$t_question_title2 = lang_get( 'fixed_in_version' );
				$t_form2 = 'fixed_in_version';
			}
			$t_form_name			= 'bug_resolve';
			break;

		case 'UP_PRIOR' :
			$t_question_title 		= lang_get( 'priority_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'priority_group_bugs_button' );
			$t_form 				= 'priority';
			$t_request 				= 'priority';
			$t_form_name			= 'bug_update_priority';
			break;

		case 'UP_STATUS' :
			$t_question_title 		= lang_get( 'status_bugs_conf_msg' );
			$t_button_title 		= lang_get( 'status_group_bugs_button' );
			$t_form 				= 'status';
			$t_request 				= 'status';
			$t_form_name			= 'bug_update_status';
			break;

		case 'UP_CATEGORY' :
			$t_question_title		= lang_get( 'category_bugs_conf_msg' );
			$t_button_title			= lang_get( 'category_group_bugs_button' );
			$t_form					= 'category';
			$t_form_name			= 'bug_update_category';
			break;

		case 'VIEW_STATUS' :
			$t_question_title		= lang_get( 'view_status_bugs_conf_msg' );
			$t_button_title			= lang_get( 'view_status_group_bugs_button' );
			$t_form					= 'view_status';
			$t_form_name			= 'bug_update_view_status';
			break;
		
		case 'UP_FIXED_IN_VERSION':
			$t_question_title		= lang_get( 'fixed_in_version_bugs_conf_msg' );
			$t_button_title			= lang_get( 'fixed_in_version_group_bugs_button' );
			$t_form					= 'fixed_in_version';
			$t_form_name			= 'bug_update_fixed_in_version';
			break;

		case 'UP_TARGET_VERSION':
			$t_question_title		= lang_get( 'target_version_bugs_conf_msg' );
			$t_button_title			= lang_get( 'target_version_group_bugs_button' );
			$t_form					= 'target_version';
			$t_form_name			= 'bug_update_target_version';
			break;

		case 'CUSTOM' :
			$t_custom_field_def = custom_field_get_definition( $t_custom_field_id );
			$t_question_title = sprintf( lang_get( 'actiongroup_menu_update_field' ), lang_get_defaulted( $t_custom_field_def['name'] ) );
			$t_button_title = $t_question_title;
			$t_form = "custom_field_$t_custom_field_id";
			$t_form_name			= 'bug_update_custom_field_' . $t_custom_field_id;
			break;

		default:
			trigger_error( ERROR_GENERIC, ERROR );
	}

	bug_group_action_print_top();
	
	if ( $t_multiple_projects ) {
		echo '<p class="bold">' . lang_get( 'multiple_projects' ) . '</p>';
	}
?>

<br />

<div align="center">
<form method="post" action="bug_actiongroup.php">
<?php
if ( !is_blank( $t_form_name ) ) {
	echo form_security_field( $t_form_name );
}
?>
<input type="hidden" name="action" value="<?php echo string_attribute( $f_action ) ?>" />
<?php
	bug_group_action_print_hidden_fields( $f_bug_arr );

	if ( $f_action === 'CUSTOM' ) {
		echo "<input type=\"hidden\" name=\"custom_field_id\" value=\"$t_custom_field_id\" />";
	}
?>
<table class="width75" cellspacing="1">
<?php
if ( !$t_finished ) {
?>
<tr class="row-1">
	<td class="category">
		<?php echo $t_question_title ?>
	</td>
	<td>
	<?php
		if ( $f_action === 'CUSTOM' ) {
			$t_custom_field_def = custom_field_get_definition( $t_custom_field_id );

			$t_bug_id = null;

			# if there is only one issue, use its current value as default, otherwise,
			# use the default value specified in custom field definition.
			if ( sizeof( $f_bug_arr ) == 1 ) {
				$t_bug_id = $f_bug_arr[0];
			}

			print_custom_field_input( $t_custom_field_def, $t_bug_id );
		} else {
			echo "<select name=\"$t_form\">";

			switch ( $f_action ) {
				case 'COPY':
				case 'MOVE':
					print_project_option_list( null, false );
					break;
				case 'ASSIGN':
					print_assign_to_option_list( 0, $t_project_id );
					break;
				case 'VIEW_STATUS':
					print_enum_string_option_list( 'view_state', config_get( 'default_bug_view_status' ) );
					break;
				case 'UP_CATEGORY':
					print_category_option_list();
					break;
				case 'UP_TARGET_VERSION':
				case 'UP_FIXED_IN_VERSION':
					print_version_option_list( '', $t_project_id, VERSION_ALL );
					break;
			}

			# other forms use the same function to display the list
			if ( $t_request > '' ) {
			echo '<option></option>';			//inserido PROSEGUR 06-07-2010
				print_enum_string_option_list( $t_request);  //alterado para evitar o problema de escolher o status indesejado, antes era print_enum_string_option_list( $t_request, FIXED ); PROSEGUR 06-07-2010
			}

			echo '</select>';
		}
		?>
	</td>
</tr>
	<?php
	if ( isset( $t_question_title2 ) ) {
		switch ( $f_action ) {
			case 'RESOLVE':
				$t_show_version = ( ON == config_get( 'show_product_version' ) )
					|| ( ( AUTO == config_get( 'show_product_version' ) )
								&& ( count( version_get_all_rows( $t_project_id ) ) > 0 ) );
				if ( $t_show_version ) {
	?>
		<tr class="row-2">
			<td class="category">
				<?php echo $t_question_title2 ?>
			</td>
			<td>
				<select name="<?php echo $t_form2 ?>">
					<?php print_version_option_list( '', null, VERSION_ALL );?>
				</select>
			</td>
		</tr>
	<?php
				}
				break;
		}
	}
	?>
<?php
} else {
?>

<tr class="row-1">
	<td class="category" colspan="2">
		<?php echo $t_question_title; ?>
	</td>
</tr>
<?php
}
?>

<!-- Bugnote adicionado para permitir que seja inserido anota��o em todas as a��es feitas em lote PROSEGUR 30-09-2009-->
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'add_bugnote_title' ) ?>
	</td>
	<td class="category">
		<textarea name="bugnote_text" cols="80" rows="10"></textarea>
	</td>
</tr>
<?php if ( access_has_bug_level( config_get( 'private_bugnote_threshold' ), $t_bug_id ) ) { ?>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
<?php
		$t_default_bugnote_view_status = config_get( 'default_bugnote_view_status' );
		if ( access_has_bug_level( config_get( 'set_view_status_threshold' ), $t_bug_id ) ) {
?>
			<input type="checkbox" name="private" <?php check_checked( $t_default_bugnote_view_status, VS_PRIVATE ); ?> />
<?php
			echo lang_get( 'private' );
		} else {
			echo get_enum_element( 'project_view_state', $t_default_bugnote_view_status );
		}
?>
	</td>
</tr>
<?php } ?>

<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo $t_button_title ?>" />
	</td>
</tr>
</table>
<br />

<?php
	bug_group_action_print_bug_list( $f_bug_arr );
?>
</form>
</div>

<?php
	bug_group_action_print_bottom();
?>
