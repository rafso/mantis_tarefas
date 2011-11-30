<?php
# Mantis - a php based bugtracking system

	# --------------------------------------------------------
	# $Revision: 1 $
	# $Author: Raphael Soares $
	# $Date: 14-07-2011 $
	#
	# $Id: task_pool_page.php,v 1$
	# $Description: Página para visualizar a fila separada por recurso
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'compress_api.php' );
	require_once( $t_core_path.'filter_api.php' );
	require_once( $t_core_path.'last_visited_api.php' );
	require_once( $t_core_path.'current_user_api.php' );
	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'string_api.php' );
	require_once( $t_core_path.'date_api.php' );
	require_once( $t_core_path.'icon_api.php' );
	require_once( $t_core_path.'columns_api.php' );
	//arquivo utilitario PROSEGUR
	if (!isset($_POST['action'])){
	$_POST['action'] = 1; //apenas iniciar o action para evitar que ele seja usado incorretamente na página task_util
	}
	require_once( 'task_util.php' );

	//scripts
?>

	<link type="text/css" rel="stylesheet" href="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css?random=20051112" media="screen"></link>
	<script type="text/javascript" src="plugins/calendar/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js?random=20060118"></script>

<?php

 auth_ensure_user_authenticated() ?>
<?php
	$f_page_number		= gpc_get_int( 'page_number', 1 );

	$t_per_page = null;
	$t_bug_count = null;
	$t_page_count = null;
	
	$t_icon_path = config_get( 'icon_path' );
	$t_update_bug_threshold = config_get( 'update_bug_threshold' );

	$t_columns = helper_get_columns_to_view( COLUMNS_TARGET_VIEW_PAGE );

	$col_count = sizeof( $t_columns );
		gpc_set_cookie( config_get( 'bug_list_cookie' ), implode( ',', $t_bugslist ) );

	compress_enable();

	html_page_top1( lang_get( 'view_bugs_link' ) );

	html_page_top2();

	print_recently_visited();
	//aqui termina a parte padrao que foi usada do mantis para o cabeçalho, casos recentes e filtro PROSEGUR 14/07/2011
	?>
	<br />
<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
		<?php 
		collapse_icon( 'filter' );
		echo lang_get( 'partial_report_alter_label');?>
	</td>
