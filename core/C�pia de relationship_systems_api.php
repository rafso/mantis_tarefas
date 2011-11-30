<?php
# Mantis - a php based bugtracking system

# Copyright (C) 2009  Raphael Soares - rafsopd@gmail.com

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
	# $Id: relationship_systems_api.php,v 0.1 2009-03-26 15:32:44
	# $Id: relationship_systems_api.php,v 0.2 2010-03-03 15:32:44
	# --------------------------------------------------------

	# ======================================================================
	# Author: Raphael Soares' <rafsopd at gmail.com> BRAZIL
	# ======================================================================

	# esta página é chamada pela relationship_api.php

	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path . 'relationship_api.php' );

#controle do post da página, verifica se esta sendo acessada para adicionar ou deletar um relacionamento
if (($_POST) and IsSet($_POST['sistema'])) { #caso a página tenha sido chamada através do formulário de inclusão

	#trata os dados enviados de acordo com o sistema que foi selecionado
	$url = explode('@', gpc_get_string('sistema')); #a url de acesso do sistema é enviado junto com os dados de acesso ao sistema
	$sistema = trim($url[0]); #a variaavel contem na casa 0 todos os dados de acesso ao banco e na casa 1 a url de acesso ao sistema
	$instancia = trim($url[2]);
	$url = trim($url[1]);
	$sistema = explode(':', $sistema);
	$host = trim($sistema[0]);
	$bd = trim($sistema[1]);
	$user = trim($sistema[2]);
	$pass = trim($sistema[3]);

	#conexao com o segundo banco de dados
	$conexao = mssql_connect($host,$user,$pass) or die ("Não foi possível conectar ao banco 1");
	mssql_select_db($bd) or die ("Não foi possível selecionar o banco"); #seleciona o banco de dados

	#chama a função de adicionar
	add_relationship_system($conexao);

	}
	else if ($_GET['act'] === 'del') {
		#chama a função para deletar informando o ID que deve ser exluído
		del_relationship_system($_GET['id_del'], $_GET['id']);
	}

