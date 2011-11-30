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
	# $Id: summary_page.php,v 1.54.2.1 2007-10-13 22:34:43 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'summary_api.php' );
	require_once( $t_core_path.'filter_api.php' );	
	require_once( $t_core_path.'current_user_api.php' );
	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'string_api.php' );
	require_once( $t_core_path.'date_api.php' );
	require_once( $t_core_path.'icon_api.php' );
	require_once( $t_core_path.'columns_api.php' );

	//scripts
?>

	<link type="text/css" rel="stylesheet" href="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
	<script type="text/javascript" src="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>

<?php

	# Override the current page to make sure we get the appropriate project-specific configuration
	$g_project_override = $f_project_id;

	$t_user_id = auth_get_current_user_id();
	$t_project_ids = user_get_accessible_projects( $t_user_id );
?>
<?php html_page_top1( lang_get( 'summary_link' ) ); 
 html_page_top2(); ?>

<br />
<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
		<?php 
		echo collapse_icon( 'filter' );
		echo lang_get( 'partial_report_alter_label');?>
	</td>
</tr>
<tr valign="top">
	<td width="50%">
	<?php 
	$f_project_id = gpc_get_int( 'project_id', helper_get_current_project() );
	$t_per_page = null;
	$t_bug_count = null;
	$t_page_count = null;
	
	$t_columns = custom_function_default_get_columns_to_view( 'view_relationship_page_columns' );
	$col_count = sizeof( $t_columns );
	//verifica se a página está sendo recarregada após um filtro feito na tela, se não for carrega o padrão
	if (!isset($_POST['action'])){
		$date_start = date("d\/m\/Y", mktime(date('H'),date('i'),date('s'),date('m'), date('d')-7, date('Y'))); //data atual - X dias
		$date_end = date("d\/m\/Y", mktime(date('H'),date('i'),date('s'),date('m'), date('d')+7, date('Y'))); //data atual + X dias
		//configuração do filtro inicial, traz todos as tarefas de um usuário 
		$p_filter_arr = array();
		$p_filter_arr['_view_type'] = 'advanced'; //modo avançado do filtro
		$p_filter_arr['hide_status'] = 0;
		$p_filter_arr['per_page'] = 5000;
		$p_filter_arr['handler_id'] = auth_get_current_user_id();
		$p_filter_arr['sort'] = 'type_task,coverage';
		$p_filter_arr['dir'] = 'ASC,DESC';
		$p_filter_arr['sort_task'] = 'project_name,ASC';  //ordena pelo nome do projeto
		$p_filter_arr['date_start'] = $date_start;
		$p_filter_arr['date_end'] = $date_end;
		$p_filter_arr['report_submitted'] = 0;  //não exibe no loading as tarefas que já foram enviadas com 100%
		
		//ação filtrar na tela
	} else if (isset($_POST['statusShow'])) {
		$status = $_POST['statusShow'];
		$type = $_POST['typeShow'];
		$date_start = $_POST['date_start'];
		$date_end = $_POST['date_end'];
		$show_submitted = $_POST['showFinished'];
		//preenche os campos conforme selecionado na tela
		
		//verifica os status que foram marcados, para serem exibidos
		$t_status_arr = explode_enum_string( config_get( 'status_enum_string' ) );
		foreach( $t_status_arr as $t_status ) {
			$t_entry_array = explode_enum_arr( $t_status );
			//verifica se o status atual foi selecionado, se foi adiciona no array de exibição
			if (in_array($t_entry_array[0], $status)){
				$show_status[] = $t_entry_array[0];
			}
			
		}
		
		//verifica os status que foram marcados, para serem exibidos
		$t_type_arr = explode_enum_string( config_get( 'status_enum_string' ) );
		foreach( $t_type_arr as $t_type ) {
			$t_entry_array = explode_enum_arr( $t_type );
			//verifica se o status atual foi selecionado, se foi adiciona no array de exibição
			if (in_array($t_entry_array[0], $type)){
				$show_type[] = $t_entry_array[0];
			}
			
		}
		
		$p_filter_arr = array();
		$p_filter_arr['_view_type'] = 'advanced'; //modo avançado do filtro
		$p_filter_arr['hide_status'] = 0;
		$p_filter_arr['show_status'] = $show_status;
		$p_filter_arr['show_type'] = $show_type;		
		$p_filter_arr['per_page'] = 5000;
		$p_filter_arr['handler_id'] = auth_get_current_user_id();
		$p_filter_arr['sort'] = 'type_task,coverage';
		$p_filter_arr['dir'] = 'ASC,DESC';
		$p_filter_arr['sort_task'] = 'project_name,ASC';  //ordena pelo nome do projeto
		$p_filter_arr['date_start'] = $date_start;
		$p_filter_arr['date_end'] = $date_end;
		
		//verifica se é para ser exibido as tarefas já enviadas com 100% uma vez
		if ($show_submitted == 'on'){
			$p_filter_arr['report_submitted'] = 1;
			
		} else{  //não foi marcado para exibir, então não exibe as com 100% já enviadas
			$p_filter_arr['report_submitted'] = 0;  //não exibe no loading as tarefas que já foram enviadas com 100%
		  }
			
	}
	
	//executa o filtro e recebe as linhas com o resultado
	$rows = filter_get_bug_rows( $p_page_number, $p_per_page, $p_page_count, $p_bug_count, $p_filter_arr, null, null, null, $p_id_array);
	collapse_open( 'filter' );

	
	//form do filtro
	echo '<form id="form1" name="form1" action="#" enctype="multipart/form-data"
					method="post">';

	//tabela
	echo '<table class="width100" cellspacing="1">
	      <tr>
		  <td class="form-title">';

	//cabeçalho do filtro
	echo '<center>';
	echo lang_get( 'type_show');
	echo '</center>';
	echo '</td>';
	echo '<td class="form-title">';
	echo '<center>';
	echo lang_get( 'status_show');
	echo '</center>';
	echo '</td>';
	echo '<td class="form-title" colspan="2">';
	echo '<center>';
	echo lang_get( 'date_show') . '</tr>';
	echo '<tr><td class="form-title" rowspan="2">';
	echo '</center>';

	//filtro por tipo
	echo '<center>';
	echo '<select multiple="true" class="selType" name="typeShow[]">';
	$t_type_arr = explode_enum_string( config_get( 'type_enum_string' ) );
	foreach( $t_type_arr as $t_type ) {
		$t_entry_array = explode_enum_arr( $t_type );
		if (isset($_POST['typeShow'])){  //verifica se é post passando alguém selecionado, se não for seleciona todos
			$selected = null;
			if (in_array($t_entry_array[0], $show_type)){ //verifica se está recarregando a página após um filtro e o caso atual foi usado no filtro para selecionar no select
				$selected = 'selected';
			}
		} else {  //se não for post seleciona todos, estado inicial da tela
			$selected = 'selected';
		}
		echo '<option value="'. $t_entry_array[0] . '" '. $selected .'>';
		echo string_no_break( get_enum_to_string( lang_get( 'type_enum_string' ), $t_entry_array[0] ) );
		echo '</option>';
	}
	echo '</select>';
	echo '</center>';
	
	//filtro por status
	echo '</td>';
	echo '<td class="form-title" rowspan="2">';
	echo '<center>';
	echo ' <select multiple="true" class="selType" name="statusShow[]">';
	$t_status_arr = explode_enum_string( config_get( 'status_enum_string' ) );
	foreach( $t_status_arr as $t_status ) {
		$t_entry_array = explode_enum_arr( $t_status );
		if (isset($_POST['statusShow'])){  //verifica se é post passando alguém selecionado, se não for seleciona todos
			$selected = null;
			if (in_array($t_entry_array[0], $show_status)){ //verifica se está recarregando a página após um filtro e o caso atual foi usado no filtro para selecionar no select
				$selected = 'selected';
			}
		} else {  //se não for post seleciona todos, estado inicial da tela
			$selected = 'selected';
		}		
		echo '<option value="'. $t_entry_array[0] .'" '. $selected .'>';
		echo string_no_break( get_enum_to_string( lang_get( 'status_enum_string' ), $t_entry_array[0] ) );
		echo '</option>';
	}
	echo '</select>';
	echo '</center>';
	
	//filtro por data
	echo '</td>';
	echo '<td class="form-date-report">';
	echo lang_get( 'from_date' );
	echo ' : <input '. helper_get_tab_index() .' type="text" name="date_start" id="date_start" size="8" maxlength="10" value="'. $date_start .'" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'date_start\'),\'dd/mm/yyyy\',this)">';
	echo '<br />';
	echo lang_get( 'to_date' );
	echo ': <input '. helper_get_tab_index() .' type="text" name="date_end" id="date_end" size="8" maxlength="10" value="'. $date_end .'" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'date_end\'),\'dd/mm/yyyy\',this)">';
	echo '<td class="form-button-clear"><input type="button" value="'. lang_get("clear_date") .'" onclick="clear_date()"></td>';

	//mostrar tarefas com 100% e já enviadas no relatório anterior?
	echo '<tr><td class="form-title-date1" colspan="2">';
	//verifica se é para exibir checado ou não
	if ($show_submitted == 'on'){
		echo '<input type="checkbox" name="showFinished" checked /> ' . lang_get('show_finished_task_report') ;
	} else {
		echo '<input type="checkbox" name="showFinished" /> ' . lang_get('show_finished_task_report') ;
	}
	
	//fim do form e botão filtrar
	echo '</td></tr>';	
	echo '</td>';
	echo '<tr>
		  <td class="form-title" colspan="4">';
	echo ' <input type="submit" value="'. lang_get("filter_button") . '" name="action" />';
	echo '</form>';
	
	
	
	?>
			</td>
		</tr>
		</table>
		<br />
		<?php 
	collapse_closed( 'filter' ); //opções de fechamento do filtro
	collapse_end( 'filter' ); //opções de fechamento do filtro
	?>
		
		<table id="buglist" class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="<?php echo $col_count ; ?>">