</tr>
<tr valign="top">
	<td width="50%">
	<?
		//verifica se a página está sendo recarregada após um filtro feito na tela, se não for carrega o padrão
	if (!isset($_POST['action']) || ($_POST['action'] == 1) ){
		$date_start = date("d\/m\/Y", mktime(date('H'),date('i'),date('s'),date('m'), date('d')-30, date('Y'))); //data atual - X dias
		$date_end = date("d\/m\/Y", mktime(date('H'),date('i'),date('s'),date('m'), date('d')+30, date('Y'))); //data atual + X dias
		//configuração do filtro inicial, traz todos as tarefas de um usuário 
		$p_filter_arr['_view_type'] = 'advanced'; //modo avançado do filtro
		$p_filter_arr['show_status'] = explode(",", '10,30,50,70,90'); //o filtro inicial oculta apenas o status 100, entregue finalizada
		$p_filter_arr['date_start'] = $date_start;
		$p_filter_arr['date_end'] = $date_end;
		$p_filter_arr['report_submitted'] = 1;  //exibe as tarefas que já foram enviadas com 100%				
		
		
		//ação filtrar na tela
	} else if (isset($_POST['statusShow'])) {
		$status = $_POST['statusShow'];
		$type = $_POST['typeShow'];
		$users_sel = $_POST['handlerShow'];
		$date_start = $_POST['date_start'];
		$date_end = $_POST['date_end'];
		//preenche os campos conforme selecionado na tela
		
		//verifica os responsáveis que foram marcados, para serem exibidos
		$t_users_arr = 	user_get_all_level();  //pega todos os usuários
		foreach( $t_users_arr as $t_user ) {
			//verifica se o status atual foi selecionado, se foi adiciona no array de exibição
			if (in_array($t_user['id'], $users_sel)){
				$show_handler[] = $t_user['id'];
			}
			
		}
		
		//verifica os status que foram marcados, para serem exibidos
		$t_status_arr = explode_enum_string( config_get( 'status_enum_string' ) );
		foreach( $t_status_arr as $t_status ) {
			$t_entry_array = explode_enum_arr( $t_status );
			//verifica se o status atual foi selecionado, se foi adiciona no array de exibição
			if (in_array($t_entry_array[0], $status)){
				$show_status[] = $t_entry_array[0];
			}
			
		}
		
		//verifica os tipos que foram marcados, para serem exibidos
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
		$p_filter_arr['show_status'] = $show_status;
		$p_filter_arr['show_type'] = $show_type;		
		$p_filter_arr['per_page'] = 5000;
		$p_filter_arr['date_start'] = $date_start;
		$p_filter_arr['date_end'] = $date_end;
		$p_filter_arr['report_submitted'] = 1;  //exibe as tarefas que já foram enviadas com 100%
		$p_filter_arr['handler_id'] = $show_handler;
	}

	$rows = filter_get_bug_rows( $f_page_number, $t_per_page, $t_page_count, $t_bug_count, $p_filter_arr, null, null, true, $p_id_array ); //adicionado o parametro $p_id_array, para retornar o total de bugs, retorna array de ids.. PROSEGUR 02/06/2011

	$t_bugslist = Array();
	$t_row_count = sizeof( $rows );
	for($i=0; $i < $t_row_count; $i++) {
		array_push($t_bugslist, $rows[$i]["id"] );
	}

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
	echo lang_get( 'handler_show');
	echo '</center>';
	echo '</td>';
	echo '<td class="form-title">';
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
	echo '</center>';
	//filtro por responsável
	$t_users = user_project_get_with_level(tester, helper_get_current_project());  //pega todos os usuários com perfil >= 
	
	//ordena o array pelo nome para ser exibido em ordem alfabética
	foreach ($t_users as $key => $row) {
		$cont[$key] = $row['realname'];	
	}
	array_multisort($cont, SORT_ASC, $t_users);

	echo '<tr><td class="form-title">';
	echo '<center>';
	echo '<select multiple="true" class="selType" name="handlerShow[]">';
	for ($i = 0; $i < count($t_users); $i++ ) {
		if (isset($_POST['handlerShow'])){  //verifica se é post passando alguém selecionado, se não for seleciona todos
			$selected = null;
			if (in_array($t_users[$i]['id'], $show_handler)){ //verifica o que foi usado no filtro para selecionar no select
				$selected = 'selected';
			}
		} else {  //se não for post seleciona todos, estado inicial da tela
			$selected = 'selected';
		}
		//exibe apenas se for um usuário habilitado
		if (user_get_field($t_users[$i]['id'], 'enabled') > 0){
		echo '<option value="'. $t_users[$i]['id'] . '" '. $selected .'>';
		echo user_get_field($t_users[$i]['id'], 'realname');
		echo '</option>';
		}
	}
	echo '</select>';
	echo '</center>';
	echo '</td>';


	//filtro por tipo
	echo '<td class="form-title">';
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
	echo '</td>';
	
	//filtro por status
	echo '<td class="form-title">';
	echo '<center>';
	echo ' <select multiple="true" class="selType" name="statusShow[]">';
	$t_status_arr = explode_enum_string( config_get( 'status_enum_string' ) );
	foreach( $t_status_arr as $t_status ) {
		$t_entry_array = explode_enum_arr( $t_status );
		if (isset($_POST['statusShow'])){  //verifica se é post passando alguém selecionado, se não for seleciona todos, menos o entregue/finalizado
			$selected = null;
			if (in_array($t_entry_array[0], $show_status)){ //verifica se está recarregando a página após um filtro e o caso atual foi usado no filtro para selecionar no select
				$selected = 'selected';
			}
		} else if ($t_entry_array[0] <> 100) {  //se não for post seleciona todos, menos entregue/finalizado, estado inicial da tela
			$selected = 'selected';
		} else {  //deixa o entregue/finalizado de fora
				$selected = '';
			}
		echo '<option value="'. $t_entry_array[0] .'" '. $selected .'>';
		echo string_no_break( get_enum_to_string( lang_get( 'status_enum_string' ), $t_entry_array[0] ) );
		echo '</option>';
	}
	echo '</select>';
	echo '</center>';
	echo '</td>';
	
	//filtro por data
	echo '<td class="form-date-report">';
	echo lang_get( 'from_date' );
	echo ' : <input '. helper_get_tab_index() .' type="text" name="date_start" id="date_start" size="8" maxlength="10" value="'. $date_start .'" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'date_start\'),\'dd/mm/yyyy\',this)">';
	echo '<br />';
	echo lang_get( 'to_date' );
	echo ': <input '. helper_get_tab_index() .' type="text" name="date_end" id="date_end" size="8" maxlength="10" value="'. $date_end .'" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'date_end\'),\'dd/mm/yyyy\',this)">';
	echo '<td class="form-button-clear"><input type="button" value="'. lang_get("clear_date") .'" onclick="clear_date()"></td>';

	//fim do form e botão filtrar
	echo '</td>';
	echo '<tr>
		  <td class="form-title" colspan="5">';
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




	//ordena o array pela atribuição
	foreach ($rows as $key => $row) {
		$cont[$key] = $row['handler_id'];	
	}
	array_multisort($cont, SORT_DESC, $rows);


	//definições sobre os usuários, pega todos para exibir no filtro e para listar apenas os que possuem tarefas nas tabelas abaixo do filtro
	$t_users = user_get_all_level();  //pega todos os usuários
	for ($i = 0; $i < count($t_users); $i++){
		$t_user[] = search($rows, 'handler_id', $t_users[$i]['id']);
		if (count($t_user[$i]) > 0) {  //adiciona apenas se houver tarefas atribuídas ao usuário
			$users[] = $t_user[$i];
		} 
	}

	//for que separa 1 tabela para cada usuário, users foi definido a cima
	for ($i = 0; $i < count($users); $i++){
		print_bugs_table($users[$i]);
	}

	//pega as tarefas sem atribuição
	$p_filter_arr['handler_id'] = -2;
	$no_user = filter_get_bug_rows( $f_page_number, $t_per_page, $t_page_count, $t_bug_count, $p_filter_arr, null, null, true );
	//imprime as sem atribuição
	print_bugs_table($no_user, 1);





