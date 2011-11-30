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
	# $Id: bug_update_advanced_page.php,v 1.103.2.1 2007-10-13 22:32:57 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'ajax_api.php' );
	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
	require_once( $t_core_path.'date_api.php' );
	require_once( $t_core_path.'last_visited_api.php' );
	require_once( $t_core_path.'projax_api.php' );
?>
<?php
	$f_bug_id = gpc_get_int( 'bug_id' );

	$t_bug = bug_prepare_edit( bug_get( $f_bug_id, true ) );

	if( $t_bug->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the bug we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_bug->project_id;
		$t_changed_project = true;
	} else {
		$t_changed_project = false;
	}

	if ( SIMPLE_ONLY == config_get( 'show_update' ) ) {
		print_header_redirect ( 'bug_update_page.php?bug_id=' . $f_bug_id );
	}

	if ( bug_is_readonly( $f_bug_id ) ) {
		error_parameters( $f_bug_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	access_ensure_bug_level( config_get( 'update_bug_threshold' ), $f_bug_id );

	html_page_top1( bug_format_summary( $f_bug_id, SUMMARY_CAPTION ) );
	html_page_top2();

	print_recently_visited();
?>

<br />
<form method="post" action="bug_update.php">
<?php echo form_security_field( 'bug_update' ) ?>
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="3">
		<input type="hidden" name="bug_id" value="<?php echo $f_bug_id ?>" />
		<input type="hidden" name="update_mode"			value="1" />
		<?php echo lang_get( 'updating_bug_advanced_title' ) ?>
	</td>
	<td class="right" colspan="3">
<?php
	print_bracket_link( string_get_bug_view_url( $f_bug_id ), lang_get( 'back_to_bug_link' ) );

	if ( BOTH == config_get( 'show_update' ) ) {
		print_bracket_link( 'bug_update_page.php?bug_id=' . $f_bug_id, lang_get( 'update_simple_link' ) );
	}
?>
	</td>
</tr>


<tr class="row-category">
	<td width="15%">
		<?php echo lang_get( 'id' ) ?>
	</td>
	<td width="20%">
		<?php echo lang_get( 'email_project' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'start_date' ) ?>
	</td>
	<td width="20%">
		<?php echo lang_get( 'end_date' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'date_submitted' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'last_update' ) ?>
	</td>
</tr>


<tr <?php echo helper_alternate_class() ?>>

	<!-- Bug ID -->
	<td  class="numero_demanda">
		<?php echo bug_format_id( $f_bug_id ) ?>
	</td>

	<!-- Category -->
	<td>
<?php
$t_project_name = string_display( project_get_field( $t_bug->project_id, 'name' ) );
echo "[$t_project_name] $t_bug->category";
		
		//adicionando os java scripts PROSEGUR 13-03-2011		
		?>
			<!-- java script PROSEGUR 05/03/2011 -->
<script type ="text/javascript" src="javascript/common.js">
</script>
<link type="text/css" rel="stylesheet" href="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></link>
<script type="text/javascript" src="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>
	</td>

	<td>
	<center><input <?php echo helper_get_tab_index() ?> type="text" size="8" maxlength="10" name="date_start" id="date_start" value="<?php print_date( config_get( 'short_date_format' ), $t_bug->date_start ) ?>" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById('date_start'),'dd-mm-yyyy',this)"> </center>
	</td>

	<td>
		<center><input <?php echo helper_get_tab_index() ?> type="text" size="8" maxlength="10" name="date_end" id="date_end" value="<?php print_date( config_get( 'short_date_format' ), $t_bug->date_end ) ?>" />  <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById('date_end'),'dd-mm-yyyy',this)">	</center>
	</td>

	<!-- Date Submitted -->
	<td>
		<?php print_date( config_get( 'normal_date_format' ), $t_bug->date_submitted ) ?>
	</td>

	<!-- Date Updated -->
	<td>
		<?php print_date( config_get( 'normal_date_format' ), $t_bug->last_updated ) ?>
	</td>

</tr>


<!-- spacer -->
<tr class="spacer">
	<td colspan="6"></td>
</tr>



<tr <?php echo helper_alternate_class() ?>>

	<!-- Reporter -->
	<td class="category">
		<?php echo lang_get( 'reporter' ) ?>
	</td>
	<td>
		<?php
			if ( ON == config_get( 'use_javascript' ) ) {
				$t_username = prepare_user_name( $t_bug->reporter_id );
				echo ajax_click_to_edit( $t_username, 'reporter_id', 'entrypoint=issue_reporter_combobox&issue_id=' . $f_bug_id );
			} else {
				echo '<select <?php echo helper_get_tab_index() ?> name="reporter_id">';
				print_reporter_option_list( $t_bug->reporter_id, $t_bug->project_id );
				echo '</select>';
			}
		?>
	</td>

	<!-- View Status -->
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
<?php
	if ( access_has_project_level( config_get( 'change_view_status_threshold' ) ) ) { ?>
			<select <?php echo helper_get_tab_index() ?> name="view_state">
				<?php print_enum_string_option_list( 'view_state', $t_bug->view_state) ?>
			</select>
		<?php
	} else {
		echo get_enum_element( 'view_state', $t_bug->view_state );
	}

