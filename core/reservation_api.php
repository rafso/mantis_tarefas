<?php

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'email_api.php' );
	require_once( $t_core_dir . 'string_api.php' );

	### Reservations API ###

function reserv_cache_row( $p_reserv_id, $p_trigger_errors=true ) {
	global $g_cache_reserv;
	
	if ( isset( $g_cache_reserv[$p_reserv_id] ) ) {
		return $g_cache_reserv[$p_reserv_id];
	}
	
	$c_reserv_id	= db_prepare_int( $p_reserv_id );
	$t_reserv_table	= config_get( 'mantis_user_reservations_table' );
	
	$query = "SELECT *
			FROM $t_reserv_table
			WHERE id='$c_reserv_id'";
	$result = db_query( $query );
	
	$row = db_fetch_array( $result );
	$row['date_submitted']	= db_unixtimestamp( $row['date_submitted'] );
	$row['last_updated']	= db_unixtimestamp( $row['last_updated'] );
	//tratando as datas - PROSEGUR 04/03/2011
	$row['date_start']	= db_unixtimestamp( $row['date_start'] );
	$row['date_end']	= db_unixtimestamp( $row['date_end'] );
	$g_cache_reserv[$c_reserv_id] = $row;
	
	return $row;
}


	#===================================
	# Boolean queries and ensures
	#===================================

	# --------------------
	# check to see if reserv exists by id
	# return true if it does, false otherwise
	function reserv_exists( $p_reserv_id ) {
	if ( false == reserv_cache_row( $p_reserv_id, false ) ) {
			return false;
		} else {
			return true;
		}
	}
	#===================================
	# Creation / Deletion / Updating
	#===================================

	# --------------------
	# Create a new reserv and return the reserv id
	#
	function reserv_create( $p_reserv_data ) {

		$c_date_start			= db_prepare_string( $p_reserv_data['date_start'] );
		$c_date_end				= db_prepare_string( $p_reserv_data['date_end'] );
		$c_project_id			= db_prepare_int( $p_reserv_data['project_id'] );
		$c_description			= db_prepare_string( $p_reserv_data['description'] );
		$c_user_owner_id		= db_prepare_int( $p_reserv_data['user_owner_id'] );
		$c_user_reserv_id		= db_prepare_int( $p_reserv_data['user_reserv_id'] );

		$t_reserv_table			= config_get( 'mantis_user_reservations_table' );

		# Insert text information
	$query = "INSERT INTO $t_reserv_table
			( date_start, date_end, project_id, status, user_reserved_id, user_owner_id, date )
			VALUES
			( CONVERT(DATETIME, '$c_date_start',103), CONVERT(DATETIME, '$c_date_end',103),'$c_project_id', '10', '$c_user_reserv_id', '$c_user_owner_id', " . db_now() . " )";
		
		db_query( $query );

	
		$t_reserv_id = db_insert_id($t_reserv_table);

		return $t_reserv_id;
	}
	
	//retorna todas as reservas feitas por um usuário
	function reserv_get_all_user($user_id) {

	$t_reserv_table			= config_get( 'mantis_user_reservations_table' );
	
	$query = "SELECT *
			FROM $t_reserv_table
			WHERE user_owner_id='$user_id'
			ORDER BY date_start DESC";
	$result = db_query( $query );
	
	# envia o e-mail notificando o líder qa
	//email_generic( $task_id, 'updated', $t_action_prefix . 'email_notification_title_for_update_coverage' );

	return $result;

	}

	//função que retorna todos que possuem um perfil selecionado em um determinado projeto
	function get_perfil_project($project_id, $perfil) {

	$t_project_user_list_table = config_get( 'mantis_project_user_list_table' );
	
	if (is_array($project_id)) { //verifica se é um array de projetos ou se é apenas 1 projeto		
		for ($i = 0; $i < count($project_id) ;  $i++ ){
			$t_ids .= $project_id[$i] . ',';
		}
		$t_ids = substr($t_ids, 0, strlen($t_ids) - 1);
		$query = "SELECT distinct user_id
				FROM $t_project_user_list_table
				WHERE project_id IN ($t_ids) AND access_level='$perfil'";
		
		}
	
	elseif  ($project_id != ALL_PROJECTS) {
		$query = "SELECT distinct user_id
				FROM $t_project_user_list_table
				WHERE project_id='$project_id' AND access_level='$perfil'";
	} else {
		$query = "SELECT distinct user_id
				FROM $t_project_user_list_table
				WHERE access_level='$perfil'";
	}
	$result = db_query( $query );
	
	if ( db_num_rows( $result ) > 0 ) {
		return $result;
	} else {
		return false;
	}
	
	}
?>