//	$t_filter_position = config_get( 'filter_position' );
//
//	# -- ====================== FILTER FORM ========================= --
//	if ( ( $t_filter_position & FILTER_POSITION_TOP ) == FILTER_POSITION_TOP ) {
//		filter_draw_selection_area( $f_page_number );
//	}
//	# -- ====================== end of FILTER FORM ================== --


	# -- ====================== BUG LIST ============================ --

	$t_status_legend_position = config_get( 'status_legend_position' );

	if ( $t_status_legend_position == STATUS_LEGEND_POSITION_TOP || $t_status_legend_position == STATUS_LEGEND_POSITION_BOTH ) {
		html_status_legend();
	}

# @@@ (thraxisp) this may want a browser check  ( MS IE >= 5.0, Mozilla >= 1.0, Safari >=1.2, ...)
	if ( ( ON == config_get( 'dhtml_filters' ) ) && ( ON == config_get( 'use_javascript' ) ) ){
		?>
		<script type="text/javascript" language="JavaScript">
		<!--
			var string_loading = '<?php echo lang_get( 'loading' );?>';
		 -->
		</script>
		<script type="text/javascript" language="JavaScript" src="javascript/xmlhttprequest.js"></script>
		<script type="text/javascript" language="JavaScript" src="javascript/addLoadEvent.js"></script>
		<script type="text/javascript" language="JavaScript" src="javascript/dynamic_filters.js"></script>
		<?php
	}
