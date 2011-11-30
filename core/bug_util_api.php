<?php
# Mantis - arquivo com fun��es utilitarias diversas que foram sendo necess�rias e implementadas para adaptar o uso do mantis na PROSEGUR
# criado por Raphael Soares - 06/07/2009

//fun��o que envia um e-mail de acordo com o status que ser� alterado,
//� utilizada quando um status � alterado atrav�s das a��o "atualizar status" presente na tela de visualiza��o, que permite que seja alterado o status em lote
function send_mail_status($p_bug_id, $f_status) {  //

	switch ($f_status) {
	
		case 10:
		email_generic( $p_bug_id, 'new', 'email_notification_title_for_status_bug_new' );
		break;
		
		case 20:
		email_generic( $p_bug_id, 'reopened', 'email_notification_title_for_action_bug_reopened' );
		break;
		
		case 30:
		email_generic( $p_bug_id, 'construcao', 'email_notification_title_for_status_bug_acknowledged' );
		break;
		
		case 40:
		email_generic( $p_bug_id, 'paralisada', 'email_notification_title_for_status_bug_confirmed' );
		break;
		
		case 50:
		email_generic( $p_bug_id, 'owner', 'email_notification_title_for_action_bug_assigned' );
		break;
		
		case 70:
		email_generic( $p_bug_id, 'testes', 'email_notification_title_for_status_bug_testes' );
		break;
		
		case 80:
		email_generic( $p_bug_id, 'resolved', 'email_notification_title_for_status_bug_resolved' );
		break;
		
		case 85:
		email_generic( $p_bug_id, 'recusada', 'email_notification_title_for_status_bug_recusado' );
		break;
		
		case 90:
		email_generic( $p_bug_id, 'closed', 'email_notification_title_for_status_bug_closed' );
		break;
		
	}
}

 ?>