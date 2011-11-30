<?php
# Mantis - arquivo com funчѕes utilitarias diversas que foram sendo necessсrias e implementadas para adaptar o uso do mantis na PROSEGUR
# criado por Raphael Soares - 06/07/2009

//funчуo que envia um e-mail de acordo com o status que serс alterado,
//щ utilizada quando um status щ alterado atravщs das aчуo "atualizar status" presente na tela de visualizaчуo, que permite que seja alterado o status em lote
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