<?php
require_once( "core.php" );
$t_core_path = config_get( "core_path" );
require_once( $t_core_path . 'version_api.php' );
require_once('core/relationship_systems_api.php');

//recebe a a��o
$action = $_POST['action'];
$file = $_GET['file']; //usada no myacont para abrir a p�gina pela primeira vez, mostrando o avatar ou mostrando o tempor�rio no refresh da p�gina

//retorna os perfis
if (strstr($action , "changeCove")){
	change_coverage($_POST['taskid'], $_POST['value']);
} elseif  (strstr($action , "changeHours")){
	change_hours_prev($_POST['taskid'], $_POST['value']);
} elseif  (strstr($action , "Visualizar")){
	preview_avatar1();
} elseif  (strstr($action , "Alterar")){
	change_avatar();
} elseif  ($file == 1){
	preview_avatar();
} elseif  (strstr($action , "dellAvatarTemp")){
	file_delete_avatar_temp();
} elseif  ((!isset($file)) and (!isset($action))){
	preview_avatar1();
} elseif  (strstr($action , "email_partial_report")){
	email_partial_report($_POST['array_tasks'], stripslashes($_POST['array_notes']), $_POST['array_bugs']); //stripslashes remove as barras invertidas (\) que o js coloca na codifica��o de passagem dos par�metros quando � inserido aspas
} elseif  (strstr($action , "bug_close")){
	task_close($_POST['id'], 40 , $_POST['tasknote_text']);
}  elseif  (strstr($action , "getTaskField")){
	get_task_field($_POST['taskid'], $_POST['field']);
}  elseif  (strstr($action , "show_cases")){
	show_cases($_POST['sistem'], $_POST['current_proj'], $_POST['status'], $_POST['users']);  //mostra o select com o conte�do dos casos da integra��o
}  elseif  (strstr($action , "show_users")){
	show_users($_POST['sistem'], $_POST['current_proj'], $_POST['show']);  //mostra o select com o conte�do dos casos da integra��o
}  elseif  (strstr($action , "search_ldap")){
	search_ldap($_POST['user'], $_POST['name'], $_POST['email']);  //mostra o select com o conte�do dos casos da integra��o
} 

function change_coverage($task, $value) {
	//atualiza o valor da cobertura da tarefa
	task_coverage_update($task, $value);
	echo $value;
}

