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
	# $Id: custom_function_api.php,v 1.32.2.1 2007-10-13 22:35:22 giallu Exp $
	# --------------------------------------------------------

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'prepare_api.php' );

	### Custom Function API ###

	# --------------------
	# Checks the provided bug and determines whether it should be included in the changelog
	# or not.
	# returns true: to include, false: to exclude.
	function custom_function_default_changelog_include_issue( $p_issue_id ) {
		$t_issue = bug_get( $p_issue_id );

		return ( ( $t_issue->duplicate_id == 0 ) && ( $t_issue->resolution == FIXED ) &&
			( $t_issue->status >= config_get( 'bug_resolved_status_threshold' ) ) );
	}


	# --------------------
	# Prints one entry in the changelog.
	function custom_function_default_changelog_print_issue( $p_issue_id, $p_issue_level = 0 ) {
		$t_bug = bug_get( $p_issue_id );

		$t_category = is_blank( $t_bug->category ) ? '' : '<b>[' . $t_bug->category . ']</b> ';
		echo str_pad( '', $p_issue_level * 6, '&nbsp;' ), '- ', string_get_bug_view_link( $p_issue_id ), ': ', $t_category, string_display_line_links( $t_bug->summary );

		if ( $t_bug->handler_id != 0 ) {
			echo ' (', prepare_user_name( $t_bug->handler_id ), ')';
		}

		echo ' - ', get_enum_element( 'status', $t_bug->status ), '.<br />';
	}

	# --------------------
	# Checks the provided bug and determines whether it should be included in the roadmap or not.
	# returns true: to include, false: to exclude.
	function custom_function_default_roadmap_include_issue( $p_issue_id ) {
		$t_issue = bug_get( $p_issue_id );

		return ( $t_issue->duplicate_id == 0 );
	}

	# --------------------
	# Prints one entry in the roadmap.
	function custom_function_default_roadmap_print_issue( $p_issue_id, $p_issue_level = 0 ) {
		$t_bug = bug_get( $p_issue_id );
		
		if ( bug_is_resolved( $p_issue_id ) ) {
			$t_strike_start = '<strike>';
			$t_strike_end = '</strike>';
		} else {
			$t_strike_start = $t_strike_end = '';
		}
		
		$t_category = is_blank( $t_bug->category ) ? '' : '<b>[' . $t_bug->category . ']</b> ';
		echo str_pad( '', $p_issue_level * 6, '&nbsp;' ), '- ', $t_strike_start, string_get_bug_view_link( $p_issue_id ), ': ', $t_category, string_display_line_links( $t_bug->summary );

		if ( $t_bug->handler_id != 0 ) {
			echo ' (', prepare_user_name( $t_bug->handler_id ), ')';
		}

		echo ' - ', get_enum_element( 'status', $t_bug->status ), $t_strike_end, '.<br />';
	}

	# --------------------
	# format the bug summary.
	function custom_function_default_format_issue_summary( $p_issue_id, $p_context=0 ) {
		switch ( $p_context ) {
			case SUMMARY_CAPTION:
				$t_string = bug_format_id( $p_issue_id ) . ': ' . string_attribute( bug_get_field( $p_issue_id, 'summary' ) );
				break;
			case SUMMARY_FIELD:
				$t_string = bug_format_id( $p_issue_id ) . ': ' . string_display_line_links( bug_get_field( $p_issue_id, 'summary' ) );
				break;
			case SUMMARY_EMAIL:
				$t_string = bug_format_id( $p_issue_id ) . ': ' . string_attribute( bug_get_field( $p_issue_id, 'summary' ) );
				break;
			default:
				$t_string = string_attribute( bug_get_field( $p_issue_id, 'summary' ) );
				break;
		}
		return $t_string;
	}

	# --------------------
	# Register a checkin in source control by adding a history entry and a note
	# This can be overriden to do extra work.
	# The issue status/resolution would only be set if the issue is fixed, and hence $p_fixed is passed as true.
	function custom_function_default_checkin( $p_issue_id, $p_comment, $p_file, $p_new_version, $p_fixed ) {
		if ( bug_exists( $p_issue_id ) ) {
			history_log_event_special( $p_issue_id, CHECKIN, $p_file, $p_new_version );
			bugnote_add( $p_issue_id, $p_comment, 0, VS_PRIVATE == config_get( 'source_control_notes_view_status' ) );

			$t_status = config_get( 'source_control_set_status_to' );
			if ( ( OFF != $t_status ) && $p_fixed ) {
				bug_set_field( $p_issue_id, 'status', $t_status );
				bug_set_field( $p_issue_id, 'resolution', config_get( 'source_control_set_resolution_to' ) );
			}
		}
	}

	# --------------------
	# Hook to validate field issue data before updating
	# Verify that the proper fields are set with the appropriate values before proceeding
	# to change the status.
	# In case of invalid data, this function should call trigger_error()
	# p_issue_id is the issue number that can be used to get the existing state
	# p_new_issue_data is an object (BugData) with the appropriate fields updated
	function custom_function_default_issue_update_validate( $p_issue_id, $p_new_issue_data, $p_bugnote_text ) {
	}

	# --------------------
	# Hook to notify after an issue has been updated.
	# In case of errors, this function should call trigger_error()
	# p_issue_id is the issue number that can be used to get the existing state
	function custom_function_default_issue_update_notify( $p_issue_id ) {
	}

	# --------------------
	# Hook to validate field settings before creating an issue
	# Verify that the proper fields are set before proceeding to create an issue
	# In case of errors, this function should call trigger_error()
	# p_new_issue_data is an object (BugData) with the appropriate fields updated
	function custom_function_default_issue_create_validate( $p_new_issue_data ) {
	}

	# --------------------
	# Hook to notify after aa issue has been created.
	# In case of errors, this function should call trigger_error()
	# p_issue_id is the issue number that can be used to get the existing state
	function custom_function_default_issue_create_notify( $p_issue_id ) {
	}

	# --------------------
	# Hook to validate field settings before deleting an issue.
	# Verify that the issue can be deleted before the actual deletion.
	# In the case that the issue should not be deleted, this function should
	# call trigger_error().
	# p_issue_id is the issue number that can be used to get the existing state
	function custom_function_default_issue_delete_validate( $p_issue_id ) {
	}

	# --------------------
	# Hook to notify after an issue has been deleted.
	# p_issue_data is the issue data (BugData) that reflects the last status of the
	# issue before it was deleted.
	function custom_function_default_issue_delete_notify( $p_issue_data ) {
	}

	# --------------------
	# Hook for authentication
	# can Mantis update the password
	function custom_function_default_auth_can_change_password( ) {
		$t_can_change = array( PLAIN, CRYPT, CRYPT_FULL_SALT, MD5 );
		if ( in_array( config_get( 'login_method' ), $t_can_change ) ) {
			return true;
		} else {
			return false;
		}
	}

	# --------------------
	# returns an array of the column names to be displayed.
	# The column names to use are those of the field names in the bug table.
	# In addition, you can use the following:
	# - "selection" for selection checkboxes.
	# - "edit" for icon to open the edit page.
	# - "custom_xxxx" were xxxx is the name of the custom field that is valid for the
	#   current project.  In case of "All Projects, the field will be empty where it is
	#   not applicable.
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function custom_function_default_get_columns_to_view( $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target == COLUMNS_TARGET_CSV_PAGE ) {
			$t_columns = config_get( 'csv_columns' );
		} else if ( $p_columns_target == COLUMNS_TARGET_VIEW_PAGE ) {
			$t_columns = config_get( 'view_issues_page_columns' );
			//adicionado para trazer as colunas do relatório parcial PROSEGUR 07/06/2011
		} else if ($p_columns_target == 'view_relationship_page_columns')
			{ 
			$t_columns = config_get( 'view_relationship_page_columns' );
		} else {
			$t_columns = config_get( 'print_issues_page_columns' );
		} 

		return $t_columns;
	}

	# --------------------
	# Print the title of a column given its name.
	# $p_column: custom_xxx for custom field xxx, or otherwise field name as in bug table.
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function custom_function_default_print_column_title( $p_column, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_sort, $t_dir;

		if ( strpos( $p_column, 'custom_' ) === 0 ) {
			$t_custom_field = substr( $p_column, 7 );

			if ( COLUMNS_TARGET_CSV_PAGE != $p_columns_target ) {
				echo '<td>';
			}

			$t_field_id = custom_field_get_id_from_name( $t_custom_field );
			if ( $t_field_id === false ) {
				echo '@', $t_custom_field, '@';
			} else {
				$t_def = custom_field_get_definition( $t_field_id );
				$t_custom_field = lang_get_defaulted( $t_def['name'] );

				if ( COLUMNS_TARGET_CSV_PAGE != $p_columns_target ) {
					print_view_bug_sort_link( $t_custom_field, $p_column, $t_sort, $t_dir, $p_columns_target );
					print_sort_icon( $t_dir, $t_sort, $p_column );
				} else {
					echo $t_custom_field;
				}
			}

			if ( COLUMNS_TARGET_CSV_PAGE != $p_columns_target ) {
				echo '</td>';
			}
		} else {
			$t_function = 'print_column_title_' . $p_column;
			if ( function_exists( $t_function ) ) {
				$t_function( $t_sort, $t_dir, $p_columns_target );
			} else {
				echo '<td>';
				print_view_bug_sort_link( lang_get_defaulted( $p_column ), $p_column, $t_sort, $t_dir, $p_columns_target );
//				print_sort_icon( $t_dir, $t_sort, $p_column );
				echo '</td>';
			}
		}
	}

	# --------------------
	# Print the value of the custom field (if the field is applicable to the project of
	# the specified issue and the current user has read access to it.
	# see custom_function_default_print_column_title() for rules about column names.
	# $p_column: name of field to show in the column.
	# $p_row: the row from the bug table that belongs to the issue that we should print the values for.
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function custom_function_default_print_column_value( $p_column, $p_issue_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( COLUMNS_TARGET_CSV_PAGE == $p_columns_target ) {
			$t_column_start = '';
			$t_column_end = '';
			$t_column_empty = '';
		} else {
			$t_column_start = '<td>';
			$t_column_end = '</td>';
			$t_column_empty = '&nbsp;';
		}

		if ( strpos( $p_column, 'custom_' ) === 0 ) {
			echo $t_column_start;
			$t_custom_field = substr( $p_column, 7 );

			$t_field_id = custom_field_get_id_from_name( $t_custom_field );
			if ( $t_field_id === false ) {
				echo '@', $t_custom_field, '@';
			} else {
				$t_issue_id = $p_issue_row['id'];
				$t_project_id = $p_issue_row['project_id'];

				if ( custom_field_is_linked( $t_field_id, $t_project_id ) ) {
					$t_def = custom_field_get_definition( $t_field_id );
					print_custom_field_value( $t_def, $t_field_id, $t_issue_id );
				} else {
					// field is not linked to project
					echo $t_column_empty;
				}
			}
			echo $t_column_end;
		} else {
			if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
				$t_function = 'print_column_' . $p_column;
			} else {
				$t_function = 'csv_format_' . $p_column;
			}

			if ( function_exists( $t_function ) ) {
				if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
					$t_function( $p_issue_row, $p_columns_target );
				} else {
					$t_function( $p_issue_row[$p_column] );
				}
			} else {
				if ( isset( $p_issue_row[$p_column] ) ) {
					echo $t_column_start . $p_issue_row[$p_column] . $t_column_end;
				} else {
					echo $t_column_start . '@' . $p_column . '@' . $t_column_end;
				}
			}
		}
	}

	# --------------------
	# Construct an enumeration for all versions for the current project.
	# The enumeration will be empty if current project is ALL PROJECTS.
	# Enumerations format is: "abc|lmn|xyz"
	# To use this in a custom field type "=versions" in the possible values field.
	function custom_function_default_enum_versions() {
		$t_versions = version_get_all_rows( helper_get_current_project() );

		$t_enum = array();
		foreach( $t_versions as $t_version ) {
			$t_enum[] = $t_version['version'];
		}

		$t_possible_values = implode( '|', $t_enum );

		return $t_possible_values;
	}

	# --------------------
	# Construct an enumeration for released versions for the current project.
	# The enumeration will be empty if current project is ALL PROJECTS.
	# Enumerations format is: "abc|lmn|xyz"
	# To use this in a custom field type "=released_versions" in the possible values field.
	function custom_function_default_enum_released_versions() {
		$t_versions = version_get_all_rows( helper_get_current_project() );

		$t_enum = array();
		foreach( $t_versions as $t_version ) {
			if ( $t_version['released'] == 1 ) {
				$t_enum[] = $t_version['version'];
			}
		}

		$t_possible_values = implode( '|', $t_enum );

		return $t_possible_values;
	}

	# --------------------
	# Construct an enumeration for released versions for the current project.
	# The enumeration will be empty if current project is ALL PROJECTS.
	# Enumerations format is: "abc|lmn|xyz"
	# To use this in a custom field type "=future_versions" in the possible values field.
	function custom_function_default_enum_future_versions() {
		$t_versions = version_get_all_rows( helper_get_current_project() );

		$t_enum = array();
		foreach( $t_versions as $t_version ) {
			if ( $t_version['released'] == 0 ) {
				$t_enum[] = $t_version['version'];
			}
		}

		$t_possible_values = implode( '|', $t_enum );

		return $t_possible_values;
	}

	# --------------------
	# Construct an enumeration for all categories for the current project.
	# The enumeration will be empty if current project is ALL PROJECTS.
	# Enumerations format is: "abc|lmn|xyz"
	# To use this in a custom field type "=categories" in the possible values field.
	function custom_function_default_enum_categories() {
		$t_categories = category_get_all_rows( helper_get_current_project() );

		$t_enum = array();
		foreach( $t_categories as $t_category ) {
			$t_enum[] = $t_category['category'];
		}

		$t_possible_values = implode( '|', $t_enum );

		return $t_possible_values;
	}

	# --------------------
	# This function prints the custom buttons on the current view page based on specified bug id
	# and the context.  The printing of the buttons will typically call html_button() from
	# html_api.php.  For each button, this function needs to generate the enclosing '<td>' and '</td>'.
	function custom_function_default_print_bug_view_page_custom_buttons( $p_bug_id ) {
	}
?>
