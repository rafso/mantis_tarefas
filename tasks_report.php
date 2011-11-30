<?php
# Mantis - a php based bugtracking system

# página que efetua a criação de tarefas em lote, integradas, a criação normal de tarefas é feita pela página bug_report.php

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'string_api.php' );
	require_once( $t_core_path.'file_api.php' );
	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
	require_once( $t_core_path.'relationship_systems_api.php' );

	access_ensure_project_level( config_get('report_bug_threshold' ) );
	
	//quantidade de registros selecionados para gerar tarefas
	$tasks_id = $_POST['casos'];
	$type = $_POST['type_task'];
	$perf_type = $_POST['type_task_perf']; //tarefa de teste ou desenvolvimento? 10 é desenv e 50 é teste

//verificação se é tarefa com bugs ou demandas que será criado, com demandas é uma tarefa para cada demanda, para bugs é uma tarefa única para todos os bugs selecionados
if ($type == 10) {	//demandas
	//for que faz a criação das tarefas
	for ($i = 0; $i < sizeof($tasks_id); $i++) {
		
		//recupera os dados para o sumario e texto, será o mesmo da demanda ou se for bug será um conteúdo fixo + os números dos bugs
		$query = "SELECT b.summary as summary, t.description
				FROM mantis_bug_table as b INNER JOIN mantis_bug_text_table as t ON t.id = b.id
				where b.id = $tasks_id[$i]";
		$result = exec_query("Demandas", $query); 
		$line = mssql_fetch_array($result);
		$summary = $line['summary'];
		$description = $line['description'];
		
		
		$t_bug_data = new BugData;
		$t_bug_data->handler_id			= gpc_get_int( 'handler_id', 0 );
		$t_bug_data->view_state			= gpc_get_int( 'view_state', config_get( 'default_bug_view_status' ) );
		$t_bug_data->type_task			= gpc_get_int( 'type_task', 0 );
		$t_bug_data->date_start			= gpc_get_string( 'data_inicio'. ($i + 1), '' ); //o id começa em 1 e o for em 0, por isso + 1
		$t_bug_data->date_end			= gpc_get_string( 'data_fim'. ($i + 1), '' ); //o id começa em 1 e o for em 0, por isso + 1
		$t_bug_data->hours_prev			= gpc_get_int( 'hours_prev', 0 );
		$t_bug_data->relation_cases		= gpc_get_string( 'numero', '' );
		
		$t_bug_data->priority				= gpc_get_int( 'priority', config_get( 'default_bug_priority' ) );
		$t_bug_data->summary				= $tasks_id[$i] .': '.$summary;
		$t_bug_data->description			= $description;
		$t_bug_data->steps_to_reproduce	= gpc_get_string( 'steps_to_reproduce', config_get( 'default_bug_steps_to_reproduce' ) );
		$t_bug_data->additional_information	= gpc_get_string( 'additional_info', config_get ( 'default_bug_additional_info' ) );
		
		$t_bug_data->project_id			= gpc_get_int( 'project_id' );
		
		$t_bug_data->reporter_id		= auth_get_current_user_id();
		
		$t_bug_data->summary			= trim( $t_bug_data->summary );
		
		helper_call_custom_function( 'issue_create_validate', array( $t_bug_data ) );
		
	# Create the bug
	$t_bug_id = bug_create( $t_bug_data );
	
	
	email_new_bug( $t_bug_id );
	
	helper_call_custom_function( 'issue_create_notify', array( $t_bug_id ) );
	
		} //fim do for de criação de tarefas para demandas
	
	} //fim do if para a demanda e início dos dados se for bugs
		else if ($type == 50) {
		//verifica se é desenv ou teste para mudar o título
		if ($perf_type == 10) {
			$text_summary_bug = lang_get('description_task_bug'); //texto para resolução de bugs
		}
		
		else if ($perf_type == 50){	
				//na de teste verifica o tipo de título que vai ser exibido, bugs de fábrica ou externos, 10 == fábrica e 50 == externos
				if ($_POST['type_summary'] == 10){
					$text_summary_bug = lang_get('summary_task_bug_factory'); //texto para os bugs da fábrica
				}
				else if ($_POST['type_summary'] == 50) {
						$text_summary_bug = lang_get('summary_task_bug_client'); //texto para os bugs do cliente
					}
					else {
						$text_summary_bug = lang_get('summary_task_bug_all'); //texto para bugs do cliente e interno juntos
					}
			}
		
		//verifica se foram selecionados até 5 bugs, se for os ids serão inseridos no resumo da tarefa
		if (sizeof($tasks_id) <= 5) {
			//sumário com um texto menor e os ids, ex: Verificar os bugs: XXXXX, XXXX, XXXX
			$summary = $text_summary_bug . ": " . implode(", ", $tasks_id);
		} else {
			//sumário sem os ids dos casos, ex: Verificação de bugs entregues ou Verificação de bugs do cliente
			$summary = $text_summary_bug . " (" . sizeof($tasks_id) . " " . lang_get('cases') .")";
		}
		
		//for que faz a montagem da descrição com os ids dos casos
		$description = lang_get('description_task_bug')  . "\n";
		for ($i = 0; $i < sizeof($tasks_id); $i++) {
			
			$description .= "- ". $tasks_id[$i] . "\n";
		}
		
		$_POST;
		$t_bug_data = new BugData;
		$t_bug_data->handler_id			= gpc_get_int( 'handler_id', 0 );
		$t_bug_data->view_state			= gpc_get_int( 'view_state', config_get( 'default_bug_view_status' ) );
		$t_bug_data->type_task			= gpc_get_int( 'type_task', 0 );
		$t_bug_data->date_start			= gpc_get_string( 'data_inicio50_1' );
		$t_bug_data->date_end			= gpc_get_string( 'data_fim50_1' );
		$t_bug_data->hours_prev			= gpc_get_int( 'hours_prev', 0 );
		$t_bug_data->relation_cases		= gpc_get_string( 'numero', '' );
		
		$t_bug_data->priority				= gpc_get_int( 'priority', config_get( 'default_bug_priority' ) );
		$t_bug_data->summary				= $summary;
		$t_bug_data->description			= htmlentities($description);
		$t_bug_data->steps_to_reproduce	= gpc_get_string( 'steps_to_reproduce', config_get( 'default_bug_steps_to_reproduce' ) );
		$t_bug_data->additional_information	= gpc_get_string( 'additional_info', config_get ( 'default_bug_additional_info' ) );
		
		$t_bug_data->project_id			= gpc_get_int( 'project_id' );
		
		$t_bug_data->reporter_id		= auth_get_current_user_id();
		
		$t_bug_data->summary			= trim( $t_bug_data->summary );
		
		helper_call_custom_function( 'issue_create_validate', array( $t_bug_data ) );
		
		# Create the bug
		$t_bug_id = bug_create( $t_bug_data );
		
		
		email_new_bug( $t_bug_id );
		
		helper_call_custom_function( 'issue_create_notify', array( $t_bug_id ) );
		}

	html_page_top1();

	if ( ! $f_report_stay ) {
		//html_meta_redirect( 'view_all_bug_page.php' );
	}

	html_page_top2();
	
	
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( string_get_bug_view_url( $t_bug_id ), lang_get( 'view_submitted_bug_link' ) . " $t_bug_id" );
	print_bracket_link( 'view_all_bug_page.php', lang_get( 'view_bugs_link' ) );

	
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