#função que exibe a tabela com os relacionamentos
function show_relationship_systems() {

#verifica se a integração está habilitada no config_inc.php
	if ( ON == config_get( 'relationship_system_enable' ) ) {

	?>

<table class="width100" cellspacing="1">
<tr class="row-1" valign="top">
	<td class="category" width="28%" >
		<? echo lang_get( 'relationships_systems' ); ?>
	</td>
		<form method="post" action="">
	<td><?php echo lang_get( 'systems' );
		#recebe a lista de sistemas que estão integrados e coloca no array
		$sistemas = explode (',', config_get( 'sistemas_relaciona_enum_string' ));

		#recebe a lista de string de conexões para cada sistema
		$conexoes = explode (',', config_get( 'string_conexao_sistemas' ));

		#recebe a lista de urls dos sistemas
		$url = explode (',', config_get( 'url_sistemas' ));
		?>

		<select name="sistema">

		<?php 
		
		$controle = 0;
		for ($i=0;$i<sizeof($sistemas);$i++) {
		
		//verifica a permissão do usuário para visualizar relações com cada sistema
		$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_add';
		if ( access_has_project_level(config_get($permissao))) {
		
		?>
		<option value= "
		<?php #recebe a string contendo os dados da conexão, concatena e envia a string de conexao, a url de acesso e o nome do sistema, isso para cada instância do mantis
		echo $conexoes[$i] ."@". $url[$i] ."@". $sistemas[$i];
		?>" >

		<?php
		#recebe a lista de sistemas
		echo $sistemas[$i];
		?></option>

		<?php  }//fim do if
		//verifica se não tem permissão de adicionar em nenhum sistema para remover os campos de adicionar da tela
		else {
			$controle = $controle + 1;
		}
		
		} //fim do for
		?>

		</select>

		<?php
 #removido por não fazer sentido nas relações entre instancias
	#	<option>?php echo lang_get( 'duplicate_of' ); ? </option>
	#	<option>?php echo lang_get( 'has_duplicate' ); ? </option>
		
		//exibe os campos de adicionar somente se o usuário tiver permissão de adicionar em algum sistema, caso contrario não exibe os campos na tela
		if ($controle <> sizeof($sistemas)) {
		
		//relações que serão apresentadas para o usuário escolher
		echo lang_get( 'this_bug' ); ?>
		<select name="relacao">
		<option value="related_to"><?php echo lang_get( 'related_to' ); ?> </option>

		</select>
		<input type="text" name="dest_bug_id" value="" />
		<input type="submit" name="add_relationship" class="button" value="<?php echo lang_get( 'add_new_relationship_button' ) ?>" />
		<input type="hidden" name="src_bug_id" value="<?php echo gpc_get_int('id');?>" />

<?php  } //fim do if do controle 
else { 
//echo "<font color='red'>Você tem permissão</font>";
}
?>		
		
		</td>
	</tr>
		</form>

	<?php  
		#inicia o processo de exibição dos dados
		#função que retorna os dados
		relationship_system_get_summary(gpc_get_int('id'));

	}
}

	# --------------------
	# print ALL the SYSTEM RELATIONSHIPS  OF A SPECIFIC BUG
	function relationship_system_get_summary( $p_bug_id ) {
	
	echo '<br>'; //espaçamento inicial entre a relação de bugs x sistemas

	#é criado uma tabela para cada sistema cadastrado e que contém relacionamentos
	#recebe a lista de sistemas que estão integrados e coloca no array
	$sistemas = explode (',', config_get( 'sistemas_relaciona_enum_string' ));
	
	for ($i = 0; $i< count($sistemas);$i++) {
	
		//verifica se o usuário tem permissão de visualisar os relacionamentos entre cada sistema
		$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_view';
		if ( access_has_project_level(config_get($permissao))) {	
	
	#chama a funação UPDATESTATUS para atualizar o status do caso destino na tabela local, atualiza o status de todos os casos que serão exibidos para o status atual
	update_status_relacao($p_bug_id, $sistemas[$i]);
	
	#retorna os dados dos relacionamentos existentes para o caso aberto e iterando por sistema
	$query = "SELECT * FROM mantis_bug_relationship_system_table WHERE id_source=$p_bug_id AND sistema_dest='$sistemas[$i]' ORDER BY status ASC";
	
	#executa a query
	$resultado = db_query($query);
	echo '<tr class="print"><td class="print" colspan="2"><table border="0" width="100%" cellpadding="0" cellspacing="1">';

	#exibe somente os sistemas que tem algum caso relacionado:
	if ($resultado->_numOfRows>0){  //verifica o número de registros retornado da pesquisa
	
	if ($i & 1) {  //alternar a cor dos grids dos sistemas
		$cor = "#dfdfdf";
	} else {
		$cor= "#dfdfdf";
	}
	//$cor= "#FFE67D";
	// executa novamente a query para fazer a contagem dos resultados x abertos
	// é necessário executar novamente porque a função do mantis que faz o fecth reinicia a variavel e mesmo criando uma cópia não deu certo
	$open = 0;
	$j = 0;
	$contador = db_query($query); 
	while ($j = db_fetch_array($contador)) {
		if ($j['status'] < 90) {
		$open = $open +  1;
		}

	}
	
	#imprime o cabeçalho do grid
	echo '<tr width="100%" ><b>'. $sistemas[$i] .'</tr>';
	echo '(Total: '. $contador->_numOfRows .'|Abertos: '. $open .')';
	echo '<tr class="row-1_tittle" width="100%"><td class="row-1_tittle" width="15%">Tipo</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.' width="5%">NÃºmero</td>';
	//echo '<td class="row-1" bgcolor='.$cor.' width="10%">Sistema</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.' width="10%">Status</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.' width="10%">Gravidade</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.'>Projeto</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.'>Resumo</td></tr>';

	#trata o resultado, separando em linhas e exibindo na tela
		while ($linha = db_fetch_array($resultado)) {
			$type = $linha['type'];
			$id_dest = $linha['id_dest'];
			#pega somente o número para poder colocar no link
			$link = explode ('=', $id_dest);
			$sev = trim($linha['severity']); //recebe a graidade do caso
			$project = $linha['project'];
			$summary = $linha['summary'];
			$instancia = $linha['sistema_dest'];
			//pega o status atual do caso e trata para ser exibido
			//$st = return_bug_status($link[1], trim($instancia)); //recebe o status do caso relacionado em número
			$st = trim($linha['status']);
			$status =  explode($st.':', config_get('status_enum_string_' . trim($instancia))); //trata o status para o nome
			$status = explode(',' , $status[1]);  //trata o status para o nome
			
			//tratamento da gravidade
			$severity = explode($sev . ':', config_get('severity_enum_string_' . trim($instancia)));
			$severity = explode(',' , $severity[1]);
			
			//$status = explode($linha['status'], $status);
			$id = $linha['id']; #recebe o id do caso, util para exclusão

			#faz a verificação do status para circular a linha
			if ($st<90) {
			
						if ($st>=80){
			
							//validação para tratar os dados que vêm da instância de bugs pois a codificação está incorreta
				if (strstr($instancia, "Bugs")) {
					echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: yellow">'. lang_get($type) .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
					//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade é de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contrário fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }
					echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($project) .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($summary) .'';

					$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_project_level(config_get($permissao))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usuário tiver permissão
					}
					
				} else {			
			
			echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: yellow">'. lang_get($type) .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
			//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade é de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contrário fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }		
			echo '<td class="row-1" bgcolor='.$cor.'>' . $project .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'>' . $summary .'';

					$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_project_level(config_get($permissao))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usuário tiver permissão
					}
				}
			
			}  else {
			
			
				//validação para tratar os dados que vêm da instância de bugs pois a codificação está incorreta
				if (strstr($instancia, "Bugs")) {
					echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: red">'. lang_get($type) .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
					//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade é de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contrário fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }			
					echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($project) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($summary) .'';

					$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_project_level(config_get($permissao))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usuário tiver permissão
					}
				} else {			
			
			echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: red">'. lang_get($type) .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
			//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade é de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contrário fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }	
			echo '<td class="row-1" bgcolor='.$cor.'>' . $project .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $summary .'';

					$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_project_level(config_get($permissao))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usuário tiver permissão
					}
				}
				
				} //fim do else do >=80

			} else {
			
				if (strstr($instancia, "Bugs")) {
				echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%">'. lang_get($type) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
				//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
				//os casos fechados não serão marcados com negrito a severidade
				echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';					
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($project) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($summary) .'';

					$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_project_level(config_get($permissao))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usuário tiver permissão
					}
				
				} else {
				echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%">'. lang_get($type) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
				//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
				//os casos fechados não serão marcos com negrito a severidade
				echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';					
				echo '<td class="row-1" bgcolor='.$cor.'>' . $project .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $summary .'';

					$permissao = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_project_level(config_get($permissao))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usuário tiver permissão
					}
				}
				}

		}
		 
		echo '</table>';
		} #fim do IF verificando se o sistema tem relação
		
		} #termina o for das tabelas
		
		} //fim do if da permissão
		
		echo '</table></table></table>';
	}