function change_hours_prev($task, $value) {
	//atualiza o valor do tempo previsto da tarefa
	task_hours_prev_update($task, $value);
	echo $value;
	
}
//fun��o quando a tela � carregada a primeira vez
function preview_avatar(){
	echo '<link rel="stylesheet" type="text/css" href="css/default.css" />';
	echo '<script type="text/javascript" language="JavaScript" src="javascript/common.js"></script>';
	echo '<div name="preview" id="preview">';
	print_avatar(auth_get_current_user_id(), 1);
	echo '</div>';
	echo '<body class="bodyAvatar">';
	echo '<form id="form1" name="form1" action="task_util.php" enctype="multipart/form-data"
			method="post" onSubmit="return fieldBlank(\'avatar\', \''. lang_get('avatar_file_not_found') .'\')" >';
	echo '<input type="file" class="fileAvatar" id="avatar" name="avatar" value="teste.png"/>';
	echo '<br /><input type="submit" id="visualizar" value="Visualizar" name="action"  />';
	echo ' <input type="submit" id="alterar" value="Alterar" name="action"  />';
	//echo '<input type="hidden" id="action" name="action" value="avatarPreview" />';
	echo '</form></body>';
}
//fun��o quando a op��o de visualizar � acionada
function preview_avatar1(){
	//pasta tempor�ria
	$folder = config_get('folder_avatar') . "temp/";
	
	//verifica se foi passado um arquivo, se n�o foi, � apenas o refresh da p�gina
	if (!isset($_FILES['avatar'])){
		//caminho completo
		$comp = $folder . user_get_field( auth_get_current_user_id(), 'username' ) . "*";
		//usa a fun��o glob para pesquisar pelo arquivo nomeuser.* no diret�rio
		$file = glob($comp);
		$file = $file[0];
		//verifica se existe um arquivo tempor�rio para o usu�rio
		if ( file_exists($file) ) {
			echo '<link rel="stylesheet" type="text/css" href="css/default.css" />';
			echo '<div name="preview" id="preview">';
			echo '<img class="avatar" src="'. $file .'" />';
			echo '</div>';
			echo '<body class="bodyAvatar">';
			echo '<form id="form1" name="form1" action="task_util.php" enctype="multipart/form-data"
					method="post" onSubmit="return fieldBlank(\'avatar\', \''. lang_get('avatar_file_not_found') .'\')">';
			echo '<input type="file" class="fileAvatar" id="avatar" name="avatar"/>';
			echo '<br /><input type="submit" id="visualizar" value="Visualizar" name="action" />';
			echo ' <input type="submit" id="alterar" value="Alterar" name="action" />';
			//echo '<input type="hidden" id="action" name="action" value="avatarPreview" />';
			echo '</form></body>';
		
		}
		
		else {  //se o arquivo n�o existe, mostra o padr�o
			preview_avatar();
		}
		
	} else { //foi passado um arquivo
		
		$avatar;
		
		//tratar o tipo do arquivo
		$type = $_FILES['avatar']['type'];
		
		//tratando o endere�o da imagem para pegar apenas a extens�o do arquivo
		$ext = explode('.', $_FILES['avatar']['name']);
		//verifica o tamanho, pois pode ser que a imagem tenha mais de um ponto no seu nome
		$ext = $ext[count($ext)-1];
		
		$name = user_get_field( auth_get_current_user_id(), 'username' ) . "." . $ext;  //o nome da imagem ser� o login do usu�rio
		$file = $_FILES['avatar']['tmp_name'];
		
		//caminho e nome completo do arquivo
		$comp = $folder . $name;
		
		//verifica se j� n�o existe um arquivo tempor�rio do usu�rio, se existir, apaga antes
		file_delete_avatar_temp();
		
		if (move_uploaded_file($file, $comp)) {
			echo '<link rel="stylesheet" type="text/css" href="css/default.css" />';
			echo '<div name="preview" id="preview">';
			echo '<img class="avatar" src="'. $comp .'" />';
			echo '</div>';
			echo '<body class="bodyAvatar">';
			echo '<form id="form1" name="form1" action="task_util.php" enctype="multipart/form-data"
					method="post">';
			echo '<input type="file" class="fileAvatar" id="avatar" name="avatar"/>';
			echo '<br /><input type="submit" value="Visualizar" name="action" />';
			echo ' <input type="submit" value="Alterar" name="action" />';
			//echo '<input type="hidden" id="action" name="action" value="avatarPreview" />';
			echo '</form></body>';
			echo "<meta HTTP-EQUIV='refresh' CONTENT='0;URL='task_util.php'>";
		}
		else { 
			echo '<script>alert("' . lang_get('avatar_file_error') .'");</script>';
			preview_avatar();
		}
	}
	
}