?>
	</td>

	<!-- spacer -->
	<td colspan="2">&nbsp;</td>
</tr>


<tr <?php echo helper_alternate_class() ?>>

	<!-- Assigned To -->
	<td class="category">
		<?php echo lang_get( 'assigned_to' ) ?>
	</td>
	<td>
	<?php if ( access_has_project_level( config_get( 'update_bug_assign_threshold', config_get( 'update_bug_threshold' ) ) ) ) {
	?>
		<select <?php echo helper_get_tab_index() ?> name="handler_id">
			<option value="0"></option>
			<?php print_assign_to_option_list( $t_bug->handler_id, $t_bug->project_id ) ?>
		</select>
	<?php
		} else {
			echo user_get_name( $t_bug->handler_id );
		}
	?>
	</td>
	<td><b>
	<?php echo lang_get( 'custom_field_type' ) ?>
	</b>
	</td>
		</td class="category">
	<td>
		<select <?php echo helper_get_tab_index() ?> name="type_task">
			<?php print_enum_string_option_list( 'type' , $t_bug->type_task ) ?>
		</select>
	</td>

</tr>


<tr <?php echo helper_alternate_class() ?>>

	<!-- Priority -->
	<td class="category">
		<?php echo lang_get( 'priority' ) ?>
	</td>
	<td align="left">
		<select <?php echo helper_get_tab_index() ?> name="priority">
			<?php print_enum_string_option_list( 'priority', $t_bug->priority ) ?>
		</select>
	</td>

	<!-- Resolution -->
	<td class="category">
		<?php echo lang_get( 'resolution' ) ?>
	</td>
	<td>
		<select <?php echo helper_get_tab_index() ?> name="resolution">
			<?php print_enum_string_option_list( "resolution", $t_bug->resolution ) ?>
		</select>
	</td>

	<!-- Platform -->
	<td class="category">

	</td>
	<td>

	</td>

</tr>


<tr <?php echo helper_alternate_class() ?>>

	<!-- Status -->
	<td class="category">
		<?php echo lang_get( 'status' ) ?>
	</td>
	<td bgcolor="<?php echo get_status_color( $t_bug->status ) ?>">
	
	<?php
	//configurado para n�o exibir o select de altera��o de status, se o status atual for o status configurado para ser o somente leitura PROSEGUR 06/03/2011
if ( config_get( 'bug_readonly_status_threshold' ) == $t_bug->status) {
	echo get_enum_element( "status", $t_bug->status );
		} else {
			 print_status_option_list( 'status', $t_bug->status,
			( $t_bug->reporter_id == auth_get_current_user_id() &&
				( ON == config_get( 'allow_reporter_close' ) ) ), $t_bug->project_id );
		} //fim do else do config_get do status
		?>
								
	</td>
	
	<!-- Andamento em % PROSEGUR 19/05/2011 -->
	<td>
	<b>	<?php echo lang_get( 'coverage' ) ?></b>
	</td>
	<td>
		<input <?php echo helper_get_tab_index() ?> onKeyPress="javascript:return soNumero()"; type="text" name="coverage" id="coverage" size="1" maxlength="3" value="<?php echo $t_bug->coverage ?>" />%
	</td>

	<?php
		# Duplicate Id

		# MASC RELATIONSHIP
		if ( OFF == config_get( 'enable_relationship' ) ) {
			# Duplicate ID
			echo '<td class="category">', lang_get( 'duplicate_id' ), '&nbsp;</td>';
			echo '<td>';
			echo '<input ', helper_get_tab_index(), ' type="text" name="duplicate_id" value="', $t_bug->duplicate_id, '" maxlength="7" />&nbsp;';
			echo '</td>';
		} else {
			# spacer
			echo '<td colspan="2">&nbsp;</td>';
		}
	?>

	<!-- Operating System -->
	<td>

	</td>
	<td>

	</td>

</tr>


<tr <?php echo helper_alternate_class() ?>>