?>
<br />
<? 
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
				

			helper_call_custom_function( $t_column_value_function, array( $t_column, $t_row ));

			}
			echo '</tr>';
		}

	}


	function print_bugs_table($rows, $no_name = 0) { //função que imprime cada tabela de cada recurso
		
		global $t_columns, $t_filter, $col_count;
		
?>
<form name="bug_action" method="get" action="bug_actiongroup_page.php">
<table id="buglist" class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="<?php echo $col_count; ?>">
		<?php
			//nome do recurso
			if ($no_name == 0){
				echo user_get_field($rows[0]['handler_id'], 'realname');
			} else {
				echo lang_get('my_view_title_unassigned');
				}

		?>

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

<?php # -- Spacer row -- ?>
<tr class="spacer">
	<td colspan="<?php echo $col_count; ?>"></td>
</tr>
<?php
	

		//exibe o loading PROSEGUR 01/06/2011
		echo '<script type="text/javascript" language="JavaScript">
		loadStartStop(1);
		</script>';
		
	write_bug_rows($rows);
		
		//oculta o loading PROSEGUR 01/06/2011
		echo '<script type="text/javascript" language="JavaScript">
		loadStartStop(0);
		</script>';
	# -- ====================== end of BUG LIST ========================= --

	# -- ====================== MASS BUG MANIPULATION =================== --
?>
	<tr>
		<td class="left" colspan="<?php echo $col_count-2; ?>">
<?php
		if ( $t_checkboxes_exist && ON == config_get( 'use_javascript' ) ) {
			echo "<input type=\"checkbox\" name=\"all_bugs\" value=\"all\" onclick=\"checkall('bug_action', this.form.all_bugs.checked)\" /><span class=\"small\">" . lang_get( 'select_all' ) . '</span>';
		}

		if ( $t_checkboxes_exist ) {
?>
			<select name="action">
				<?php print_all_bug_action_option_list() ?>
			</select>
			<input type="submit" class="button" value="<?php echo lang_get( 'ok' ); ?>" />
<?php
		} else {
			echo '&nbsp;';
		}
?>
		</td>
		<?php # -- Page number links -- ?>
		<td class="rightPaginacao" colspan="2">
			<span class="small">
				<?php
					$f_filter	= gpc_get_int( 'filter', 0);
					print_page_links( 'view_all_bug_page.php', 1, $t_page_count, (int)$f_page_number, $f_filter );
				?>
			</span>
		</td>
	</tr>
<?php # -- ====================== end of MASS BUG MANIPULATION ========================= -- ?>
</table>
</form>

<?php } //fim da função que imprime as tabelas por recurso
	
	//print_bugs_table($rows);
	
	mark_time( 'end loop' );

	if ( $t_status_legend_position == STATUS_LEGEND_POSITION_BOTTOM || $t_status_legend_position == STATUS_LEGEND_POSITION_BOTH ) {
		
		//exibe a legenda com a % de todas as tarefas do(s) projetos selecionados ou exibe somente as visíveis na tela, faz a separação pelo usuário, se o usuário tem permissão de ver tudo, mostra tudo, se não mostra só o do filtro PROSEGUR 02/06/2011
		if ( current_user_get_field('access_level') >= config_get( 'private_task_threshold' ) ) {
		html_status_legend();
		} else {
			html_status_legend($p_id_array);
			}
	}

	# -- ====================== FILTER FORM ========================= --
	if ( ( $t_filter_position & FILTER_POSITION_BOTTOM ) == FILTER_POSITION_BOTTOM ) {
		filter_draw_selection_area( $f_page_number );
	}
	# -- ====================== end of FILTER FORM ================== --
	//até aqui teve como base copia do conteúdo de view_all_inc.php PROSEGUR 14/07/2011
	

	html_page_bottom1( __FILE__ );
?>
