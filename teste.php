<?php
	require_once( 'core.php' );

	#pergunta se o usuário tem certeza que deseja apagar o bug	
	helper_ensure_confirmed( lang_get( 'delete_relationship_sure_msg' ), lang_get( 'delete_relationship_button' ) );
	
		#recebe os parametros enviados
		$parametros = explode("?", $HTTP_SERVER_VARS['HTTP_REFERER']);
		
		#retorna para pagina chamadora informando os parametros
		header("location:core/relationship_systems_api.php" . "?" . "$parametros[1]");
	
	?>