<!-- java script PROSEGUR 05/03/2011 -->
<script type ="text/javascript" src="javascript/common.js">
</script>

	<!-- ETA -->
	<td class="category">
	<!-- Fixed in Version -->
		<?php echo lang_get( 'hora_previsao' ); ?>
	</td>
	<td>
		<input <?php echo helper_get_tab_index() ?> onKeyPress="javascript:return soNumero()"; type="text" name="hours_prev" id="hours_prev" size="1" maxlength="3" value="<?php echo $t_bug->hours_prev ?>" /> <?php echo lang_get( 'horas'); ?> 
	</td>

	<td class="category">
	
	</td>
	<td>
		
	</td>

	<!-- Product Version  or Product Build, if version is suppressed -->
	<td class="category">

	</td>
	<td>
		
	</td>

</tr>


<!-- Summary -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'summary' ) ?>
	</td>
	<td colspan="5">
		<input <?php echo helper_get_tab_index() ?> type="text" name="summary" size="105" maxlength="128" value="<?php echo $t_bug->summary ?>" />
	</td>
</tr>


<!-- Description -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'description' ) ?>
	</td>
	<td colspan="5">
		<textarea <?php echo helper_get_tab_index() ?> cols="80" rows="10" name="description"><?php echo $t_bug->description ?></textarea>
	</td>
</tr>

<? /* removendo o campo de passos para reproduzir do bug_update PROSEGUR 18/05/2011
<!-- Steps to Reproduce -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'steps_to_reproduce' ) ?>
	</td>
	<td colspan="5">
		<textarea <?php echo helper_get_tab_index() ?> cols="80" rows="10" name="steps_to_reproduce"><?php echo $t_bug->steps_to_reproduce ?></textarea>
	</td>
</tr>
*/ ?>

<!-- Additional Information -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'additional_information' ) ?>
	</td>
	<td colspan="5">
		<textarea <?php echo helper_get_tab_index() ?> cols="80" rows="10" name="additional_information"><?php echo $t_bug->additional_information ?></textarea>
	</td>
</tr>


<tr class="spacer">
	<td colspan="6"></td>
</tr>


<!-- Custom Fields -->
<?php
	$t_custom_fields_found = false;
	$t_related_custom_field_ids = custom_field_get_linked_ids( $t_bug->project_id );
	foreach( $t_related_custom_field_ids as $t_id ) {
		$t_def = custom_field_get_definition( $t_id );
		if( ( $t_def['display_update'] || $t_def['require_update']) && custom_field_has_write_access( $t_id, $f_bug_id ) ) {
			$t_custom_fields_found = true;
?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php if($t_def['require_update']) {?><span class="required">*</span><?php } ?><?php echo string_display( lang_get_defaulted( $t_def['name'] ) ) ?>
	</td>
	<td colspan="5">
		<?php
			print_custom_field_input( $t_def, $f_bug_id );
		?>
	</td>
</tr>
<?php
		}
	} # foreach( $t_related_custom_field_ids as $t_id )
?>


<?php if ( $t_custom_fields_found ) { ?>
<!-- spacer -->
<tr class="spacer">
	<td colspan="6"></td>
</tr>
<?php } # custom fields found ?>


<!-- Bugnote Text Box -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'add_bugnote_title' ) ?>
	</td>
	<td colspan="5">
		<textarea <?php echo helper_get_tab_index() ?> name="bugnote_text" cols="80" rows="10"></textarea>
	</td>
</tr>


<!-- Bugnote Private Checkbox (if permitted) -->
<?php if ( access_has_bug_level( config_get( 'private_bugnote_threshold' ), $f_bug_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'private' ) ?>
	</td>
	<td colspan="5">
<?php
		$t_default_bugnote_view_status = config_get( 'default_bugnote_view_status' );
		if ( access_has_bug_level( config_get( 'set_view_status_threshold' ), $f_bug_id ) ) {
?>
			<input <?php echo helper_get_tab_index() ?> type="checkbox" name="private" <?php check_checked( config_get( 'default_bugnote_view_status' ), VS_PRIVATE ); ?> />
<?php
			echo lang_get( 'private' );
		} else {
			echo get_enum_element( 'view_state', $t_default_bugnote_view_status );
		}
?>
	</td>
</tr>
<?php } ?>

<!-- Time Tracking (if permitted) -->
<?php if ( config_get('time_tracking_enabled') ) { ?>
<?php if ( access_has_bug_level( config_get( 'time_tracking_edit_threshold' ), $f_bug_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'time_tracking' ) ?>
	</td>
	<td colspan="5">
		<input type="text" name="time_tracking" size="5" value="0:00" />
	</td>
</tr>
<?php } ?>
<?php } ?>

<!-- Submit Button -->
<tr>
	<td class="center" colspan="6">
		<input <?php echo helper_get_tab_index() ?> type="submit" class="button" value="<?php echo lang_get( 'update_information_button' ) ?>" />
	</td>
</tr>


</table>
</form>

<?php
	include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'bugnote_view_inc.php' );
	html_page_bottom1( __FILE__ );

	last_visited_issue( $f_bug_id );
?>
