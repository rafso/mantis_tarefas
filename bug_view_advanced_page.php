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
	# $Id: bug_view_advanced_page.php,v 1.87.2.1 2007-10-13 22:32:59 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
	require_once( $t_core_path.'file_api.php' );
	require_once( $t_core_path.'compress_api.php' );
	require_once( $t_core_path.'date_api.php' );
	require_once( $t_core_path.'relationship_api.php' );
	require_once( $t_core_path.'last_visited_api.php' );
	require_once( $t_core_path.'tag_api.php' );

	$f_bug_id		= gpc_get_int( 'bug_id' );
	$f_history		= gpc_get_bool( 'history', config_get( 'history_default_visible' ) );

	bug_ensure_exists( $f_bug_id );

	access_ensure_bug_level( VIEWER, $f_bug_id );

	$t_bug = bug_prepare_display( bug_get( $f_bug_id, true ) );

	if( $t_bug->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the bug we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_bug->project_id;
	}

	if ( SIMPLE_ONLY == config_get( 'show_view' ) ) {
		print_header_redirect ( 'bug_view_page.php?bug_id=' . $f_bug_id );
	}

	compress_enable();

	html_page_top1( bug_format_summary( $f_bug_id, SUMMARY_CAPTION ) );
	html_page_top2();

	print_recently_visited();

	$t_access_level_needed = config_get( 'view_history_threshold' );
	$t_can_view_history = access_has_bug_level( $t_access_level_needed, $f_bug_id );

	$t_bugslist = gpc_get_cookie( config_get( 'bug_list_cookie' ), false );
?>

<br />
<table class="width100" cellspacing="1">


<tr>

	<!-- Title -->
	<td class="form-title" colspan="<?php echo $t_bugslist ? '3' : '4' ?>">
		<?php echo lang_get( 'viewing_bug_advanced_details_title' ) ?>

		<!-- Jump to Bugnotes -->
		<span class="small"><?php print_bracket_link( "#bugnotes", lang_get( 'jump_to_bugnotes' ) ) ?></span>

		<!-- Send Bug Reminder -->
	<?php
		if ( !current_user_is_anonymous() && !bug_is_readonly( $f_bug_id ) &&
			  access_has_bug_level( config_get( 'bug_reminder_threshold' ), $f_bug_id ) ) {
	?>
		<span class="small">
			<?php print_bracket_link( 'bug_reminder_page.php?bug_id='.$f_bug_id, lang_get( 'bug_reminder' ) ) ?>
		</span>
	<?php
		}
		
		if ( wiki_is_enabled() ) {
	?>
		<span class="small">
			<?php print_bracket_link( 'wiki.php?id='.$f_bug_id, lang_get( 'wiki' ) ) ?>
		</span>
	<?php
		}
	?>
	</td>

	<!-- prev/next links -->
	<?php if( $t_bugslist ) { ?>
	<td class="center"><span class="small">
		<?php
			$t_bugslist = explode( ',', $t_bugslist );
			$t_index = array_search( $f_bug_id, $t_bugslist );
			if( false !== $t_index ) {
				if( isset( $t_bugslist[$t_index-1] ) ) print_bracket_link( 'view.php?id='.$t_bugslist[$t_index-1], '&lt;&lt;' );
				if( isset( $t_bugslist[$t_index+1] ) ) print_bracket_link( 'view.php?id='.$t_bugslist[$t_index+1], '&gt;&gt;' );
			}
		?>
	</span></td>
	<?php } ?>

	<!-- Links -->
	<td class="right" colspan="2">

		<!-- Simple View (if enabled) -->
	<?php if ( BOTH == config_get( 'show_view' ) ) { ?>
			<span class="small"><?php print_bracket_link( 'bug_view_page.php?bug_id=' . $f_bug_id, lang_get( 'view_simple_link' ) ) ?></span>
	<?php } ?>

	<?php if ( $t_can_view_history ) { ?>
		<!-- History -->
		<span class="small"><?php print_bracket_link( 'bug_view_advanced_page.php?bug_id=' . $f_bug_id . '&amp;history=1#history', lang_get( 'bug_history' ) ) ?></span>
	<?php } ?>

		<!-- Print Bug -->
		<span class="small"><?php print_bracket_link( 'print_bug_page.php?bug_id=' . $f_bug_id, lang_get( 'print' ) ) ?></span>

	</td>