<?php
# -- Viewing range info --

$v_start = 0;
$v_end   = 0;

if ( sizeof( $rows ) > 0 ) {
	if( $t_filter )
		$v_start = $t_filter['per_page'] * (int)($f_page_number-1) +1;
	else 
		$v_start = 1;
	$v_end   = $v_start + sizeof( $rows ) -1;
}

echo $v_end ." " .lang_get( 'task_found' );
		?>

<span class="small"> <?php
# -- Print and Export links --

	//print_bracket_link( 'print_all_bug_page.php', lang_get( 'print_all_bug_page_link' ) );
	echo '&nbsp;';
	if ( ON == config_get( 'use_jpgraph' ) ) {
		//print_bracket_link( 'bug_graph_page.php', lang_get( 'graph_bug_page_link' ) );
		echo '&nbsp;';
	}
	//print_bracket_link( 'csv_export.php', lang_get( 'csv_export' ) );
	?> </span>
	</td>

</tr>
<?php # -- Bug list column header row -- ?>
<tr class="row-category">
<?php
foreach( $t_columns as $t_column ) {
	$t_title_function = 'print_column_title';
	helper_call_custom_function( $t_title_function, array( $t_column ) );
}
?>
</tr>


<?php

	function write_bug_rows ( $p_rows )
	{				
		global $t_columns, $t_filter;

		$t_in_stickies = ( $t_filter && ( 'on' == $t_filter['sticky_issues'] ) );

		mark_time( 'begin loop' );

		# -- Loop over bug rows --

		$t_rows = sizeof( $p_rows ); 
		for( $i=0; $i < $t_rows; $i++ ) {
			$t_row = $p_rows[$i];

			if ( ( 0 == $t_row['sticky'] ) && ( 0 == $i ) ) {
				$t_in_stickies = false;
			}
			if ( ( 0 == $t_row['sticky'] ) && $t_in_stickies ) {	# demarcate stickies, if any have been shown
?>
               <tr>
                       <td class="left" colspan="<?php echo sizeof( $t_columns ); ?>" bgcolor="#999999">&nbsp;</td>
               </tr>
<?php
				$t_in_stickies = false;
			}

			$t_column_value_function = 'print_column_value';
			# choose color based on status
			$status_color = get_status_color( $t_row['status'] );

			echo '<tr bgcolor="'. $status_color .'" border="1">';
			foreach( $t_columns as $t_column ) {
				
			switch($t_column){
				case "selection":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;
				case "type_task":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;
				case "id":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;
				case "summary":
					print_column_summary( $t_row, null, 1 );
					break;
				case "status":
					print_column_status( $t_row, null, 1);
					break;
				case "bugs":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;
				case "date_s":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;
				case "date_t":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;
				case "coverage":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;
				case "category":
					print_column_category( $t_row ,null,  1);  //chama a função diretamente passando um parâmetro forçando que seja escrito o nome do projeto PROSEGUR 08/06/2011
					break;
				case "notes_task":
					helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));
					break;				
				
			}
				

			}
			echo '</tr>';
		}

	}

 ?>

<tr class="spacer">
	<td colspan="<?php echo $col_count; ?>"></td>
</tr>
<?
	//exibe o loading PROSEGUR 01/06/2011
	echo '<script type="text/javascript" language="JavaScript">
			loadStartStop(1);
			</script>';
echo '<form id="submit_email" name="submit_email" action="#" enctype="multipart/form-data"
		method="post">';

write_bug_rows($rows);

echo '<tr>
		<td class="center">';		
echo "<input type=\"checkbox\" name=\"all_bugs\" value=\"all\" onclick=\"checkall('submit_email', this.form.all_bugs.checked)\" />";
echo '</td>';
echo '<td colspan="9"><span class="small">'. lang_get( 'select_all' ). '</span>';
echo '</td></tr>';

echo '<tr>
		<td class="form-title" colspan="10">';
echo '<input type="button" value="'. lang_get("submit_button") . '" name="action" onclick="return send_parcial_reporter(this.form)" />';
echo '</td></tr></form>';

	//oculta o loading PROSEGUR 01/06/2011
	echo '<script type="text/javascript" language="JavaScript">
			loadStartStop(0);
			</script>';


?>
</table>
		
		
<?php 
html_status_legend();
html_page_bottom1( __FILE__ ); ?>
