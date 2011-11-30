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
	# $Id: bug_report_advanced_page.php,v 1.66.2.1 2007-10-13 22:32:52 giallu Exp $
	# --------------------------------------------------------

	# This file POSTs data to report_bug.php

	$g_allow_browser_cache = 1;
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'file_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
	require_once( $t_core_path.'last_visited_api.php' );
	require_once( $t_core_path.'projax_api.php' );
	$_POST['action'] = "";
	require_once('task_util.php');
	

	$f_master_bug_id = gpc_get_int( 'm_id', 0 );

	# this page is invalid for the 'All Project' selection except if this is a clone
	if ( ( ALL_PROJECTS == helper_get_current_project() ) && ( 0 == $f_master_bug_id ) ) {
		print_header_redirect( 'login_select_proj_page.php?ref=bug_report_advanced_page.php' );
	}

	if ( SIMPLE_ONLY == config_get( 'show_report' ) ) {
		print_header_redirect ( 'bug_report_page.php' .
						( 0 == $f_master_bug_id ) ? '' : '?m_id=' . $f_master_bug_id );
	}

	if( $f_master_bug_id > 0 ) {
		# master bug exists...
		bug_ensure_exists( $f_master_bug_id );

		# master bug is not read-only...
		if ( bug_is_readonly( $f_master_bug_id ) ) {
			error_parameters( $f_master_bug_id );
			trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
		}

		$t_bug = bug_prepare_edit( bug_get( $f_master_bug_id, true ) );

		# the user can at least update the master bug (needed to add the relationship)...
		//access_ensure_bug_level( config_get( 'update_bug_threshold', null, $t_bug->project_id ), $f_master_bug_id );

		#@@@ (thraxisp) Note that the master bug is cloned into the same project as the master, independent of
		#       what the current project is set to.
		if( $t_bug->project_id != helper_get_current_project() ) {
            # in case the current project is not the same project of the bug we are viewing...
            # ... override the current project. This to avoid problems with categories and handlers lists etc.
            $g_project_override = $t_bug->project_id;
            $t_changed_project = true;
        } else {
            $t_changed_project = false;
        }

	    //access_ensure_project_level( config_get( 'report_bug_threshold' ) );

		$f_handler_id			= $t_bug->handler_id;
		$f_priority				= $t_bug->priority;
		$f_summary				= $t_bug->summary;
		$f_description			= $t_bug->description;
		$f_steps_to_reproduce	= $t_bug->steps_to_reproduce;
		$f_additional_info		= $t_bug->additional_information;
		$f_view_state			= $t_bug->view_state;

		$t_project_id			= $t_bug->project_id;
	} else {
	   // access_ensure_project_level( config_get( 'report_bug_threshold' ) );

		$f_handler_id			= gpc_get_int( 'handler_id', 0 );
		$f_priority				= gpc_get_int( 'priority', config_get( 'default_bug_priority' ) );
		$f_summary				= gpc_get_string( 'summary', '' );
		$f_description			= gpc_get_string( 'description', '' );
		$f_steps_to_reproduce	= gpc_get_string( 'steps_to_reproduce', config_get( 'default_bug_steps_to_reproduce' ) );
		$f_additional_info		= gpc_get_string( 'additional_info', config_get ( 'default_bug_additional_info' ) );
		$f_view_state			= gpc_get_int( 'view_state', config_get( 'default_bug_view_status' ) );

		$t_project_id			= helper_get_current_project();

		$t_changed_project		= false;
	}

	$f_report_stay			= gpc_get_bool( 'report_stay', false );

	html_page_top1( lang_get( 'report_bug_link' ) );
	html_page_top2();

	print_recently_visited();
?>

<br /><center>
<? 
//exibe o submenu com as opÁıes de gerar uma tarefa integrada ou gerar normal, por padr„o abre a integrada PROSEGUR 15/09/2011
print_bracket_link( '', lang_get( 'integrated_method' ) ); 
print_bracket_link( 'bug_report_advanced_page.php', lang_get( 'normal_method' ) ); 
?>
</center>
<div align="center">
<form name="report_bug_form" method="post" action="tasks_report.php">
<table class="width75" cellspacing="1">


<!-- Title -->
<tr>
	<td class="form-title" colspan="3">
		<input type="hidden" name="m_id" value="<?php echo $f_master_bug_id ?>" />
		<input type="hidden" name="project_id" value="<?php echo $t_project_id ?>" />
		<?php 	echo lang_get( 'choice_integrated'); ?>
   </td>
</tr>

<!-- java script PROSEGUR 05/03/2011 -->
<script type ="text/javascript" src="javascript/common.js">
</script>
		<link type="text/css" rel="stylesheet" href="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
	<script type="text/javascript" src="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>