function change_avatar(){
	//pasta de avatares
	$folder = config_get('folder_avatar');
	
	//verifica se foi passado um arquivo, se n�o foi, pode ser que exista j� no temp
	if (isset($_FILES['avatar'])){
		//tratando o endere�o da imagem para pegar apenas a extens�o do arquivo
		$ext = explode('.', $_FILES['avatar']['name']);
		//verifica o tamanho, pois pode ser que a imagem tenha mais de um ponto no seu nome
		$ext = $ext[count($ext)-1];
		
		$name = user_get_field( auth_get_current_user_id(), 'username' ) . "." . $ext;  //o nome da imagem ser� o login do usu�rio
		$file = $_FILES['avatar']['tmp_name'];
		
		//caminho e nome completo do arquivo
		$comp = $folder . $name;
		
		//usa a fun��o glob para pesquisar pelo arquivo nomeuser.* no diret�rio oficial
		$fileE = glob($folder . user_get_field( auth_get_current_user_id(), 'username' ) . "*");
		$fileE = $fileE[0];
		//verifica se j� existe um avatar para esse usu�rio
		if ( file_exists($fileE) ) { 
			//se ele existe, pega o nome com extens�o dele para a c�pia, nome somente do arquivo, n�o completo
			$avatarEnd = explode("/", $fileE);
			$avatarEnd = $avatarEnd[count($avatarEnd)-1]; //pega o ultimo elemento do array que � o nome do arquivo
			//fazendo backup do antigo, se der erro aborta tudo
			if (rename($fileE, $folder . "bkp_" . $avatarEnd )) { //copia username.jpg para bkp_username.jpg
				//bkp feito, tenta mover o arquivo para a pasta final
				if (move_uploaded_file($file, $comp)) {
					//c�pia e bkp com sucesso, apaga o bkp
					chmod( $folder . "bkp_" . $avatarEnd, 0775 );
					unlink( $folder . "bkp_" . $avatarEnd );
					echo '<script>alert("' . lang_get('avatar_sucess') .'");</script>';
					preview_avatar();
				} else {
					//erro ao mover do temp, volta o backup
					rename($folder . "bkp_" . $avatarEnd, $folder . $avatarEnd  );
					echo '<script>alert("' . lang_get('avatar_backup_error') .'");</script>';
					preview_avatar();
				}
			} else {
				//erro no bkp
				echo '<script>alert("' . lang_get('avatar_backup_error') .'");</script>';
				preview_avatar();
			}
		} else {  //foi passado um arquivo, mas n�o existe avatar oficial para o usu�rio
			if (move_uploaded_file($file, $comp)) {
				echo '<script>alert("' . lang_get('avatar_sucess') .'");</script>';					
				preview_avatar();
			}
			else {
				echo '<script>alert("' . lang_get('avatar_backup_error') .'");</script>';
				preview_avatar();
			}
		}

	} else { //se n�o foi passado, verifica se existe um temp para o usu�rio
		//caminho completo
		$compTemp = $folder . "temp/";
		//usa a fun��o glob para pesquisar pelo arquivo nomeuser.* no diret�rio temp
		$fileT = glob($compTemp . user_get_field( auth_get_current_user_id(), 'username' ) . "*");
		$fileT = $fileT[0];
		//verifica se existe um arquivo tempor�rio para o usu�rio, se existir esse ser� usado na mudan�a
		if ( file_exists($fileT) ) {
			//se ele existe, pega o nome com extens�o dele para a c�pia, nome somente do arquivo, n�o completo
			$avatarTemp = explode("/", $fileT);
			$avatarTemp = $avatarTemp[count($avatarTemp)-1]; //pega o ultimo elemento do array que � o nome do arquivo
			//usa a fun��o glob para pesquisar pelo arquivo nomeuser.* no diret�rio oficial
			$fileE = glob($folder . user_get_field( auth_get_current_user_id(), 'username' ) . "*");
			$fileE = $fileE[0];
			//verifica se j� existe um avatar para esse usu�rio
			if ( file_exists($fileE) ) { 
				//se ele existe, pega o nome com extens�o dele para a c�pia, nome somente do arquivo, n�o completo
				$avatarEnd = explode("/", $fileE);
				$avatarEnd = $avatarEnd[count($avatarEnd)-1]; //pega o ultimo elemento do array que � o nome do arquivo
				//fazendo backup do antigo, se der erro aborta tudo
				if (rename($fileE, $folder . "bkp_" . $avatarEnd )) { //copia username.jpg para bkp_username.jpg
					//bkp feito, tenta mover o arquivo para a pasta final
					if (copy($fileT, $folder . $avatarTemp)) {
						//c�pia e bkp com sucesso, apaga o bkp
						chmod( $folder . "bkp_" . $avatarEnd, 0775 );
						unlink( $folder . "bkp_" . $avatarEnd );
						echo '<script>alert("' . lang_get('avatar_sucess') .'");</script>';						
						preview_avatar();
					} else {
						//erro no upload, volta o backup
						rename($folder . "bkp_" . $avatarEnd, $folder . $avatarEnd  );
						echo '<script>alert("' . lang_get('avatar_backup_error') .'");</script>';
						preview_avatar();
					}
				} else {
					//erro no bkp
					echo '<script>alert("' . lang_get('avatar_backup_error') .'");</script>';
					preview_avatar();
				}

			} else {  //existe arquivo tempor�rio para ele, mas n�o existe avatar oficial
				//copia o tempor�rio para ser o oficial
				if (copy($fileT, $folder . $avatarTemp)) {
					echo '<script>alert("' . lang_get('avatar_sucess') .'");</script>';					
					preview_avatar();
				}
				else {
					echo '<script>alert("' . lang_get('avatar_backup_error') .'");</script>';
					preview_avatar();
				}
			}
			

		}

	}
}