</tr>


<!-- Labels -->
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
	<td class="numero_demanda">
		<?php echo bug_format_id( $f_bug_id ) ?>
	</td>

	<!-- Category -->
	<td>
		<?php
			$t_project_name = string_display( project_get_field( $t_bug->project_id, 'name' ) );
			echo "[$t_project_name] $t_bug->category";
		?>
	</td>

	<!-- Severity -->
	<td> 
		<?php print_date( config_get( 'short_date_format' ), $t_bug->date_start ) ?>
	</td>

	<!-- Reproducibility -->
	<td>
		<?php print_date( config_get( 'short_date_format' ), $t_bug->date_end ) ?>
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
		<?php print_user_with_subject( $t_bug->reporter_id, $f_bug_id ) ?>
	</td>

	<!-- View Status -->
	<td class="category">
		
	</td>
	<td>
		
	</td>

	<!-- spacer -->
	<td colspan="2">&nbsp;</td>

</tr>


<!-- Handler -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'assigned_to' ) ?>
	</td>
	<td>
		<?php 
			if ( access_has_bug_level( config_get( 'view_handler_threshold' ), $f_bug_id ) ) {
				print_user_with_subject( $t_bug->handler_id, $f_bug_id );
			}
		?>
	</td>
	<td><b>
	<?php echo lang_get( 'custom_field_type' ) ?>
	</b>
	</td>
		</td class="category">
	<td>
	<?php echo get_enum_element( 'type', $t_bug->type_task) ?>
	</td>
</tr>


<tr <?php echo helper_alternate_class() ?>>

	<!-- Priority -->
	<td class="category">
		<?php echo lang_get( 'priority' ) ?>
	</td>
	<td>
		<?php echo get_enum_element( 'priority', $t_bug->priority ) ?>
	</td>

	<!-- Resolution -->
	<td class="category">
		<?php echo lang_get( 'resolution' ) ?>
	</td>
	<td>
		<?php echo get_enum_element( 'resolution', $t_bug->resolution ) ?>
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
		<?php echo get_enum_element( 'status', $t_bug->status ) ?>
	</td>
		<!-- Andamento em % PROSEGUR 19/05/2011 -->
	<td>
	<b>	<?php echo lang_get( 'coverage' ) ?></b>
	</td>
	<td>
	<label id="coverageValue<?php echo $f_bug_id ?>"> <?php echo $t_bug->coverage ?></label>% <image class="linkImg" src='images/update.png' alt="<? echo lang_get('update_coverage') ?>" onclick="showObj('changeCoverage<?php echo $f_bug_id ?>')" hspace="10" />
	<div class="divChangCoverage" id="changeCoverage<?php echo $f_bug_id ?>"> <img class="imgDivChange" src="images/fechar1.png" onclick="showObj('changeCoverage<?php echo $f_bug_id ?>')" >
	<? echo lang_get('new_value'); ?>:<br/>
	<input type="text" size="6" id="inputValue" value="<?php echo $t_bug->coverage ?>"> <input class="button-small" type="button" value="Ok" onclick="updateCoverage(<? echo $f_bug_id; ?>, 'inputValue' )">
	</div>
	</td>
	<?php
		# Duplicate Id
		# MASC RELATIONSHIP
		if ( OFF == config_get( 'enable_relationship' ) ) {
			# Duplicate ID
			echo '<td>', lang_get( 'duplicate_id' ), '&nbsp;</td>';
			echo '<td>';
			print_duplicate_id( $t_bug->duplicate_id );
			echo '</td>';
		} else {
			# spacer
			echo '<td colspan="2">&nbsp;</td>';
		}
	?>

	<!-- Operating System -->
	<td>
		
	</td>
	<td >
	
	</td>