#adiciona um relacionamento, insere tanto na tabela da instância local quanto na tabela da instancia destino
	function add_relationship_system($conexao){

	#trata os dados enviados de acordo com o sistema que foi selecionado
	$url = explode('@', gpc_get_string('sistema')); #a url de acesso do sistema é enviado junto com os dados de acesso ao sistema
	$sistema = $url[0]; #a variaavel contem na casa 0 todos os dados de acesso ao banco e na casa 1 a url de acesso ao sistema
	$instancia = $url[2];
	$url = $url[1];

	#organiza os dados enviados para relação
	$type = gpc_get_string('relacao');

	/*verifica se o tipo é filho ou pai, para colocar da forma correta a relação na outra instancia
	if ($type === lang_get('dependant_on')){
	$type_dest = lang_get('blocks');
	}
	else if ($type === lang_get('blocks')){
	$type_dest = lang_get('dependant_on');
	}
	else {
	$type_dest = $type;
	}
	*/
	#verifica se o tipo é filho ou pai, para colocar da forma correta a relação na outra instancia
	if ($type === 'dependant_on'){
	$type_dest = 'blocks';
	}
	else if ($type === 'blocks'){
	$type_dest = 'dependant_on';
	}
	else {
	$type_dest = $type;
	}

	$src_bug_id = gpc_get_int('src_bug_id'); #id do bug de origem - que será o destino da outra instancia
	$dest_bug =  gpc_get_int('dest_bug_id'); #id do bug destino - que será o origem da outra instancia
	$dest_bug_link = $url . "view.php?id=" . trim($dest_bug); #link completo com id do bug destino
	$src_bug_link = config_get('url_instancia_local') . "view.php?id=" . trim($src_bug_id); #link completo do bug local para ser inserido na tabela destino
	$instancia_local = config_get('instancia_local'); #nome da instancia local

	#verificar se ja nao existe um relacionamento entre esses dois casos, caso exista a péssoa necessita antes exclu~ir o relacionamento para inserir o outro
	$query = "SELECT * FROM mantis_bug_relationship_system_table WHERE id_source=$src_bug_id AND id_dest='$dest_bug_link'";
	$resultado = db_query($query);

	#caso não exista uma relação entre os dois casos ele adiciona, caso exista ele exibe uma mensagem informando
	if (db_fetch_array($resultado)==0)	{
	
	#retorna os dados do caso informado em outro sistema
	$query = "SELECT mantis_bug_table.status, mantis_project_table.name, summary FROM mantis_bug_table INNER JOIN mantis_project_table ON(mantis_bug_table.project_id = mantis_project_table.id) where mantis_bug_table.id=$dest_bug";

	#pega os dados do bug no outro banco e insere na tabela
	$resultado = mssql_query($query, $conexao) or die ("Não foi possível executar a consulta1");

	while ($linha = mssql_fetch_array($resultado)) {
		$status = $linha['status'];
		$project = $linha['name'];
		$summary = $linha['summary'];
	}

	if ($status <> '') {
		#query para inserir o relacionamento no banco da instancia local
		$query ="INSERT INTO mantis_bug_relationship_system_table
           (type
           ,id_source
           ,id_dest
           ,status
           ,project
           ,summary
		   ,sistema_dest)
     VALUES
           ('$type'
           ,'$src_bug_id'
           ,'$dest_bug_link'
           ,'$status'
           ,'$project'
           ,'$summary'
		   ,'$instancia')";

		#insere os dados na tabela local
		db_query($query);
		
		#---- inicio da gravação do historico no bug local
		#faz a validação do tipo do relacionamento, de acordo com o tipo é gravado 1,2 ou 3
			/*
			if ($type === lang_get('related_to')) {
				$tipo = 1;
			} else if ($type === lang_get('dependant_on')) {
				$tipo = 2;
		
			} else if ($type === lang_get('blocks')) {
				$tipo = 3;
			}
			*/
			#faz a validação do tipo do relacionamento, de acordo com o tipo é gravado 1,2 ou 3
			if ($type === 'related_to') {
				$tipo = 1;
			} else if ($type === 'dependant_on') {
				$tipo = 2;
		
			} else if ($type === 'blocks') {
				$tipo = 3;
			}
		
		#chama a função do mantis que grava na tabela local
		//criar uma função melhor que pega o usuário da outra instancia, pelo username e grava na outra instancia com o id de lá
		#history_log_event_special_prosegur( $src_bug_id, BUG_ADD_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode($instancia) . ")");
		history_log_event_special( $src_bug_id, BUG_ADD_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode($instancia) . ")" );

		#insere dados na tabela da outra instância

		#retorna os dados do caso local para gravar no em outro sistema
		$query = "SELECT mantis_bug_table.status, mantis_project_table.name, summary FROM mantis_bug_table INNER JOIN mantis_project_table ON(mantis_bug_table.project_id = mantis_project_table.id) where mantis_bug_table.id=$src_bug_id";

		#pega os dados do bug no outro banco e insere na tabela
		$resultado = db_query($query);

		while ($linha = db_fetch_array($resultado)) {
			$status = $linha['status'];
			$project = $linha['name'];
			$summary = $linha['summary'];
		}

		#query para inserir o relacionamento no banco da instancia destino
		$query ="INSERT INTO mantis_bug_relationship_system_table
           (type
           ,id_source
           ,id_dest
           ,status
           ,project
           ,summary
		   ,sistema_dest)
     VALUES
           ('$type_dest'
           ,'$dest_bug'
           ,'$src_bug_link'
           ,'$status'
           ,'$project'
           ,'$summary'
		   ,'$instancia_local')";


		mssql_query($query, $conexao);// or die ("Nao foi possivel inserir na instancia destino");

			#grava no historico do caso relacionado		
			#inverte o tipo de pai para filho e vice versa
			if ($tipo == 2) {
				$tipo = 3;
			} else if ($tipo == 3) {
			    $tipo = 2;
			  }
			  
			#recebe o nome da instancia local e grava os dados na instancia destino
			$instancia = config_get('instancia_local');
			history_log_relathionship( $dest_bug, BUG_ADD_RELATIONSHIP, $tipo, "000" . $src_bug_id . " (" . utf8_decode(trim($instancia)) . ")", $conexao );
			#history_log_relathionship( $dest_bug, BUG_ADD_RELATIONSHIP, $tipo,  "000" . $src_bug_id . " (" . trim($instancia) . ")", $conexao );
		
	}
	else {
		$id = gpc_get_int('src_bug_id');
		echo '<script>alert("' .lang_get('thecase'). ' '. $dest_bug .' '. lang_get('case_notfound') .'" );</script>';
	}
	}

	#exibe a mensagem caso encontre uma relação já existente entre os dois casos, não verifica o tipo, somente os casos, visto que nao é necessario uma relação duplicada entre dois casos com tipos diferentes
	else  {
		$id = gpc_get_int('src_bug_id');
		echo '<script>alert(" '. lang_get('relexist') .' '. $id . ' ' . lang_get('andcase') .' '. $dest_bug .'." );</script>';
	}
	}

	#função para excluir um relacionamento, deve ser excluído nos dois sistemas
	function del_relationship_system($id_deletar, $bug){
	
	$url = config_get('url_instancia_local'); #a url de acesso do sistema é enviado junto com os dados de acesso ao sistema


		#$id_deletar = gpc_get_string('id'); #recebe o id do registro que será apagado no banco
		#$bug = gpc_get_string('bug'); #recebe o numero do bug atual para redirecionar a página

		#query que recebe o ID do caso da instancia relacionada
		$query = "SELECT * FROM mantis_bug_relationship_system_table where id=$id_deletar";
		$resultado = db_query($query);

		#verifica se existe essa relação
			$linha = db_fetch_array($resultado);
		if ($linha==0) {
			#a relação não existe ou pode ter dado algum problema com a consulta, algum caso específico
			 $id = gpc_get_int('src_bug_id');
			echo '<script>alert("'. lang_get('relationship_notfound') .'" );</script>';

		}
		else {
		#pega os nomes dos sistemas disponíveis no config_inc
		$sistema = config_get('sistemas_relaciona_enum_string');
		$sistema = explode(",", $sistema);

		#trata os dados da conexao da instancia relacionada
		$sistema_con = config_get('string_conexao_sistemas'); #a variaavel contem na casa todos os dados de acesso ao banco
		$sistema_con = explode(',', $sistema_con); #recebe as conexões existentes

		#verifica qual é o sistema que está relacionado para pegar os dados da conexão corretos
		$instancia = $linha['sistema_dest'];


	for ( $i = 0 ; $i <= sizeof($sistema) ; $i++ ) {
	
		#faz o tratamento dos dados da conexão de acordo com o sistema que está a relação
		if (strcasecmp(trim($sistema[$i]),trim($instancia))  == 0 ) {
			$string_conexao = explode(":", $sistema_con[$i]);
			$host = $string_conexao[0];
			$bd = $string_conexao[1];
			$user = $string_conexao[2];
			$pass = $string_conexao[3];
		}
}

			#trata o retorno para pegar somente o número do bug
			$src_bug_link = config_get('url_instancia_local') . "view.php?id=" . $bug; #link completo do bug local para ser inserido na tabela destino  #link completo do bug local para ser excluído na tabela destino
			#trata o link para receber o id do bug da instancia destino que está em forma de link

			$id_src = explode("=" , $linha['id_dest']);
			$id_src = $id_src[1];


			#---- inicio da gravação do historico no bug local
			#faz a validação do tipo do relacionamento, de acordo com o tipo é gravado 1,2 ou 3
			/*
			$type = trim($linha['type']);
			$dest_bug = trim($id_src);
			$src_bug_id = trim($linha['id_source']);
			if ($type === lang_get('related_to')) {
				$tipo = 1;
			} else if ($type === lang_get('dependant_on')) {
				$tipo = 2;
		
			} else if ($type === lang_get('blocks')) {
				$tipo = 3;
			}
		*/
			#faz a validação do tipo do relacionamento, de acordo com o tipo é gravado 1,2 ou 3
			$type = trim($linha['type']);
			$dest_bug = trim($id_src);
			$src_bug_id = trim($linha['id_source']);
			if ($type === 'related_to') {
				$tipo = 1;
			} else if ($type === 'dependant_on') {
				$tipo = 2;
		
			} else if ($type === 'blocks') {
				$tipo = 3;
			}
				

			#caso a relação exista ela é excluída local e na instancia da relação
			#query que deleta local pelo ID
			$query ="DELETE FROM mantis_bug_relationship_system_table WHERE id=$id_deletar";
			db_query($query);
			
			#chama a função do mantis que grava na tabela local
			//criar uma função melhor que pega o usuário da outra instancia, pelo username e grava na outra instancia com o id de lá
			#history_log_event_special_prosegur( $src_bug_id, BUG_DEL_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode(trim($instancia)) . ")" );			
			history_log_event_special( $src_bug_id, BUG_DEL_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode(trim($instancia)) . ")" );			

			#conexao com o segundo banco de dados
			$conexao = mssql_connect($host,$user,$pass) or die ("Não foi possível conectar ao banco 3");
			mssql_select_db($bd) or die ("Não foi possível selecionar o banco"); #seleciona o banco de dados
	
			#query que deleta na instancia relacionada pelo id destino e src, visto que só existe uma relação entre dois casos
			$query ="DELETE FROM mantis_bug_relationship_system_table WHERE id_source=$id_src AND id_dest='$src_bug_link'";
			mssql_query($query, $conexao);// or die ("Não foi possível executar a consulta para apagar a relação na instância destino");

			#grava no historico do caso relacionado
			#inverte o tipo de pai para filho e vice versa
			if ($tipo == 2) {
				$tipo = 3;
			} else if ($tipo == 3) {
			    $tipo = 2;
			  }
			
			#recebe o nome da instancia local e grava os dados na instancia destino
			$instancia = config_get('instancia_local');
			history_log_relathionship( $dest_bug, BUG_DEL_RELATIONSHIP, $tipo,  "000" . $src_bug_id . " (" . utf8_decode(trim($instancia)) . ")", $conexao );
			#history_log_relathionship( $dest_bug, BUG_DEL_RELATIONSHIP, $tipo,  "000" . $src_bug_id . " (" . trim($instancia) . ")", $conexao );
			
		}

	}
	
	#função para inserir no histórico do caso da instância relacionada quando é inserido e quando é removido uma relação
	#função copiada e adaptada do próprio mantis
		function history_log_relathionship( $p_bug_id, $p_type, $p_optional='',  $p_optional2='', $id_conexao ) {
		$c_bug_id		= db_prepare_int( $p_bug_id );
		$c_type			= db_prepare_int( $p_type );
		$c_optional		= db_prepare_string( $p_optional );
		$c_optional2	= db_prepare_string( $p_optional2 );

		$t_mantis_bug_history_table = config_get( 'mantis_bug_history_table' );

		$query = "INSERT INTO $t_mantis_bug_history_table
					( user_id, bug_id, date_modified, type, old_value, new_value, field_name )
				VALUES
					( '$t_user_id', '$c_bug_id', " . db_now() . ", '$c_type', '1', '$c_optional2', '' )";
		mssql_query($query, $id_conexao) or die ("Não foi possível executar a consulta para gravar no historico da instância destino");
	}
	
	#função que retorna o status e gravidade de um caso em uma instância, passar a instância caso seja outra e não informar a instância caso seja a local, o status é um ENUM
	function return_bug_status_severity( $id, $instancia='') {
	
		if (strcasecmp($instancia, '')) { //instancia informada

		#pega os nomes dos sistemas disponíveis no config_inc
		$sistema = config_get('sistemas_relaciona_enum_string');
		$sistema = explode(",", $sistema);

		#trata os dados da conexao da instancia relacionada
		$sistema_con = config_get('string_conexao_sistemas'); #a variaavel contem na casa todos os dados de acesso ao banco
		$sistema_con = explode(',', $sistema_con); #recebe as conexões existentes

	for ( $i = 0 ; $i <= sizeof($sistema) ; $i++ ) {
	
		#faz o tratamento dos dados da conexão de acordo com o sistema que está a relação
		if (strcasecmp(trim($sistema[$i]),trim($instancia))  == 0 ) {
			$string_conexao = explode(":", $sistema_con[$i]);
			$host = $string_conexao[0];
			$bd = $string_conexao[1];
			$user = $string_conexao[2];
			$pass = $string_conexao[3];
		}
}

		#conexao com o segundo banco de dados
		$conexao = mssql_connect($host,$user,$pass) or die ("Não foi possível conectar ao banco 3");
		mssql_select_db($bd) or die ("Não foi possível selecionar o banco"); #seleciona o banco de dados
	
		#query que retorna o status do bug
		$query = "SELECT status, severity FROM mantis_bug_table where id=$id";
		$resultado = mssql_query($query, $conexao) or die ("Não foi possível executar a consulta para apagar a relação na instância destino");
		$resultado = mssql_fetch_array($resultado);
		$status = $resultado['status'];
		$severity = $resultado['severity'];
		return $status . "," . $severity;

		}
		else { //instancia local
		#query que recebe o status
		$query = "SELECT status FROM mantis_bug_table where id=$id";
		$resultado = db_query($query);
		$resultado = db_fetch_array($resultado);
		$status = $resultado['status'];
		$severity = $resultado['severity'];
		return $status . "," . $severity;
		}
	
	}
	
	//atualiza na tabela o status e gravidade do bug, sempre verifica antes de exibir pois pode ter sido alterado
	function update_status_relacao($bug_src, $sistema) {
	
	#retorna os dados dos relacionamentos existentes para o caso aberto e iterando por sistema
	$query = "SELECT id_dest, sistema_dest FROM mantis_bug_relationship_system_table WHERE id_source=$bug_src AND sistema_dest='$sistema' ORDER BY status ASC";
	$resultado = db_query($query);
	
		#trata o resultado, separando em linhas e exibindo na tela
		while ($linha = db_fetch_array($resultado)) {
			$id_dest = $linha['id_dest'];
			#pega somente o número
			$link = explode ('=', $id_dest);
			$instancia = $linha['sistema_dest'];
			//pega o status atual do caso e trata para ser atualizado na tabela local
			$dados = explode("," , return_bug_status_severity($link[1], trim($instancia))); //recebe o status do caso relacionado em número
			$status = $dados[0];
			$severity = $dados[1];
			
			if ($status > 0) {  //verifica se a função retornou algum status para o caso informado, caso contrário o caso pode ter sido apagado.
				#atualiza o status do bug destino na tabela local, atualiza o status desse bug em todos os relacionamentos que ele participa
				$lin = trim($link[1]);  //remove os espaços
				db_query("UPDATE mantis_bug_relationship_system_table SET status = $status, severity = $severity WHERE REPLACE(id_dest, ' ', '') LIKE '%id=$lin'");	
			
			} else {
				echo '<script>alert("'. lang_get('thecase') .' '. trim($link[1]) .' '. lang_get('that_system') .' '. trim($instancia) .' '. lang_get('maybe_deleted') .'")</script>';
				}
			
		}

	}
	
	//função que retorna os relatórios envolvendo as demandas para serem exibidos na página dos relatórios
	function showReport() {
	
	}
	
	//funação que retorna as demandas em testes há mais de 7 dias, informando também quem é o analista de testes que atuou nela, provavelmente era quem deveria ter alterado o status para testada
	function showInTest(){
	
	
	}
	
	//função auxiliar que retorna quantos bugs estão abertos para uma determinada demanda
	function showBugsOpen($demanda) {
	
	}
	
	function showLoad(){
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $ub = '';
    if(preg_match('/MSIE/i',$u_agent))
    {
	echo '<script type="text/javascript" src="javascript/jquery-1.2.6.min.js"></script>
	<div id="carregandoIE"><img id="imagem" class="imgLoad" src="images/Carregando.gif"><br>'. lang_get("loading") .'
				</div>
								<script type="text/javascript" src="javascript/jquery-1.2.6.min.js"></script>
                
				<script>			
                        window.onload=function(){
								$("#carregandoIE").fadeOut("slow");
                        }
				</script>';
    }
    else 
    {
   echo '<script type="text/javascript" src="javascript/jquery-1.2.6.min.js"></script>
   <div id="carregandoFX"><img class="imgLoad" src="images/Carregando.gif"><br>'. lang_get("loading") .'
				</div>
				<script type="text/javascript" src="javascript/jquery-1.2.6.min.js"></script>
                
				<script>			
                        window.onload=function(){
								$("#carregandoFX").fadeOut("slow");
                        }
				</script>';

    } 
	
	}

?>