//deleta todos os arquivos tempor�rios de avatar que existir para um usu�rio no dirat�rio tempor�rio
function file_delete_avatar_temp() {
	
		//pasta tempor�ria
		$folder = config_get('folder_avatar') . "temp/";
		//o nome da imagem ser� o login do usu�rio
		$name = user_get_field( auth_get_current_user_id(), 'username' );
		//caminho completo
		$comp = $folder . $name . "*";
		
		//usa a fun��o glob para pesquisar pelo arquivo nomeuser.* no diret�rio
		$file = glob($comp);

	//varre o array com o resultado e apaga os arquivos	
	for ($i = 0; $i < count($file); $i++){	
		if ( file_exists($file[$i]) ) {
			chmod( $file[$i], 0775 );
			unlink( $file[$i] );
		}
	}
}

function email_partial_report($array_tasks, $array_notes, $array_bugs){ //as vari�veis array_tasks e array_notes que s�o enviadas precisam ser desprezadas a primeira posi��o pois a primeira � undefined
	//importa o conte�do do relat�rio para poder ter acesso ao rows
	//require_once('task_partial_report_page.php');
	
	$array_tasks = explode("|", $array_tasks);  //recebendo o array com os casos selecionados
	unset($array_tasks[0]);  //removendo a primeira posi��o pq � undefined
	$array_tasks = array_values($array_tasks);  //reindexando o array
	
	$array_bugs = explode("|", $array_bugs);  //recebendo o array com o n�mero de bugs
	unset($array_bugs[0]);  //removendo a primeira posi��o pq � undefined
	$array_bugs = array_values($array_bugs);  //reindexando o array		
	
	$array_notes = explode("|", $array_notes);  //recebendo o array com as observa��es
	unset($array_notes[0]);  //removendo a primeira posi��o pq � undefined
	$array_notes = array_values($array_notes);  //reindexando o array	
	
	//ap�s pegar os ids que foram selecionados, faz uma consulta montando um array com os bugs que ser�o enviados no e-mail
	for($i = 0; $i < count($array_tasks); $i++){
		$rows[] = bug_cache_row( $array_tasks[$i] );
		$rows[$i]['notes'] = $array_notes[$i];
		$rows[$i]['bugs'] = $array_bugs[$i];
	}
	//chama a fun��o de envio do e-mail passando o array com os bugs
	send_partial_report($rows);
	
	//chama a fun��o que marca os casos com 100% de cobertura e j� enviados
	//cria um novo array contendo somente os casos com 100% de cobertura usando a fun��o de pesquisa em array
	$t_rows = search($rows, 'coverage', '100');
	task_update_report_submitted($t_rows);
	
}