<!-- Tipo -->
<?php if ( access_has_project_level( config_get( 'report_bug_threshold' ) ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category" width="15%">
	<br>
	<input type="button" value="<? echo lang_get('filter_button'); ?>" onclick="javascript:dinamicStatusFilter(<? echo helper_get_current_project() ?> , document.forms['report_bug_form'].elements['type_task'])">
	</td>
	
	<td width="20%">
	<b>
	<? echo lang_get('by_status'); ?></b>
	<div id="demanda_status">
	<input id="deman_status" type="checkbox" value="10"> Nova <br/>
	<input id="deman_status" type="checkbox" value="50"> Atribu√≠da <br/>
	<input id="deman_status" type="checkbox" value="30"> Em constru√ß√£o <br/>
	<input id="deman_status" type="checkbox" value="60"> Constru√≠da <br/>
	<input id="deman_status" type="checkbox" value="70" checked > Em testes <br/><br/>
	</div>
	<div id="bug_status" style="display:none">
	<input id="bug_status" type="checkbox" value="10"> Novo <br/>
	<input id="bug_status" type="checkbox" value="20"> Reaberto <br/>
	<input id="bug_status" type="checkbox" value="30"> Admitido <br/>
	<input id="bug_status" type="checkbox" value="50"> Atribu√≠do <br/>
	<input id="bug_status" type="checkbox" value="80" checked > Resolvido <br/>
	<input id="bug_status" type="checkbox" value="85" checked > Entregue 
	</div>
	</td>
	<td>
	<div id ="handlerFilter">
	<?
	//filtro por relator
	show_users(10, helper_get_current_project(), 1);  //pega todos os usu·rios com permiss„o de abrir casos na inst‚ncia informada
	
	?>
	</div>
	</td>
	
</tr>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category" width="15%">
	<?php 
	echo '<input helper_get_tab_index() type="radio" onclick="javascript:selectTypeTask(10, '. helper_get_current_project() .', \'70\', 1)" name="type_task" id="type_task10" value="10" CHECKED /> Demandas<br/>';
	echo '<input helper_get_tab_index() type="radio" onclick="javascript:selectTypeTask(50, '. helper_get_current_project() .', \'80,85\', 1)" name="type_task" id="type_task50" value="50" /> Bugs<br/>';

		?>
	</td>
	<td width="80%" colspan="2">
		<div id="caso">
		<?
		//j· carrega a lista com as demandas do projeto selecionado
		show_cases(10, helper_get_current_project(), '70');
		?>
		</div>
	</td>

</tr>
<?php } ?>

<!-- Priority (if permissions allow) -->
<?php if ( access_has_project_level( config_get( 'report_bug_threshold' ) ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'priority' ) ?> <?php print_documentation_link( 'priority' ) ?>
	</td>
	<td colspan="2">
		<select <?php echo helper_get_tab_index() ?> name="priority">
			<?php print_enum_string_option_list( 'priority', $f_priority ) ?>
		</select>
	</td>
</tr>
<?php } ?>





<!-- Handler (if permissions allow) -->
<?php if ( access_has_project_level( config_get( 'report_bug_threshold' ) ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'assign_to' ) ?>
	</td>
	<td>
		<select <?php echo helper_get_tab_index() ?> name="handler_id">
			<option value="0" selected="selected"></option>
			<?php print_assign_to_option_list( $f_handler_id ) ?>
		</select>
	</td>
	<td>
	<input type="radio" name="type_task_perf" id="type_task_perf10" value="10" /> Desenvolvimento<br/>
	<input type="radio" name="type_task_perf" id="type_task_perf50" value="50" CHECKED /> Teste<br/>
	</td>
</tr>
<?php }

//carrega os campos de data ocultos, a funÁ„o est· no task_util.php, conta todos os possÌveis para criar os campos ocultos
show_date(helper_get_current_project(), '10,30,50,60,70');

?>




<!-- Additional Information -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'additional_information' ) ?> <?php print_documentation_link( 'additional_information' ) ?>
	</td>
	<td colspan="2">
		<textarea <?php echo helper_get_tab_index() ?> name="additional_info" cols="80" rows="10"><?php echo $f_additional_info ?></textarea>
	</td>
</tr>

<!-- Submit Button -->
<tr>
	<td class="left">
		<span class="required"> * <?php echo lang_get( 'required' ) ?></span>
	</td>
	<td class="center">
		<input <?php echo helper_get_tab_index() ?> type="submit" class="button" value="<?php echo lang_get( 'submit_report_button' ) ?>" onclick="javascript:return validaCamposIntegrated1()" />
	</td>
</tr>


</table>
</form>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