</tr>



<tr <?php echo helper_alternate_class() ?>>

	<!-- ETA -->
	<td class="category">
		<?php echo lang_get( 'hora_previsao' ); ?>
	</td>
	<td>
		<label id="hoursValue<?php echo $f_bug_id ?>"> <?php echo $t_bug->hours_prev; ?></label> <?php echo lang_get( 'horas'); ?><image class="linkImg" src='images/update.png' alt="<? echo lang_get('update_hours_prev') ?>" onclick="showObj('changeHours<?php echo $f_bug_id ?>')" hspace="10" />
	<div class="divChangCoverage" id="changeHours<?php echo $f_bug_id ?>"> <img class="imgDivChange" src="images/fechar1.png" onclick="showObj('changeHours<?php echo $f_bug_id ?>')" >
	<?php echo lang_get('new_value'); ?>:<br/>
	<input type="text" size="6" id="inputValueH" value="<?php echo $t_bug->hours_prev ?>"> <input class="button-small" type="button" value="Ok" onclick="updateHours(<? echo $f_bug_id; ?>, 'inputValueH' )">
	</div>
	</td>


	<td>
	</td>
	<td>
	</td>


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
		<?php echo bug_format_summary( $f_bug_id, SUMMARY_FIELD ) ?>
	</td>
</tr>


<!-- Description -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'description' ) ?>
	</td>
	<td colspan="5">
		<?php echo $t_bug->description ?>
	</td>
</tr>

<? /* removendo o campo passos para reproduzir porque n�o faz sentido PROSEGUR 19/05/2011
<!-- Steps to Reproduce -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'steps_to_reproduce' ) ?>
	</td>
	<td colspan="5">
		<?php echo $t_bug->steps_to_reproduce ?>
	</td>
</tr>

*/ ?>
<!-- Additional Information -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'additional_information' ) ?>
	</td>
	<td colspan="5">
		<?php echo $t_bug->additional_information ?>
	</td>
</tr>


<!-- Attachments -->
<?php
	$t_show_attachments = ( $t_bug->reporter_id == auth_get_current_user_id() ) || access_has_bug_level( config_get( 'view_attachments_threshold' ), $f_bug_id );

	if ( $t_show_attachments ) {
?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<a name="attachments" id="attachments" />
		<?php echo lang_get( 'attached_files' ) ?>
	</td>
	<td colspan="5">
		<?php file_list_attachments ( $f_bug_id ); ?>
	</td>
</tr>
<?php
	}
?>

<!-- Buttons -->
<tr align="center">
	<td align="center" colspan="6">
<?php
	html_buttons_view_bug_page( $f_bug_id );
?>
	</td>
</tr>
</table>

<?php
	$t_mantis_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

	# User list sponsoring the bug
	include( $t_mantis_dir . 'bug_sponsorship_list_view_inc.php' );

	# Bug Relationships
	# MASC RELATIONSHIP
	if ( ON == config_get( 'enable_relationship' ) ) {
		relationship_view_box ( $f_bug_id );
	}
	# MASC RELATIONSHIP

	# File upload box
	if ( !bug_is_readonly( $f_bug_id ) ) {
		include( $t_mantis_dir . 'bug_file_upload_inc.php' );
	}

	# User list monitoring the bug
	include( $t_mantis_dir . 'bug_monitor_list_view_inc.php' );

	# Bugnotes and "Add Note" box
	if ( 'ASC' == current_user_get_pref( 'bugnote_order' ) ) {
		include( $t_mantis_dir . 'bugnote_view_inc.php' );
		include( $t_mantis_dir . 'bugnote_add_inc.php' );
	} else {
		include( $t_mantis_dir . 'bugnote_add_inc.php' );
		include( $t_mantis_dir . 'bugnote_view_inc.php' );
	}

	# History
	if ( $f_history ) {
		include( $t_mantis_dir . 'history_inc.php' );
	}

	html_page_bottom1( __FILE__ );

	last_visited_issue( $f_bug_id );
?>