//fun��o que formata e envia o e-mail do relat�rio parcial
function send_partial_report($tasks){

	//ordena o array pela cobertura do teste
	foreach ($tasks as $key => $row) {
		$cont[$key] = $row['coverage'];	
	}
	array_multisort($cont, SORT_DESC, $tasks);
	
	//pega quantos projetos existem e quebra o array recebido com todas as tarefas em um array organizado pelo id do projeto com todas as tarefas separadas por projeto
	$t_project_ids = user_get_accessible_projects( auth_get_current_user_id() );  //pega todos os projetos que o usu�rio tem acesso
	for ($i = 0; $i < count($t_project_ids); $i++){
		$t_tasks_projects[] = search($tasks, 'project_id', $t_project_ids[$i]);
		if (count($t_tasks_projects[$i]) > 0) {  //adiciona apenas se houver tarefas para o projeto
			$tasks_projects[] = $t_tasks_projects[$i];
		}
	}
	
	//for que separa 1 e-mail para cada projeto selecionado
	//print_r($tasks_projects);
	for ($i = 0; $i < count($tasks_projects); $i++){
		//estilo
		$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<style type="text/css">
				body{
				font-family:Verdana, Arial, Helvetica, sans-serif;
				font-size:12px;
				}
				table{
				border-top:1px #fff solid!important;
				}
				td{
				padding:0 5px;
				}
				</style>
				</head>
				<body>';	
		
		//cabe�alho do relat�rio
		if ($tasks_projects[$i][0]['project_id'] > 0) {
			$body .= header_report($tasks_projects[$i][0]['project_id']);  //pega o project_id do primeiro registro dentro de cada indice, como est� separado por projetos o array, sempre ir� pegar o projeto correto
		}
		
		//for que pega as tarefas de cada projeto
		$corlinha = 1; //controla a cor da linha
		for ($j = 0; $j < count($tasks_projects[$i]); $j++){

//			//ordena o array pelo projeto
//			foreach ($tasks_projects[$i] as $key => $row) {
//				$cont[$key] = $row['coverage'];	
//			}
			
			//descri��o linha impar
			if ($corlinha%2) { //verifica se o n�mero � par
				$body .= '<tr style="background:#f1f1f1;"><td><p>';
				$body .=  utf8_decode($tasks_projects[$i][$j]['summary']);
				$body .= '</p></td>';
				//status
				$body .= '<td><p align="center">';
				$body .=  utf8_decode(get_enum_element( 'status', $tasks_projects[$i][$j]['status'] ));
				$body .= '</p></td>';
				//cobertura
				$body .= '<td><p align="center">';
				$body .=  $tasks_projects[$i][$j]['coverage'] . '%';
				$body .= '</p></td>';
				//bugs cr�ticos, fazer depois que a rela��o estiver OK
				$body .= '<td><p align="center">';
				$body .=  $tasks_projects[$i][$j]['bugs'];
				$body .= '</p></td>';
				//observa��o
				$body .= '<td><p>';
				$body .= utf8_decode($tasks_projects[$i][$j]['notes']);
				$body .= '</p></td>';
				$body .= ' </tr>';
			} else {
				//descri��o linha par
				$body .= '<tr><td><p>';
				$body .=  utf8_decode($tasks_projects[$i][$j]['summary']);
				$body .= '</p></td>';
				//status
				$body .= '<td><p align="center">';
				$body .=  utf8_decode(get_enum_element( 'status', $tasks_projects[$i][$j]['status'] ));
				$body .= '</p></td>';
				//cobertura
				$body .= '<td><p align="center">';
				$body .=  $tasks_projects[$i][$j]['coverage'] . '%';
				$body .= '</p></td>';
				//bugs cr�ticos, fazer depois que a rela��o estiver OK
				$body .= '<td><p align="center">';
				$body .=  $tasks_projects[$i][$j]['bugs'];
				$body .= '</p></td>';
				//observa��o
				$body .= '<td><p>';
				$body .= utf8_decode($tasks_projects[$i][$j]['notes']);
				$body .= '</p></td>';
				$body .= ' </tr>';
			}
			
			//pega o id do projeto
			$proj = $tasks_projects[$i][$j]['project_id'];
			$corlinha++;

		}
		$body .= '</table></body></html>';
		if (send_mail_report($body, $proj)){
			$control[$i][0] = utf8_decode(project_get_field( $tasks_projects[$i][0]['project_id'], 'name' ));
			$control[$i][1] = true;
		} else {
			$control[$i][0] = utf8_decode(project_get_field( $tasks_projects[$i][0]['project_id'], 'name' ));
			$control[$i][1] = false;
			$control[$i][2] = " Erro: " . $mail->ErrorInfo;
		}
	}

	//controle de quais foram enviados e quais deram erro
	for ($l = 0; $l < count($control); $l++){
		if ($control[$l][1]) {
			$proj_ok .= $control[$l][0] . ', ';
		} else {
			$proj_nok .= $control[$l][0] . ', ';
			$error .= $control[$l][2] . ', ';
		}
	}
	//imprime os OK
	if (count($proj_ok) > 0) {
		echo 'E-mail enviado corretamente para o(s) projeto(s): ';
		echo substr($proj_ok, 0, -2);
	}
	if (count($proj_nok) > 0) {  //imprime os com erro
		echo "Erro ao enviar e-mail para o(s) projeto(s): ";
		echo substr($proj_nok, 0, -2);
		echo substr($error, 0, -2);
	}
}

#pega os e-mails que receberam o relat�rio do projeto, retorna um array com os e-mails
function get_email_proj($project_id){
	$t_emails_proj = version_get_all_rows( $project_id, 1 );
	foreach ( $t_emails_proj as $t_email_proj ) {
		$email_dest .= $t_email_proj['version'] . ';';
	}
	
	$email_dest = explode(';', $email_dest);
	
	//removendo o ultimo elemento do array que � em branco por causa da concatena��o do ;
	unset($email_dest[sizeof($email_dest)-1]);
	
	//adicionando os e-maisl fixos, configurados no config_inc.php
	$fixed_mail = explode(";", config_get('email_partial_report'));
	foreach ($fixed_mail as $mail) {
		$email_dest[] .= $mail;
	}
	
	//adicionando o e-mail de quem est� enviando o relat�rio
	$email_dest[] .= user_get_email(auth_get_current_user_id());
	
	//reordenando os �ndices
	$email_dest = array_values($email_dest);
	
	//retorna o array de e-mails
	return $email_dest;
}

