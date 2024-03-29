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
	# $Id: bug_actiongroup.php,v 1.52.2.1 2007-10-13 22:32:30 giallu Exp $
	# --------------------------------------------------------

	# This page allows actions to be performed an an array of bugs

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'bug_util_api.php' );  //arquivo com utilitarios PROSEGUR

	auth_ensure_user_authenticated();
	helper_begin_long_process();

	$f_action	= gpc_get_string( 'action' );
	$f_custom_field_id = gpc_get_int( 'custom_field_id', 0 );
	$f_bug_arr	= gpc_get_int_array( 'bug_arr', array() );
	
	#recebe o texto da anota��o e se ela � privada ou p�blica PROSEGUR
	$f_bugnote_text	= gpc_get_string( 'bugnote_text', '' );
	$f_private		= gpc_get_bool( 'private' );

	$t_custom_group_actions = config_get( 'custom_group_actions' );

	foreach( $t_custom_group_actions as $t_custom_group_action ) {
		if ( $f_action == $t_custom_group_action['action'] ) {
			require_once( $t_custom_group_action['action_page'] );
			exit;
		}
	}

	$t_failed_ids = array();

	if ( 0 != $f_custom_field_id ) {
		$t_custom_field_def = custom_field_get_definition( $f_custom_field_id );
	}

	$t_first_issue = true;

	foreach( $f_bug_arr as $t_bug_id ) {
		bug_ensure_exists( $t_bug_id );
		$t_bug = bug_get( $t_bug_id, true );

		if( $t_bug->project_id != helper_get_current_project() ) {
			# in case the current project is not the same project of the bug we are viewing...
			# ... override the current project. This to avoid problems with categories and handlers lists etc.
			$g_project_override = $t_bug->project_id;
			# @@@ (thraxisp) the next line goes away if the cache was smarter and used project
			config_flush_cache(); # flush the config cache so that configs are refetched
		}

		$t_status = $t_bug->status;

		switch ( $f_action ) {

		case 'CLOSE':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_close' );
			}

			if ( access_can_close_bug( $t_bug_id ) &&
					( $t_status < CLOSED ) &&
					bug_check_workflow($t_status, CLOSED) ) {

				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $f_bug_id, $t_bug_data, $f_bugnote_text ) );
				bug_close( $t_bug_id );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
				helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			} else {
				if ( ! access_can_close_bug( $t_bug_id ) ) {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
				} else {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_status' );
				}
			}
			break;

		case 'DELETE':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_delete' );
			}

			if ( access_has_bug_level( config_get( 'delete_bug_threshold' ), $t_bug_id ) ) {
				bug_delete( $t_bug_id );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'MOVE':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_move' );
			}

			if ( access_has_bug_level( config_get( 'move_bug_threshold' ), $t_bug_id ) ) {
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
				$f_project_id = gpc_get_int( 'project_id' );
				bug_set_field( $t_bug_id, 'project_id', $f_project_id );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
				helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'COPY':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_copy' );
			}

			$f_project_id = gpc_get_int( 'project_id' );

			if ( access_has_project_level( config_get( 'report_bug_threshold' ), $f_project_id ) ) {
				bug_copy( $t_bug_id, $f_project_id, true, true, true, true, true, true );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'ASSIGN':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_assign' );
			}

			$f_assign = gpc_get_int( 'assign' );
			if ( ON == config_get( 'auto_set_status_to_assigned' ) ) {
				$t_assign_status = config_get( 'bug_assigned_status' );
			} else {
				$t_assign_status = $t_status;
			}
			# check that new handler has rights to handle the issue, and
			#  that current user has rights to assign the issue
			$t_threshold = access_get_status_threshold( $t_assign_status, bug_get_field( $t_bug_id, 'project_id' ) );
			if ( access_has_bug_level( $t_threshold , $t_bug_id, $f_assign ) &&
				 access_has_bug_level( config_get( 'update_bug_assign_threshold', config_get( 'update_bug_threshold' ) ), $t_bug_id ) &&
					bug_check_workflow($t_status, $t_assign_status )	) {
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
				bug_assign( $t_bug_id, $f_assign );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
				helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			} else {
				if ( bug_check_workflow($t_status, $t_assign_status ) ) {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
				} else {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_status' );
				}
			}
			break;

		case 'RESOLVE':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_resolve' );
			}

			$t_resolved_status = config_get( 'bug_resolved_status_threshold' );
			if ( access_has_bug_level( access_get_status_threshold( $t_resolved_status, bug_get_field( $t_bug_id, 'project_id' ) ), $t_bug_id ) &&
				 		( $t_status < $t_resolved_status ) &&
						bug_check_workflow($t_status, $t_resolved_status ) ) {
				$f_resolution = gpc_get_int( 'resolution' );
				$f_fixed_in_version = gpc_get_string( 'fixed_in_version', '' );
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
				bug_resolve( $t_bug_id, $f_resolution, $f_fixed_in_version );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
				helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			} else {
				if ( ( $t_status < $t_resolved_status ) &&
						bug_check_workflow($t_status, $t_resolved_status ) ) {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
				} else {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_status' );
				}
			}
			break;

		case 'UP_PRIOR':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_update_priority' );
			}

			if ( access_has_bug_level( config_get( 'update_bug_threshold' ), $t_bug_id ) ) {
				$f_priority = gpc_get_int( 'priority' );
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
				bug_set_field( $t_bug_id, 'priority', $f_priority );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
				helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'UP_STATUS':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_update_status' );
			}

			$f_status = gpc_get_int( 'status' );
			$t_project = bug_get_field( $t_bug_id, 'project_id' );
			if ( access_has_bug_level( access_get_status_threshold( $f_status, $t_project ), $t_bug_id ) ) {
				if ( TRUE == bug_check_workflow($t_status, $f_status ) ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
					bug_set_field( $t_bug_id, 'status', $f_status );
					bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
					helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
					//inserido para enviar e-mail mesmo que seja utilizado essa op��o, antes n�o enviava PROSEGUR 06/07/2009
					//chama a fun��o send_mail_status que envia um e-mail de acordo com status que ser� alterado
					send_mail_status($t_bug_id, $f_status);  //chama a fun��o passadno o bug e o novo status
				} else {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_status' );
				}
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'UP_CATEGORY':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_update_category' );
			}

			$f_category = gpc_get_string( 'category' );
			$t_project = bug_get_field( $t_bug_id, 'project_id' );
			if ( access_has_bug_level( config_get( 'update_bug_threshold' ), $t_bug_id ) ) {
				if ( category_exists( $t_project, $f_category ) ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
					bug_set_field( $t_bug_id, 'category', $f_category );
					bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
					helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
				} else {
					$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_category' );
				}
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;
		
		case 'UP_FIXED_IN_VERSION':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_update_fixed_in_version' );
			}

			$f_fixed_in_version = gpc_get_string( 'fixed_in_version' );
			$t_project_id = bug_get_field( $t_bug_id, 'project_id' );
			$t_success = false;

			if ( access_has_bug_level( config_get( 'update_bug_threshold' ), $t_bug_id ) ) {
				if ( version_get_id( $f_fixed_in_version, $t_project_id ) !== false ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
					bug_set_field( $t_bug_id, 'fixed_in_version', $f_fixed_in_version );
					bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
					helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
					$t_success = true;
				}
			}

			if ( !$t_success ) {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'UP_TARGET_VERSION':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_update_target_version' );
			}

			$f_target_version = gpc_get_string( 'target_version' );
			$t_project_id = bug_get_field( $t_bug_id, 'project_id' );
			$t_success = false;

			if ( access_has_bug_level( config_get( 'roadmap_update_threshold' ), $t_bug_id ) ) {
				if ( version_get_id( $f_target_version, $t_project_id ) !== false ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
					bug_set_field( $t_bug_id, 'target_version', $f_target_version );
					bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
					helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
					$t_success = true;
				}
			}

			if ( !$t_success ) {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'VIEW_STATUS':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_update_view_status' );
			}

			if ( access_has_bug_level( config_get( 'change_view_status_threshold' ), $t_bug_id ) ) {
				$f_view_status = gpc_get_int( 'view_status' );
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
				bug_set_field( $t_bug_id, 'view_state', $f_view_status );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
				helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'SET_STICKY':
			if ( $t_first_issue ) {
				form_security_validate( 'bug_set_sticky' );
			}

			if ( access_has_bug_level( config_get( 'set_bug_sticky_threshold' ), $t_bug_id ) ) {
				$f_sticky = bug_get_field( $t_bug_id, 'sticky' );
				// The new value is the inverted old value
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
				bug_set_field( $t_bug_id, 'sticky', intval( !$f_sticky ) );
				bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
				helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			} else {
				$t_failed_ids[$t_bug_id] = lang_get( 'bug_actiongroup_access' );
			}
			break;

		case 'CUSTOM':
			if ( 0 === $f_custom_field_id ) {
				trigger_error( ERROR_GENERIC, ERROR );
			}

			if ( $t_first_issue ) {
				form_security_validate( 'bug_update_custom_field_' . $f_custom_field_id );
			}

			# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_bug_id, $t_bug_data, $f_bugnote_text ) );
			$t_form_var = "custom_field_$f_custom_field_id";
			$t_custom_field_value = gpc_get_custom_field( $t_form_var, $t_custom_field_def['type'], null );
			custom_field_set_value( $f_custom_field_id, $t_bug_id, $t_custom_field_value );
			bugnote_add( $t_bug_id, $f_bugnote_text, '0:00', $f_private );
			helper_call_custom_function( 'issue_update_notify', array( $t_bug_id ) );
			break;

		default:
			trigger_error( ERROR_GENERIC, ERROR );
		}

		$t_first_issue = false;
	}

	$t_redirect_url = 'view_all_bug_page.php';

	if ( count( $t_failed_ids ) > 0 ) {
		html_page_top1();
		html_page_top2();

		echo '<div align="center"><br />';
		echo '<table class="width75">';
		foreach( $t_failed_ids as $t_id => $t_reason ) {
			printf( "<tr><td width=\"50%%\">%s: %s</td><td>%s</td></tr>\n", string_get_bug_view_link( $t_id ), bug_get_field( $t_id, 'summary' ), $t_reason );
		}
		echo '</table><br />';
		print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
		echo '</div>';

		html_page_bottom1( __FILE__ );
	} else {
		print_header_redirect( $t_redirect_url );
	}
?>
