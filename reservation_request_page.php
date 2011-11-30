<?php
#pagina responsável pelas reservas efetuadas pelos líderes

require_once( 'core.php' );
$t_core_path = config_get( 'core_path' );

require_once( $t_core_path.'reservation_api.php' );
require_once( $t_core_path.'user_api.php' );

html_page_top1( lang_get( 'summary_link' ) ); 
html_page_top2();

//inserindo os javascripts necessários para o calendário da reserva
?>
<br/>
	<script type ="text/javascript" src="javascript/common.js">
	</script>
	<link type="text/css" rel="stylesheet" href="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
	<script type="text/javascript" src="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
<?

/*
$p_reserv_data = array();
$p_reserv_data['date_start'] = '12/08/2011';
$p_reserv_data['date_end'] = '25/08/2011';
$p_reserv_data['project_id'] = '2';
$p_reserv_data['description'] = 'testeeee';
$p_reserv_data['user_owner_id'] = auth_get_current_user_id();
$p_reserv_data['user_reserv_id'] = '2';

reserv_create( $p_reserv_data );
*/
//mostra as reservas do usuário logado
print_reservations();

//mostra a lista de testadores do projeto selecionado ou de todos que o usuário tem acesso se estiver marcado "todos os projetos"
print_testers();



function print_testers() {

	//pega o id do projeto selecionado se for todos o id = 0
	$f_project_id = gpc_get_int( 'project_id', helper_get_current_project() );
	// se está marcado todos os projetos pega só os q o usuario atual tem acesso
	if ($f_project_id == 0) {
		$f_project_id = current_user_get_accessible_projects();
	}
	$rows = get_perfil_project($f_project_id, tester);
	//recebe as reservas do usuários logado
	//$rows = get_perfil_project();
	//print_r($rows);
	$row_count = db_num_rows( $rows );
	
	?>
<br/>
	<table class="width100">
	<tr >
	<td class="form-title" colspan="5"> <center>
	<? echo lang_get('reservation_add_list_title'); ?>
	</center>
	</td>
	</tr>
	<tr >
	<td width="15%" class="form-title">
	<? 
	#verifica se é possível editar, caso contrário apenas vizualizar
	echo lang_get('name'); 
	?>
	</td>
	<td width="80%" class="form-title">
	<?
	echo lang_get('reservation_availability'); 
	?>
	</td>
	<td width="5%" class="form-title">
	<?
	echo lang_get('actions'); 
	
	//inicia o looping de exibição das pessoas/projeto
	for ( $j=0; $j < $row_count; $j++ ) {
		$row = db_fetch_array( $rows );
		?>
		</td>
		</tr>
		<tr class="row-2">
		<td>
		<center>
		<?
		echo user_get_field(	$row['user_id'], 'realname');
		?>
		</center>
		</td>
		<td><script>displayCalendar(document.getElementById('data_inicio<? echo $row['user_id']  ?>'),'dd/mm/yyyy',this)</script>
		</td>
		<td><center>
		<?
		echo '<img src="images/BtnAdicionar.gif" alt="'. lang_get("reservation_add") .'" onclick="showObj(\'add_reserv'. $row["user_id"] .'\')" hspace="8">';
		//div de cadastro de requisições
		echo '<div class="divAddReserv" id="add_reserv'. $row['user_id'] .'"><img class="imgDivChange" src="images/fechar1.png" onclick="showObj(\'add_reserv'. $row['user_id'] .'\')" >';
		echo '<table width="100%"><tr><td colspan="2"><center>';
		echo lang_get('reservation_values');
		echo '</tr></td></center>';
		echo "<tr><td>" . lang_get('reservation_date') . "</td><td>" . lang_get('reservation_from') . ": ";
		?>
		<input type="text" name="data_inicio" id="data_inicio<? echo $row['user_id']  ?>" size="8" maxlength="10" value="" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById('data_inicio<? echo $row['user_id']  ?>'),'dd/mm/yyyy',this)">
		<?php echo lang_get( 'reservation_to' ) . ': ' ?>
		<input type="text" name="data_fim" id="data_fim<? echo $row['user_id']  ?>" size="8" maxlength="10" value="" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById('data_fim<? echo $row['user_id']  ?>'),'dd/mm/yyyy',this)">
		<?
		echo "</tr></td><tr><td>";
		echo lang_get('email_project');
		PRINT '</td><td><select name="project_id" class="small">';
		if (count($f_project_id) > 1){
			for ($i = 0; $i < count($f_project_id); $i++){
				//verifica se o testador tem acesso ao projeto, pode estar selecionado todos os projetos, mas o testador não estar incluido nele, então não deve mostrar
				if (project_get_local_user_access_level($f_project_id[$i], $row['user_id']) == tester) { //verifica se o usuário X no projeto Y possui acesso de testador
				PRINT "<option value=\"$f_project_id[$i]\"";
				PRINT '>' . string_display( project_get_field( $f_project_id[$i], 'name' ) ) . '</option>' . "\n";
				}
			}
		} else {
			PRINT "<option value=\"$f_project_id\"";
			PRINT '>' . string_display( project_get_field( $f_project_id, "name" ) ) . '</option>' . '\n';
		}
		PRINT '</select></tr></td><tr><td>';
		echo lang_get('description');
		echo '</td><td><textarea rows="4" cols="35" id="noteValueObs'. $row['user_id'] .'"></textarea>';
		//echo '<input class="button-small" type="button" value="Ok" onclick="updateCoverage('. $p_row["id"] .', \'inputValue'. $p_row["id"] .'\' )">';
		echo '</td></tr></table></div>';
		?>
		</center>
		</td>
		</tr>
		<?
	}  //fim do for
	?>
	</table>
	<?
	
}




function print_reservations() {

	//recebe as reservas do usuários logado
	$rows = reserv_get_all_user(auth_get_current_user_id());
	$row_count = db_num_rows( $rows );

?>
<table class="width100">
<tr >
<td class="form-title" colspan="5"> <center>
<? echo lang_get('own_reservation'); ?>
	</center>
</td>
</tr>
	<tr >
<td width="5%" class="form-title">
<? 
#verifica se é possível editar, caso contrário apenas vizualizar
	echo lang_get('actions'); 
?>
	</td>
<td width="15%" class="form-title">
<?
	echo lang_get('reservation_date') . ' (' . lang_get('reservation_from') . ' - ' . lang_get('reservation_to') . ')'; 
?>
</td>
<td width="10%" class="form-title">
<?
echo lang_get('status'); 
?>
</td>
<td width="10%" class="form-title">
<?
echo lang_get('email_project'); 
?>
</td>
<td width="50%" class="form-title">
<?
echo lang_get('description'); 

	//inicia o looping de exibição das reservas
	for ( $j=0; $j < $row_count; $j++ ) {
		$row = db_fetch_array( $rows );
?>
</td>
</tr>
<tr class="row-2">
<td>
<center>
		<img src="images/BtnConsultar.gif">
		<img src="images/BtnEditar.gif">
		<img src="images/BtnExcluir.gif">
</center>
</td>
<td>
		<? echo date('d/m/Y', strtotime($row['date_start'])) . ' - ' . date('d/m/Y', strtotime($row['date_end']));
		?>
</td>
<td>
		<? echo get_enum_element('reservation_status', $row['status']);
		?>
</td>
<td>
		<? echo project_get_field($row['project_id'], 'name');
		?>
</td>
<td>
		<? echo $row['description'];
		?>
</td>
</tr>
<?
	}  //fim do for
	?>
	</table>
	<?
	
}






html_page_bottom1( __FILE__ ); 

?>