function send_mail_report($body, $project_id ){

	//pega os e-mails e envia separadamente para cada destinat�rio
	$email_dest = get_email_proj($project_id);
		//pega o id do usu�rio logado
		$user_id  = auth_get_current_user_id();
		
		$subj = "[" . utf8_decode(project_get_field( $project_id, 'name' )) . "] " . utf8_decode(lang_get('report_mail_title'));
		$nome_dest = "";
		
		$mail = new PHPMailer();
		$mail->SetLanguage("br", "./core/phpMailer/language/");
		$mail->IsSMTP(); // mandar via SMTP
		$mail->Host = "smtp.prosegur.net.br"; // Seu servidor smtp
		$mail->SMTPAuth = true; // smtp autenticado
		$mail->Username = "acessoprosegur"; // usu�rio deste servidor smtp
		$mail->Password = "acessopro"; // senha
		
		$mail->From = user_get_email( $user_id );  //envia do e-mail do usu�rio logado
		$mail->FromName = utf8_decode(user_get_realname( $user_id )); //envia com o nome do usu�rio logado
		
		//for para adicionar os destinat�rios no e-mail conforme array recuperado com os e-mails do projeto + os fixos no config_inc
		for ($i=0; $i<sizeof($email_dest); $i++) {
			$mail->AddAddress($email_dest[$i], $nome_dest);
		}

		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(true);                                  // set email format to HTML
		
		$mail->Subject = $subj;
		
		
		$mail->Body    = $body;
		//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

		if(!$mail->Send())
		{
			return false;
//			exit;
			
		} else {
			return true;
		}
}

//fun��o que cria uma nova tabela com o cabe�alho html para o relat�rio, recebe o id do projeto
function header_report($project) {
	return 	'<p><img src="http://corps031:9999/tarefas/images/prosegur.png" alt="Prosegur" /></p>
			<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ffffff">
			<tr style="width:100%;">
			<td colspan="5" style="background:transparent url(http://corps031:9999/tarefas/images/titulo.jpg) 0 0 repeat-x; text-align:center;  font-size:18px; font-weight:bold; border-top: 1px solid #FFDF2D; border-bottom: 1px solid #CEAF00;">
			<b>' . utf8_decode(project_get_field( $project, 'name' )) . '</b></td>
			</tr>
			<tr style="background:#ccc; font-weight:bold;">
			<td style="padding:3px 0; text-align:center;">
			Demanda/Item cronograma/Especifica��o
			</td>
			<td style="padding:3px; text-align:center;">
			Status
			</td>
			<td style="padding:3px; text-align:center;">
			Cobertura
			</td>
			<td style="padding:3px; text-align:center;">
			Bugs cr�ticos
			</td>
			<td style="padding:3px; text-align:center;">
			Observa��o
			</td>
			</tr>';
}

//fun��o de pesquisa em array multidimensional, ela pesquisa em uma key de um array por um valor especificado e retorna um array com todos os registros encontrados
//usado para pegar o array do resultado e criar novos arrays por projeto
function search($array, $key, $value) 
{ 
    $results = array(); 
    if (is_array($array)) 
    { 
        if ($array[$key] == $value) 
            $results[] = $array; 
        foreach ($array as $subarray) 
            $results = array_merge($results, search($subarray, $key, $value)); 
    } 
    return $results; 
} 

//fun��o que fecha a tarefa diretamente, usada ao alterar a cobertura para 100%, recebe o id do caso, resolu��o e anota��o
function task_close($id, $resolution, $tasknote_text){
	//verifica se n�o foi enviado nenhuma nota para chamar sem passar a nota, caso contr�rio o sistema escreve uma nota com NULL
	if ($tasknote_text == 'null') {
		bug_resolve($id, $resolution);
	} else {
		bug_resolve($id, $resolution, $tasknote_text);
	}
}


