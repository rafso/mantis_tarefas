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
	# $Id: columns_api.php,v 1.21.2.1 2007-10-13 22:35:18 giallu Exp $
	# --------------------------------------------------------

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_selection( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			echo '</td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_edit( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td> &nbsp; </td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'id' ), 'id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'id' );
			echo '</td>';
		} else {
			echo lang_get( 'id' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_project_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'email_project' ), 'project_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'project_id' );
			echo '</td>';
		} else {
			echo lang_get( 'email_project' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_duplicate_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'duplicate_id' ), 'duplicate_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'duplicate_id' );
			echo '</td>';
		} else {
			echo lang_get( 'duplicate_id' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_reporter_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'reporter' ), 'reporter_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'reporter_id' );
			echo '</td>';
		} else {
			echo lang_get( 'reporter' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_handler_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'assigned_to' ), 'handler_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'handler_id' );
			echo '</td>';
		} else {
			echo lang_get( 'assigned_to' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_priority( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'priority_abbreviation' ), 'priority', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'priority' );
			echo '</td>';
		} else {
			echo lang_get( 'priority' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_reproducibility( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'reproducibility' ), 'reproducibility', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'reproducibility' );
			echo '</td>';
		} else {
			echo lang_get( 'reproducibility' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_projection( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'projection' ), 'projection', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'projection' );
			echo '</td>';
		} else {
			echo lang_get( 'projection' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_eta( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'eta' ), 'eta', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'eta' );
			echo '</td>';
		} else {
			echo lang_get( 'eta' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_resolution( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'resolution' ), 'resolution', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'resolution' );
			echo '</td>';
		} else {
			echo lang_get( 'resolution' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_fixed_in_version( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'fixed_in_version' ), 'fixed_in_version', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'fixed_in_version' );
			echo '</td>';
		} else {
			echo lang_get( 'fixed_in_version' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_target_version( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'target_version' ), 'target_version', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'target_version' );
			echo '</td>';
		} else {
			echo lang_get( 'target_version' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_view_state( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'view_status' ), 'view_state', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'view_state' );
			echo '</td>';
		} else {
			echo lang_get( 'view_status' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_os( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'os' ), 'os', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'os' );
			echo '</td>';
		} else {
			echo lang_get( 'os' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_os_build( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'os_version' ), 'os_build', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'os_build' );
			echo '</td>';
		} else {
			echo lang_get( 'os_version' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_platform( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'platform' ), 'platform', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'platform' );
			echo '</td>';
		} else {
			echo lang_get( 'platform' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_version( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'product_version' ), 'version', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'version' );
			echo '</td>';
		} else {
			echo lang_get( 'product_version' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_date_submitted( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'date_submitted' ), 'date_submitted', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'date_submitted' );
			echo '</td>';
		} else {
			echo lang_get( 'date_submitted' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_attachment( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			global $t_icon_path;

			$t_show_attachments = config_get( 'show_attachment_indicator' );

			if ( ON == $t_show_attachments ) {
				echo "\t<td>";
				echo '<img src="' . $t_icon_path . 'attachment.png' . '" alt="" />';
				echo "</td>\n";
			}
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_category( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
//		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			echo lang_get( 'email_project' );
//			print_view_bug_sort_link( lang_get( 'category' ), 'category', $p_sort, $p_dir, $p_columns_target );
//			print_sort_icon( $p_dir, $p_sort, 'category' );
			echo '</td>';
//		} else {
//			echo lang_get( 'category' );
//		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_sponsorship_total( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		$t_enable_sponsorship = config_get( 'enable_sponsorship' );

		if ( ON == $t_enable_sponsorship ) {
			if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
				echo "\t<td>";
				print_view_bug_sort_link( sponsorship_get_currency(), 'sponsorship_total', $p_sort, $p_dir, $p_columns_target );
				print_sort_icon( $p_dir, $p_sort, 'sponsorship_total' );
				echo "</td>\n";
			} else {
				echo sponsorship_get_currency();
			}
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_severity( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'severity' ), 'severity', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'severity' );
			echo '</td>';
		} else {
			echo lang_get( 'severity' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_status( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'status' ), 'status', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'status' );
			echo '</td>';
		} else {
			echo  lang_get( 'status' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_last_updated( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'updated' ), 'last_updated', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'last_updated' );
			echo '</td>';
		} else {
			echo lang_get( 'updated' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_summary( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_bug_sort_link( lang_get( 'summary' ), 'summary', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'summary' );
			echo '</td>';
		} else {
			echo lang_get( 'summary' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_bugnotes_count( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td> # </td>';
		} else {
			echo '#';
		}
	}
	
	# Inserido para que a coluna de andamento de uma tarefa seja exibida na lista de tarefas PROSEGUR 24-05-2011
	function print_column_title_coverage( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
				echo '<td>';
				echo lang_get('coverage');
				echo '</td>';
	}
	
	#inserido para a coluna de tipo da tarefa PROSEGUR 06/03/2011
	function print_column_title_type_task () {
		echo "<td>";
		echo lang_get('configuration_option_type');
		echo "</td>";
	}
	function print_column_type_task ( $p_row ) {
	echo "<td><center>";
	echo get_enum_element( 'type_task', $p_row['type_task'] );
	echo "</center></td>";
	}
	
#inserido para a coluna de data de IN�CIO da tarefa da tarefa PROSEGUR 06/03/2011
function print_column_title_date_s () {
	echo "<td>";
	echo lang_get('start_date');
	echo "</td>";
}
function print_column_date_s ( $p_row ) {
	$tstamp = new DateTime($p_row['date_start']);
	echo "<td><center>";
	echo date( config_get( 'short_date_format' ), $tstamp->format('U'));
	echo "</center></td>";
}
	
#inserido para a coluna de data de conclus�o da tarefa da tarefa PROSEGUR 06/03/2011
	function print_column_title_date_t () {
		echo "<td>";
		echo lang_get('end_date');
		echo "</td>";
	}
	function print_column_date_t ( $p_row ) {
		$tstamp = new DateTime($p_row['date_end']);
		echo "<td><center>";
		echo date( config_get( 'short_date_format' ), $tstamp->format('U'));
		echo "</center></td>";
	}

	//coluna de bugs cr�ticos do relat�rio parcial	
	function print_column_title_bugs () {
		echo "<td>";
		echo lang_get('bugs');
		echo "</td>";
	}
	
	//coluna de observa��o cr�ticos do relat�rio parcial	
	function print_column_title_notes_task () {
		echo "<td>";
		echo lang_get('notes_task');
		echo "</td>";
	}
	
	# Inserido para que a coluna de totais de bugs de uma demanda seja exibida na lista de demandas PROSEGUR 25/08/2010
	function print_column_coverage( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
	//if (access_has_global_level(config_get('set_relationship_filter_bugs'))) {   //verificar se possui permiss�o de visualizar a coluna
		echo '<td class="right1">';
		echo '<label id="coverageValue'. $p_row["id"] .'">'. $p_row["coverage"] . '</label>%';
		//verifica se j� foi enviado com 100% para exibir o simbolo indicando
		if (isset($p_row["report_submitted"])) {
		echo '<image src="images/ok2.png" alt="'. lang_get("task_submitted") .'" hspace="6"/>';
		} else {
			echo '<image class="linkImg" src="images/update.png" alt="'. lang_get("update_coverage") .'" onclick="showObj(\'changeCoverage'. $p_row["id"] .'\')" hspace="8" />';
		}
		echo '<div class="divChangCoverageViewPage" id="changeCoverage'. $p_row["id"] .'"><img class="imgDivChange" src="images/fechar1.png" onclick="showObj(\'changeCoverage'. $p_row["id"] .'\')" >';
		echo lang_get('new_value');
		echo ':<br/><br/>';
		echo '<input type="text" size="6" id="inputValue'. $p_row["id"] .'" value="'. $p_row["coverage"] .'"> <input class="button-small" type="button" value="Ok" onclick="updateCoverage('. $p_row["id"] .', \'inputValue'. $p_row["id"] .'\' )">';
		echo '</div>';
		echo '</td>';
		//}  //fim da valida��o de permiss�o
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_selection( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			global $t_checkboxes_exist, $t_update_bug_threshold;

			echo '<td><center>';
			if ( access_has_bug_level( $t_update_bug_threshold, $p_row['id'] ) ) {
				$t_checkboxes_exist = true;
				printf( "<input type=\"checkbox\" name=\"bug_arr[]\" value=\"%d\" />" , $p_row['id'] );
			} else {
				echo "&nbsp;";
			}
		echo '</center></td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_edit( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			global $t_icon_path, $t_update_bug_threshold;

			echo '<td><center>';
			if ( !bug_is_readonly( $p_row['id'] )
		  		&& access_has_bug_level( $t_update_bug_threshold, $p_row['id'] ) ) {
				echo '<a href="' . string_get_bug_update_url( $p_row['id'] ) . '">';
				echo '<img border="0" width="16" height="16" src="' . $t_icon_path . 'update.png';
				echo '" alt="' . lang_get( 'update_bug_button' ) . '"';
				echo ' title="' . lang_get( 'update_bug_button' ) . '" /></a>';
			} else {
				echo '&nbsp;';
			}
		echo '</center></td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_priority( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td><center>';
			if ( ON == config_get( 'show_priority_text' ) ) {
				print_formatted_priority_string( $p_row['status'], $p_row['priority'] );
			} else {
				print_status_icon( $p_row['priority'] );
			}
		echo '</td>';
		} else {
			echo get_enum_element( 'priority', $p_row['priority'] );
			echo "</center>";
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_id( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
	echo '<td><center>';
		print_bug_link( $p_row['id'], false );
	echo '</center></td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_sponsorship_total( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		$t_enable_sponsorship = config_get( 'enable_sponsorship' );

		if ( $t_enable_sponsorship == ON ) {
			echo "\t<td class=\"right\">";
			if ( $p_row['sponsorship_total'] > 0 ) {
				$t_sponsorship_amount = sponsorship_format_amount( $p_row['sponsorship_total'] );
				echo string_no_break( $t_sponsorship_amount );
			}
			echo "</td>\n";
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_bugnotes_count( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_filter;

		# grab the bugnote count
		$t_bugnote_stats = bug_get_bugnote_stats( $p_row['id'] );
		if ( NULL !== $t_bugnote_stats ) {
			$bugnote_count = $t_bugnote_stats['count'];
			$v_bugnote_updated = $t_bugnote_stats['last_modified'];
		} else {
			$bugnote_count = 0;
		}

		echo '<td class="center">';
		if ( $bugnote_count > 0 ) {
			$t_bugnote_link = '<a href="' . string_get_bug_view_url( $p_row['id'] )
				. '&amp;nbn=' . $bugnote_count . '#bugnotes">'
				. $bugnote_count . '</a>';

			if ( $v_bugnote_updated > strtotime( '-'.$t_filter['highlight_changed'].' hours' ) ) {
				printf( '<span class="bold">%s</span>', $t_bugnote_link );
			} else {
				echo $t_bugnote_link;
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_attachment( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_icon_path;

		$t_show_attachments = config_get( 'show_attachment_indicator' );

		# Check for attachments
		$t_attachment_count = 0;
		if ( ( ON == $t_show_attachments )
		  && ( file_can_view_bug_attachments( $p_row['id'] ) ) ) {
			$t_attachment_count = file_bug_attachment_count( $p_row['id'] );
		}

		if ( ON == $t_show_attachments ) {
			echo "\t<td>";
			if ( 0 < $t_attachment_count ) {
				echo '<a href="' . string_get_bug_view_url( $p_row['id'] ) . '#attachments">';
				echo '<img border="0" src="' . $t_icon_path . 'attachment.png' . '"';
				echo ' alt="' . lang_get( 'attachment_alt' ) . '"';
				echo ' title="' . $t_attachment_count . ' ' . lang_get( 'attachments' ) . '"';
				echo ' />';
				echo '</a>';
			} else {
				echo ' &nbsp; ';
			}
			echo "</td>\n";
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php, inserido o par�metro $partial_report que quando recebe 1, for�a que seja escrito o nome do projeto ao inv�s da categoria PROSEGUR 08/06/2011
	function print_column_category( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE, $partial_report = null ) {
		global $t_sort, $t_dir;

		# grab the project name
		$t_project_name = project_get_field( $p_row['project_id'], 'name' );

		echo '<td class="center">';

		# type project name if viewing 'all projects' or if issue is in a subproject
		if ($partial_report == 1){ //apresenta de forma diferente a coluna na tela de relat�rio parcial PROSEGUR 12/07/2011
			echo '<small>';
			print_view_bug_sort_link( $t_project_name, 'project_id', $t_sort, $t_dir, $p_columns_target );
			echo '</small><br />';
		}
		else if  ( ON == config_get( 'show_bug_project_links' )
			&& helper_get_current_project() != $p_row['project_id']) {
			echo '<small>[';
			print_view_bug_sort_link( $t_project_name, 'project_id', $t_sort, $t_dir, $p_columns_target );
			echo ']</small><br />';
		}

		echo string_display( $p_row['category'] );
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_severity( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">';
		print_formatted_severity_string( $p_row['status'], $p_row['severity'] );
		echo '</td>';
	}
	
	# coluna do relat�rio parcial para ser digitado o n�mero de bugs cr�ticos PROSEGUR 12/07/2011
	function print_column_bugs( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="centerBugs">';
		echo '<input type="text" id="bugs'. $p_row['id'] .'" size="2" maxlength="3">';
		echo '</td>';
	}
	
	# coluna do relat�rio parcial para ser digitado a observa��o PROSEGUR 12/07/2011
	function print_column_notes_task( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {

		echo '<td class="center">';
		echo '<label id="NoteValue'. $p_row["id"] .'"></label> <image class="linkImg" src="images/update.png" alt="'. lang_get("update_notes_task") .'" onclick="showObj(\'changeNote'. $p_row["id"] .'\')" hspace="10" />';
		echo '<div class="divChangNote" id="changeNote'. $p_row["id"] .'"><img class="imgDivChange" src="images/fechar1.png" onclick="showObj(\'changeNote'. $p_row["id"] .'\')" >';
		echo lang_get('new_value');
		echo ':<br/>';
		echo '<textarea rows="4" cols="20" id="noteValueObs'. $p_row["id"] .'"></textarea> <input class="button-small" type="button" value="Ok" onclick="updateNote('. $p_row["id"] .', \'noteValueObs'. $p_row["id"] .'\' )">';
		echo '</textarea></div>';
		echo '</td>';

	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_eta( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td class="center">', get_enum_element( 'eta', $p_row['eta'] ), '</td>';
		} else {
			echo get_enum_element( 'eta', $p_row['eta'] );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_resolution( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">', get_enum_element( 'resolution', $p_row['resolution'] ), '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php, adicionado o $handler_view, para o relat�rio parcial, n�o ser� mostrado para queme st� atribu�do quando ele receber 1, PROSEGUR 08/06/2011
	function print_column_status( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE, $handler_view = null ) {
		echo '<td class="center">';
		printf( '<span class="issue-status" title="%s">%s</span>'
			, get_enum_element( 'resolution', $p_row['resolution'] )
			, get_enum_element( 'status', $p_row['status'] )
		);

		# print username instead of status
		if ( ( ON == config_get( 'show_assigned_names' ) && $handler_view != 1 )
		  && ( $p_row['handler_id'] > 0 ) 
		  && ( access_has_bug_level( config_get( 'view_handler_threshold' ), $p_row['id'] ) ) ) {
			printf( ' (%s)', prepare_user_name( $p_row['handler_id'] ) );
		}
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_handler_id( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">';
		if ( ( $p_row['handler_id'] > 0 ) && ( access_has_bug_level( config_get( 'view_handler_threshold' ), $p_row['id'] ) ) ) {
			echo prepare_user_name( $p_row['handler_id'] );
		}
		echo '</td>';
	}
	
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_reporter_id( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">';
		echo prepare_user_name( $p_row['reporter_id'] );
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_last_updated( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_filter;

		$t_last_updated = date( config_get( 'short_date_format' ), $p_row['last_updated'] );

		echo '<td class="center">';
		if ( $p_row['last_updated'] > strtotime( '-'.$t_filter['highlight_changed'].' hours' ) ) {
			printf( '<span class="bold">%s</span>', $t_last_updated );
		} else {
			echo $t_last_updated;
		}
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_date_submitted( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		$t_date_submitted = date( config_get( 'short_date_format' ), $p_row['date_submitted'] );

		echo '<td class="center">', $t_date_submitted, '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php, inserido o par�metro $view_private_icon para chamar personalizadamente sem exibir o �cone de caso privado no sum�rio do relat�rio parcial PROSEGUR 08/06/2011
	function print_column_summary( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE, $view_private_icon = null ) {
		global $t_icon_path;

		if ( $p_columns_target == COLUMNS_TARGET_CSV_PAGE ) {
			$t_summary = string_attribute( $p_row['summary'] );
		} else {
			$t_summary = string_display_line_links( $p_row['summary'] );
		}

		echo '<td class="left">', $t_summary;
		if ( VS_PRIVATE == $p_row['view_state'] && $view_private_icon != 1) {
			printf( ' <img src="%s" alt="(%s)" title="%s" />'
				, $t_icon_path . 'protected.gif'
				, lang_get( 'private' )
				, lang_get( 'private' )
			);
		}
		echo '</td>';
	}
	
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_target_version( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td>';
		if ( access_has_bug_level( config_get( 'roadmap_view_threshold' ), $p_row['id'] ) ) {
			echo $p_row['target_version'];
		}
		echo '</td>';
	}
?>