function send_mail_reserv($reserv_id){
	$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<style type="text/css">
			.cabecalho1, .cabecalho2
			{
			text-align: center;
			background-color: #FFFF99;
			border: 1px solid;
			font-family: Calibri,Arial;
			padding:0 10px 0 10px;	
			}
			.table
			{
			border: 1px solid;
			border-color: #000000;
			border-collapse: collapse;
			}
			.cabecalho2
			{
			font-weight: bold;
			background-color: #DDDDDD;
			font-family: Calibri,Arial;
			}
			.conteudo, .conteudoC
			{
			border: 1px solid;
			border-color: #000000;
			font-family: Calibri;
			font-size: small;
			padding:0 2px 0 2px;
			}
			.conteudoC
			{
			padding:0 10px 0 10px;
			}
			</style>
			</head>
			<body>';
	
	//pega os e-mails e envia separadamente para cada destinat�rio
	$email_dest = get_email_proj($project_id);
	//pega o id do usu�rio logado
	$user_id  = auth_get_current_user_id();
	
	$subj = "[" . utf8_decode(project_get_field( $project_id, 'name' )) . "] " . utf8_decode(lang_get('report_mail_title'));
	$nome_dest = "";
	
	$mail = new PHPMailer();
	$mail->SetLanguage("br", "./core/phpMailer/language/");
	$mail->IsSMTP(); // mandar via SMTP
	$mail->Host = "smtp.prosegur.net.br"; // Seu servidor smtp
	$mail->SMTPAuth = true; // smtp autenticado
	$mail->Username = "acessoprosegur"; // usu�rio deste servidor smtp
	$mail->Password = "acessopro"; // senha
	
	$mail->From = user_get_email( $user_id );  //envia do e-mail do usu�rio logado
	$mail->FromName = user_get_realname( $user_id ); //envia com o nome do usu�rio logado
	
	//for para adicionar os destinat�rios no e-mail conforme array recuperado com os e-mails do projeto + os fixos no config_inc
	for ($i=0; $i<sizeof($email_dest); $i++) {
		$mail->AddAddress($email_dest[$i], $nome_dest);
	}
	
	$mail->WordWrap = 50;                                 // set word wrap to 50 characters
	$mail->IsHTML(true);                                  // set email format to HTML
	
	$mail->Subject = $subj;
	
	
	$mail->Body    = $body;
	//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
	
	if(!$mail->Send())
	{
		return false;
		//			exit;
		
	} else {
		return true;
	}
}

//retorna informa��o de uma tarefa, recebe o id e o campo na tabela, usa fun��o do mantis
function get_task_field($id, $field) {
	echo bug_get_field($id, $field);
}

//fun��o que � chamada via ajax e diretamente para chamar a fun��o do relationship_systems.php que exibe o select com os registros
function show_cases($sistem, $proj_id, $status, $users = 0) {
	$sistema = $sistem;
	integration($sistema, $proj_id, $status, $users);
}

function show_users($sistema, $proj_id, $show = 0){
	user_project_permiss_open_inst($sistema, $proj_id, $show);
}

//exibe os campos datas ocultos, a fun��o � usada no task_repost_integrated_page.php
function show_date($proj_id, $status){

	$qtd_registros = count_register($proj_id, $status);  //chama a fun��o que conta a quantidade total de registros para criar os campos de data ocultos
	
	//se for demandas, cria um campo de data para cada pois ser�o 1 tarefa para cada demanda, se for bug � apenas 1 campo de data pois � apenas uma tarefa para todos os bugs selecionados
	
		for ($i = 1; $i <= $qtd_registros; $i++) { 
			// Datas for que prepara a quantidade de linhas que poder�o ser exibidas baseado na quantidade de registros retornados para sele��o, s�o exibidos e acordo com que � selecionado registro para abrir em lote-->
			echo '<tr '. helper_alternate_class() .' style="display:none" id="'. $i .'">
					<td class="category">
					<span class="required">*</span>'. lang_get( "start_date" ) .'
					</td>
					<td colspan="2">
					<input '. helper_get_tab_index() .' type="text" name="data_inicio'. $i .'" id="data_inicio'. $i .'" size="8" maxlength="10" value="" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'data_inicio'. $i .'\'),\'dd/mm/yyyy\',this)">
					<span class="required">*</span>'. lang_get( "end_date" ) .'
					<input '. helper_get_tab_index() .' type="text" name="data_fim'. $i .'" id="data_fim'. $i .'" size="8" maxlength="10" value="" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'data_fim'. $i .'\'),\'dd/mm/yyyy\',this)">
					<label id="label'. $i .'"></label>
					</td>
					
					</tr>';
			
			
		} //fim do for

		//cria apenas uma data que ser� usada no caso de ser selecionado o sistema de bugs
		echo '<tr '. helper_alternate_class() .' style="display:none" id="50_1">
				<td class="category">
					<span class="required">*</span>'. lang_get( "start_date" ) .'
					</td>
					<td colspan="2">
					<input '. helper_get_tab_index() .' type="text" name="data_inicio50_1" id="data_inicio50_1" size="8" maxlength="10" value="" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'data_inicio50_1\'),\'dd/mm/yyyy\',this)">
					<span class="required">*</span>'. lang_get( "end_date" ) .'
					<input '. helper_get_tab_index() .' type="text" name="data_fim50_1" id="data_fim50_1" size="8" maxlength="10" value="" /> <img src="images/calendar-img.gif" onclick="displayCalendar(document.getElementById(\'data_fim50_1\'),\'dd/mm/yyyy\',this)">
					<label id="label50_1"></label>
				</td>
				
				</tr>';
				
				//cria o campo de resumo para poder escolher se o resumo � de bugs de clientes ou da f�brica, por padr�o f�brica vem marcado
			echo '<tr '. helper_alternate_class() .' style="display:none" id="type_summary" >
				<td class="category">
			<span class="required">*</span>'. lang_get( 'headline' ) . print_documentation_link( 'headline' ) .'
			</td>
			<td colspan="2">
			<input helper_get_tab_index() type="radio" name="type_summary" id="type_summary" value="10" CHECKED /> '. lang_get('factory_bugs') .'
			<input helper_get_tab_index() type="radio" name="type_summary" id="type_summary" value="50" /> '. lang_get('client_bugs') .'
			<input helper_get_tab_index() type="radio" name="type_summary" id="type_summary" value="100" /> '. lang_get('all_bugs') .'
			</td>
			</tr>';
		
	
}

//fun��o que faz uma consulta no ldap para facilitar o preenchimento dos campos de cadastro. recebe o login ou nome ou sobre nome
function search_ldap($user = null, $fname = null, $email = null) {
	
	ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
	
	$ldap_server = "prosegur.net.br";
	$base_dn = "DC=prosegur,DC=net,DC=br";
	$auth_user = "rsoliveira@prosegur.net.br"; //operainsp
	$auth_pass = "567890"; //operinsp2010
	
	$username = $user;
	
	//trata o nome com sobre nome
	$fullname = explode(" ", $fname);
	$name = $fullname[0]; //recebe o primeiro nome
	$sn = $fullname[1]; //recebe o primeiro sobre nome
	$mail = $email;
	
	//campos que ser�o retornados
	$atributos = array("givenname", "sn", "mail", "sAMAccountName", "countrycode");
	
	$filter = "(&";
	
	//filtro de acordo com os dados informados
	if ($sn <> null) { //com o sn
		$filter .= "(sn=*$sn*)";
	} if ($name <> null) { //com o sn
		$filter .= "(givenname=$name)";
	} if ($username <> null) { //com o sn
		$filter .= "(sAMAccountName=$username)";
	} if ($mail <> null) { //com o sn
		$filter .= "(mail=$mail)";
	}
	
	$filter .= ")";
	
	if (($connect=@ldap_connect($ldap_server))) {
		if (($bind=@ldap_bind($connect, $auth_user, $auth_pass))) {
			if (($search=@ldap_search($connect, $base_dn, $filter, $atributos))){
				$number_returned = ldap_count_entries($connect,$search);
				$info = ldap_get_entries($connect, $search);
				
				if ($info["count"] > 1) {
					for ($i=0; $i < $info["count"]; $i++){
						$nome = utf8_decode(ucfirst($info[$i]["givenname"][0]) ." ". utf8_decode(ucfirst($info[$i]["sn"][0])));
						$login = strtolower($info[$i]["samaccountname"][0]);
						$e_mail = strtolower($info[$i]["mail"][0]);
						$string .= "\nNome: " . $nome ."\n" . "Login: ". $login ."\n" . "E-mail: ". $e_mail . "\n";
					}
					echo "2&" . $string;
				} else if ($info["count"] == 1) {
						
						$nome = utf8_decode(ucfirst($info[0]["givenname"][0]) ." ". utf8_decode(ucfirst($info[0]["sn"][0])));
						$login = strtolower($info[0]["samaccountname"][0]);
						$e_mail = strtolower($info[0]["mail"][0]);
						$c = strtolower($info[0]["countrycode"][0]);		
						echo "1&" . $nome ."|". $login ."|". $e_mail . "|" . $c;
						
					} else echo "3&"; //n�o encontrado
				
			} else { echo "erro search"; }
			
		} else { echo "erro bind"; }
		
	} else { echo "erro conectar"; }
	
	// Fecha a conexao LDAP.
	ldap_close($connect);
}


	?>