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

	# esta p�gina � chamada pela relationship_api.php

	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path . 'relationship_api.php' );

#controle do post da p�gina, verifica se esta sendo acessada para adicionar ou deletar um relacionamento
if (($_POST) and IsSet($_POST['sistema'])) { #caso a p�gina tenha sido chamada atrav�s do formul�rio de inclus�o

	#trata os dados enviados de acordo com o sistema que foi selecionado
	$url = explode('@', gpc_get_string('sistema')); #a url de acesso do sistema � enviado junto com os dados de acesso ao sistema
	$sistema = trim($url[0]); #a variaavel contem na casa 0 todos os dados de acesso ao banco e na casa 1 a url de acesso ao sistema
	$instancia = trim($url[2]);
	$url = trim($url[1]);
	$sistema = explode(':', $sistema);
	$host = trim($sistema[0]);
	$bd = trim($sistema[1]);
	$user = trim($sistema[2]);
	$pass = trim($sistema[3]);

	#conexao com o segundo banco de dados
	$conexao = mssql_connect($host,$user,$pass) or die ("N�o foi poss�vel conectar ao banco: $bd no servidor: $host");
	mssql_select_db($bd) or die ("N�o foi poss�vel selecionar o banco"); #seleciona o banco de dados

	#chama a fun��o de adicionar
	add_relationship_system($conexao);

	}
	else if ($_GET['act'] === 'del') {
		#chama a fun��o para deletar informando o ID que deve ser exlu�do
		del_relationship_system($_GET['id_del'], $_GET['id']);
	}

#fun��o que exibe a tabela com os relacionamentos
function show_relationship_systems() {
#verifica se a integra��o est� habilitada no config_inc.php
	if ( ON == config_get( 'relationship_system_enable' ) ) {
	?>

<table class="width100" cellspacing="1">
<tr class="row-1" valign="top">
	<td class="category" width="28%" >
		<? echo lang_get( 'relationships_systems' ); ?>
	</td>
		<form method="post" action="">
	<td><?php echo lang_get( 'systems' );
		#recebe a lista de sistemas que est�o integrados e coloca no array
		$sistemas = explode (',', config_get( 'sistemas_relaciona_enum_string' ));

		#recebe a lista de string de conex�es para cada sistema
		$conexoes = explode (',', config_get( 'string_conexao_sistemas' ));

		#recebe a lista de urls dos sistemas
		$url = explode (',', config_get( 'url_sistemas' ));
		
		//verificar se o usu�rio n�o tem nenhuma permiss�o para remover o select da tela		
		$controle = 0;
		for ($i=0;$i<sizeof($sistemas);$i++) {	
		$permissaoAdd = 'set_relationship_systems_' . $sistemas[$i] . '_add';
		if ( access_has_global_level(config_get($permissaoAdd))) {  //verificar se a pessoa tem permiss�o de adicionar

		 } else {
			$controle = $controle + 1;  //se n�o tem permiss�o em 1 sistema, adiciona no controle, no final se o controle for = sizeoff(sistemas) � porque n�o tem permiss�o em nenhum sistema
		}
		
		} //fim do for
		
			//exide o select apenas se tiver permiss�o em 1 sistema pelo menos, caso contr�rio n�o abre o select, abaixo ser� verificado 
			if ($controle <> sizeof($sistemas)) {
			?>

			<select name="sistema">
			
			<?
		} //fim do se do select
		$controle = 0;
		for ($i=0;$i<sizeof($sistemas);$i++) {	

		$permissaoAdd = 'set_relationship_systems_' . $sistemas[$i] . '_add';
		if ( access_has_global_level(config_get($permissaoAdd))) {  //verificar se a pessoa tem permiss�o de adicionar
		echo lang_get( 'systems' );
				?>
		<option value= "
		<?php #recebe a string contendo os dados da conex�o, concatena e envia a string de conexao, a url de acesso e o nome do sistema, isso para cada inst�ncia do mantis
		echo $conexoes[$i] ."@". $url[$i] ."@". $sistemas[$i];
		?>" >

		<?php
		#recebe a lista de sistemas
		echo $sistemas[$i];
		?></option>

		<?php  } //fim do if
		//verifica se n�o tem permiss�o de adicionar em nenhum sistema para remover os campos de adicionar da tela
		else {
			$controle = $controle + 1;
		}
		
		} //fim do for
		?>

		</select>

		<?php
 #removido por n�o fazer sentido nas rela��es entre instancias
	#	<option>?php echo lang_get( 'duplicate_of' ); ? </option>
	#	<option>?php echo lang_get( 'has_duplicate' ); ? </option>
		
		//exibe os campos de adicionar somente se o usu�rio tiver permiss�o de adicionar em algum sistema, caso contrario n�o exibe os campos na tela
		if ($controle <> sizeof($sistemas)) {
		
		//rela��es que ser�o apresentadas para o usu�rio escolher
		echo lang_get( 'this_bug' ); ?>
		<select name="relacao">
		<option value="related_to"><?php echo lang_get( 'related_to' ); ?> </option>

		</select>
		<input type="text" name="dest_bug_id" value="" />
		<input type="submit" name="add_relationship" class="button" value="<?php echo lang_get( 'add_new_relationship_button' ) ?>" />
		<input type="hidden" name="src_bug_id" value="<?php echo gpc_get_int('id');?>" />

<?php  } //fim do if do controle 
else { 
//echo "<font color='red'>Voc� tem permiss�o</font>";
}
?>		
		
		</td>
	</tr>
		</form>

	<?php  
		#inicia o processo de exibi��o dos dados
		#fun��o que retorna os dados
		relationship_system_get_summary(gpc_get_int('id'));

	}
}

	# --------------------
	# print ALL the SYSTEM RELATIONSHIPS  OF A SPECIFIC BUG
	function relationship_system_get_summary( $p_bug_id ) {
	
	echo '<br>'; //espa�amento inicial entre a rela��o de bugs x sistemas

	#� criado uma tabela para cada sistema cadastrado e que cont�m relacionamentos
	#recebe a lista de sistemas que est�o integrados e coloca no array
	$sistemas = explode (',', config_get( 'sistemas_relaciona_enum_string' ));
	
	for ($i = 0; $i< count($sistemas);$i++) {
	
		//verifica se o usu�rio tem permiss�o de visualisar os relacionamentos entre cada sistema
		$permissaoView = 'set_relationship_systems_' . $sistemas[$i] . '_view';
		$permissaoDel = 'set_relationship_systems_' . $sistemas[$i] . '_del';		
		if ( access_has_global_level(config_get($permissaoView))) {  //verificar se o usu�rio tem permiss�o para visualizar o relacionamento com determinado sistema
	
	#chama a funa��o UPDATESTATUS para atualizar o status do caso destino na tabela local, atualiza o status de todos os casos que ser�o exibidos para o status atual
	//update_status_relacao($p_bug_id, $sistemas[$i]);
	
	#retorna os dados dos relacionamentos existentes para o caso aberto e iterando por sistema
	$query = "SELECT * FROM mantis_bug_relationship_system_table WHERE id_source=$p_bug_id AND sistema_dest='$sistemas[$i]' ORDER BY status ASC";
	
	#executa a query
	$resultado = db_query($query);
	echo '<tr class="print"><td class="print" colspan="2"><table border="0" width="100%" cellpadding="0" cellspacing="1">';

	#exibe somente os sistemas que tem algum caso relacionado:
	if ($resultado->_numOfRows>0){  //verifica o n�mero de registros retornado da pesquisa
	
	if ($i & 1) {  //alternar a cor dos grids dos sistemas
		$cor = "#dfdfdf";
	} else {
		$cor= "#dfdfdf";
	}
	//$cor= "#FFE67D";
	// executa novamente a query para fazer a contagem dos resultados x abertos
	// � necess�rio executar novamente porque a fun��o do mantis que faz o fecth reinicia a variavel e mesmo criando uma c�pia n�o deu certo
	$open = 0;
	$j = 0;
	$contador = db_query($query); 
	while ($j = db_fetch_array($contador)) {
		#pega somente o n�mero
		$link = explode ('=', $j['id_dest']);
		$stat = explode("," , return_bug_status_severity($link[1], trim($j['sistema_dest']))); //recebe o status do caso relacionado em n�mero
		if ($stat[0] < 90) {
		$open = $open +  1;
		}

	}
	
	#imprime o cabe�alho do grid
	echo '<tr width="100%" ><b>'. $sistemas[$i] .'</tr>';
	echo '(Total: '. $contador->_numOfRows .'|Abertos: '. $open .')';
	echo '<tr class="row-1_tittle" width="100%"><td class="row-1_tittle" width="15%">Tipo</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.' width="5%">Número</td>';
	//echo '<td class="row-1" bgcolor='.$cor.' width="10%">Sistema</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.' width="10%">Status</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.' width="10%">Gravidade</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.'>Projeto</td>';
	echo '<td class="row-1_tittle" bgcolor='.$cor.'>Resumo</td></tr>';

	#trata o resultado, separando em linhas e exibindo na tela
		while ($linha = db_fetch_array($resultado)) {
			$type = $linha['type'];
			$id_dest = $linha['id_dest'];
			#pega somente o n�mero para poder colocar no link
			$link = explode ('=', $id_dest);
			//$sev = trim($linha['severity']); //recebe a graidade do caso
			$instancia = $linha['sistema_dest'];
			$dados = explode("," , return_bug_status_severity($link[1], trim($instancia))); //recebe o status do caso relacionado em n�mero
			$st = $dados[0];
			$sev = $dados[1];
			$project = $linha['project'];
			$summary = $linha['summary'];
			//pega o status atual do caso e trata para ser exibido
			//$st = return_bug_status($link[1], trim($instancia)); //recebe o status do caso relacionado em n�mero
			
			//$st = trim($linha['status']);
			$status =  explode($st.':', config_get('status_enum_string_' . trim($instancia))); //trata o status para o nome
			$status = explode(',' , $status[1]);  //trata o status para o nome
			
			//tratamento da gravidade
			$severity = explode($sev . ':', config_get('severity_enum_string_' . trim($instancia)));
			$severity = explode(',' , $severity[1]);
			
			//$status = explode($linha['status'], $status);
			$id = $linha['id']; #recebe o id do caso, util para exclus�o

			#faz a verifica��o do status para circular a linha
			if ($st<90) {
			
						if ($st>=80){
			
							//valida��o para tratar os dados que v�m da inst�ncia de bugs pois a codifica��o est� incorreta
				if (strstr($instancia, "Bugs")) {
					echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: yellow">'. lang_get($type) .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
					//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade � de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contr�rio fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }
					echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($project) .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($summary) .'';

					$permissaoDel = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_global_level(config_get($permissaoDel))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usu�rio tiver permiss�o
					}
					
				} else {			
			
			echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: yellow">'. lang_get($type) .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
			//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade � de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contr�rio fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }		
			echo '<td class="row-1" bgcolor='.$cor.'>' . $project .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'>' . $summary .'';

					$permissaoDel = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_global_level(config_get($permissaoDel))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usu�rio tiver permiss�o
					}
				}
			
			}  else {
			
			
				//valida��o para tratar os dados que v�m da inst�ncia de bugs pois a codifica��o est� incorreta
				if (strstr($instancia, "Bugs")) {
					echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: red">'. lang_get($type) .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
					//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
					echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade � de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contr�rio fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }			
					echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($project) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($summary) .'';

					$permissaoDel = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_global_level(config_get($permissaoDel))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usu�rio tiver permiss�o
					}
				} else {			
			
			echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%" style="border-style: solid;border-width:7px;border-right: 0px;border-top: 0px; border-bottom: 0px;border-color: red">'. lang_get($type) .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
			//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
			echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
					//verificar se a gravidade � de grande para cima e coloca negrito
					if ($sev >=60) {
						echo '<td class="row-1" bgcolor='.$cor.'><b>' . $severity[0] .'</b></td>';					
					} else {  //caso contr�rio fica sem negrito
						echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';
					  }	
			echo '<td class="row-1" bgcolor='.$cor.'>' . $project .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $summary .'';

					$permissaoDel = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_global_level(config_get($permissaoDel))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usu�rio tiver permiss�o
					}
				}
				
				} //fim do else do >=80

			} else {
			
				if (strstr($instancia, "Bugs")) {
				echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%">'. lang_get($type) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
				//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
				//os casos fechados n�o ser�o marcados com negrito a severidade
				echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';					
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($project) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($summary) .'';

					$permissaoDel = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_global_level(config_get($permissaoDel))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usu�rio tiver permiss�o
					}
				
				} else {
				echo '<tr class="row-1" width="100%"><td bgcolor='.$cor.' width="15%">'. lang_get($type) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'><a href=" '. $id_dest . ' " target="_blank" >'. $link[1] .'</td>';
				//echo '<td class="row-1" bgcolor='.$cor.'> '. $instancia .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0] .'</td>';
				//os casos fechados n�o ser�o marcos com negrito a severidade
				echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';					
				echo '<td class="row-1" bgcolor='.$cor.'>' . $project .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $summary .'';

					$permissaoDel = 'set_relationship_systems_' . $sistemas[$i] . '_del';
					if ( access_has_global_level(config_get($permissaoDel))) {
						echo '[<a class="small" href="?act=del&id_del='. $id .'&id='. $p_bug_id . '" onclick="return confirm(\''. lang_get('shure_delete'). '\')" >' . lang_get('delete_link') .'</a>]</td></tr>';	//esibe o link para apagar somente se o usu�rio tiver permiss�o
					}
				}
				}

		}
		 
		echo '</table>';
		} #fim do IF verificando se o sistema tem rela��o
		
		} #termina o for das tabelas
		
		} //fim do if da permiss�o
		
		echo '</table></table></table>';
	}

#adiciona um relacionamento, insere tanto na tabela da inst�ncia local quanto na tabela da instancia destino
	function add_relationship_system($conexao){

	#trata os dados enviados de acordo com o sistema que foi selecionado
	$url = explode('@', gpc_get_string('sistema')); #a url de acesso do sistema � enviado junto com os dados de acesso ao sistema
	$sistema = $url[0]; #a variaavel contem na casa 0 todos os dados de acesso ao banco e na casa 1 a url de acesso ao sistema
	$instancia = $url[2];
	$url = $url[1];

	#organiza os dados enviados para rela��o
	$type = gpc_get_string('relacao');

	/*verifica se o tipo � filho ou pai, para colocar da forma correta a rela��o na outra instancia
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
	#verifica se o tipo � filho ou pai, para colocar da forma correta a rela��o na outra instancia
	if ($type === 'dependant_on'){
	$type_dest = 'blocks';
	}
	else if ($type === 'blocks'){
	$type_dest = 'dependant_on';
	}
	else {
	$type_dest = $type;
	}

	$src_bug_id = gpc_get_int('src_bug_id'); #id do bug de origem - que ser� o destino da outra instancia
	$dest_bug =  gpc_get_int('dest_bug_id'); #id do bug destino - que ser� o origem da outra instancia
	$dest_bug_link = $url . "view.php?id=" . trim($dest_bug); #link completo com id do bug destino
	$src_bug_link = config_get('url_instancia_local') . "view.php?id=" . trim($src_bug_id); #link completo do bug local para ser inserido na tabela destino
	$instancia_local = config_get('instancia_local'); #nome da instancia local

	#verificar se ja nao existe um relacionamento entre esses dois casos, caso exista a p�ssoa necessita antes exclu~ir o relacionamento para inserir o outro
	$query = "SELECT * FROM mantis_bug_relationship_system_table WHERE id_source=$src_bug_id AND id_dest='$dest_bug_link'";
	$resultado = db_query($query);

	#caso n�o exista uma rela��o entre os dois casos ele adiciona, caso exista ele exibe uma mensagem informando
	if (db_fetch_array($resultado)==0)	{
	
	#retorna os dados do caso informado em outro sistema
	$query = "SELECT mantis_bug_table.status, mantis_project_table.name, summary FROM mantis_bug_table INNER JOIN mantis_project_table ON(mantis_bug_table.project_id = mantis_project_table.id) where mantis_bug_table.id=$dest_bug";

	#pega os dados do bug no outro banco e insere na tabela
	$resultado = mssql_query($query, $conexao) or die ("N�o foi poss�vel executar a consulta1");

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
		
		#---- inicio da grava��o do historico no bug local
		#faz a valida��o do tipo do relacionamento, de acordo com o tipo � gravado 1,2 ou 3
			/*
			if ($type === lang_get('related_to')) {
				$tipo = 1;
			} else if ($type === lang_get('dependant_on')) {
				$tipo = 2;
		
			} else if ($type === lang_get('blocks')) {
				$tipo = 3;
			}
			*/
			#faz a valida��o do tipo do relacionamento, de acordo com o tipo � gravado 1,2 ou 3
			if ($type === 'related_to') {
				$tipo = 1;
			} else if ($type === 'dependant_on') {
				$tipo = 2;
		
			} else if ($type === 'blocks') {
				$tipo = 3;
			}
		
		#chama a fun��o do mantis que grava na tabela local
		//criar uma fun��o melhor que pega o usu�rio da outra instancia, pelo username e grava na outra instancia com o id de l�
		#history_log_event_special_prosegur( $src_bug_id, BUG_ADD_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode($instancia) . ")");
		history_log_event_special( $src_bug_id, BUG_ADD_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode($instancia) . ")" );

		#insere dados na tabela da outra inst�ncia

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

	#exibe a mensagem caso encontre uma rela��o j� existente entre os dois casos, n�o verifica o tipo, somente os casos, visto que nao � necessario uma rela��o duplicada entre dois casos com tipos diferentes
	else  {
		$id = gpc_get_int('src_bug_id');
		echo '<script>alert(" '. lang_get('relexist') .' '. $id . ' ' . lang_get('andcase') .' '. $dest_bug .'." );</script>';
	}
	}

	#fun��o para excluir um relacionamento, deve ser exclu�do nos dois sistemas
	function del_relationship_system($id_deletar, $bug){
	
	$url = config_get('url_instancia_local'); #a url de acesso do sistema � enviado junto com os dados de acesso ao sistema


		#$id_deletar = gpc_get_string('id'); #recebe o id do registro que ser� apagado no banco
		#$bug = gpc_get_string('bug'); #recebe o numero do bug atual para redirecionar a p�gina

		#query que recebe o ID do caso da instancia relacionada
		$query = "SELECT * FROM mantis_bug_relationship_system_table where id=$id_deletar";
		$resultado = db_query($query);

		#verifica se existe essa rela��o
			$linha = db_fetch_array($resultado);
		if ($linha==0) {
			#a rela��o n�o existe ou pode ter dado algum problema com a consulta, algum caso espec�fico
			 $id = gpc_get_int('src_bug_id');
			echo '<script>alert("'. lang_get('relationship_notfound') .'" );</script>';

		}
		else {
		#pega os nomes dos sistemas dispon�veis no config_inc
		$sistema = config_get('sistemas_relaciona_enum_string');
		$sistema = explode(",", $sistema);

		#trata os dados da conexao da instancia relacionada
		$sistema_con = config_get('string_conexao_sistemas'); #a variaavel contem na casa todos os dados de acesso ao banco
		$sistema_con = explode(',', $sistema_con); #recebe as conex�es existentes

		#verifica qual � o sistema que est� relacionado para pegar os dados da conex�o corretos
		$instancia = $linha['sistema_dest'];


	for ( $i = 0 ; $i <= sizeof($sistema) ; $i++ ) {
	
		#faz o tratamento dos dados da conex�o de acordo com o sistema que est� a rela��o
		if (strcasecmp(trim($sistema[$i]),trim($instancia))  == 0 ) {
			$string_conexao = explode(":", $sistema_con[$i]);
			$host = $string_conexao[0];
			$bd = $string_conexao[1];
			$user = $string_conexao[2];
			$pass = $string_conexao[3];
		}
}

			#trata o retorno para pegar somente o n�mero do bug
			$src_bug_link = config_get('url_instancia_local') . "view.php?id=" . $bug; #link completo do bug local para ser inserido na tabela destino  #link completo do bug local para ser exclu�do na tabela destino
			#trata o link para receber o id do bug da instancia destino que est� em forma de link

			$id_src = explode("=" , $linha['id_dest']);
			$id_src = $id_src[1];


			#---- inicio da grava��o do historico no bug local
			#faz a valida��o do tipo do relacionamento, de acordo com o tipo � gravado 1,2 ou 3
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
			#faz a valida��o do tipo do relacionamento, de acordo com o tipo � gravado 1,2 ou 3
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
				

			#caso a rela��o exista ela � exclu�da local e na instancia da rela��o
			#query que deleta local pelo ID
			$query ="DELETE FROM mantis_bug_relationship_system_table WHERE id=$id_deletar";
			db_query($query);
			
			#chama a fun��o do mantis que grava na tabela local
			//criar uma fun��o melhor que pega o usu�rio da outra instancia, pelo username e grava na outra instancia com o id de l�
			#history_log_event_special_prosegur( $src_bug_id, BUG_DEL_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode(trim($instancia)) . ")" );			
			history_log_event_special( $src_bug_id, BUG_DEL_RELATIONSHIP, $tipo, "000" . $dest_bug . " (" . utf8_decode(trim($instancia)) . ")" );			

			#conexao com o segundo banco de dados
			$conexao = mssql_connect($host,$user,$pass) or die ("N�o foi poss�vel conectar ao banco: $bd no servidor: $host");
			mssql_select_db($bd) or die ("N�o foi poss�vel selecionar o banco: $bd no servidor: $host"); #seleciona o banco de dados
	
			#query que deleta na instancia relacionada pelo id destino e src, visto que s� existe uma rela��o entre dois casos
			$query ="DELETE FROM mantis_bug_relationship_system_table WHERE id_source=$id_src AND id_dest='$src_bug_link'";
			mssql_query($query, $conexao);// or die ("N�o foi poss�vel executar a consulta para apagar a rela��o na inst�ncia destino");

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
	
	#fun��o para inserir no hist�rico do caso da inst�ncia relacionada quando � inserido e quando � removido uma rela��o
	#fun��o copiada e adaptada do pr�prio mantis
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
		mssql_query($query, $id_conexao) or die ("N�o foi poss�vel executar a consulta para gravar no historico da inst�ncia destino");
	}
	
	#fun��o que retorna o status e gravidade de um caso em uma inst�ncia, passar a inst�ncia caso seja outra e n�o informar a inst�ncia caso seja a local, o status � um ENUM
	function return_bug_status_severity( $id, $instancia='') {
	
		if (strcasecmp($instancia, '')) { //instancia informada

		#pega os nomes dos sistemas dispon�veis no config_inc
		$sistema = config_get('sistemas_relaciona_enum_string');
		$sistema = explode(",", $sistema);

		#trata os dados da conexao da instancia relacionada
		$sistema_con = config_get('string_conexao_sistemas'); #a variaavel contem na casa todos os dados de acesso ao banco
		$sistema_con = explode(',', $sistema_con); #recebe as conex�es existentes

	for ( $i = 0 ; $i <= sizeof($sistema) ; $i++ ) {
	
		#faz o tratamento dos dados da conex�o de acordo com o sistema que est� a rela��o
		if (strcasecmp(trim($sistema[$i]),trim($instancia))  == 0 ) {
			$string_conexao = explode(":", $sistema_con[$i]);
			$host = $string_conexao[0];
			$bd = $string_conexao[1];
			$user = $string_conexao[2];
			$pass = $string_conexao[3];
		}
}

		#conexao com o segundo banco de dados
		$conexao = mssql_connect($host,$user,$pass) or die ("N�o foi poss�vel conectar ao banco: $bd no servidor: $host");
		mssql_select_db($bd) or die ("N�o foi poss�vel selecionar o banco"); #seleciona o banco de dados
	
		#query que retorna o status do bug
		$query = "SELECT status, severity FROM mantis_bug_table where id=$id";
		$resultado = mssql_query($query, $conexao) or die ("N�o foi poss�vel executar a consulta para apagar a rela��o na inst�ncia destino");
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
			#pega somente o n�mero
			$link = explode ('=', $id_dest);
			$instancia = $linha['sistema_dest'];
			//pega o status atual do caso e trata para ser atualizado na tabela local
			$dados = explode("," , return_bug_status_severity($link[1], trim($instancia))); //recebe o status do caso relacionado em n�mero
			$status = $dados[0];
			$severity = $dados[1];
			
			if ($status > 0) {  //verifica se a fun��o retornou algum status para o caso informado, caso contr�rio o caso pode ter sido apagado.
				#atualiza o status do bug destino na tabela local, atualiza o status desse bug em todos os relacionamentos que ele participa
				$lin = trim($link[1]);  //remove os espa�os
				db_query("UPDATE mantis_bug_relationship_system_table SET status = $status, severity = $severity WHERE REPLACE(id_dest, ' ', '') LIKE '%id=$lin'");	
			
			} else {
				echo '<script>alert("'. lang_get('thecase') .' '. trim($link[1]) .' '. lang_get('that_system') .' '. trim($instancia) .' '. lang_get('maybe_deleted') .'")</script>';
				}
			
		}

	}
	
	//fun��o que retorna os relat�rios envolvendo as demandas para serem exibidos na p�gina dos relat�rios
	function showReport() {
	
	}
	
	//funa��o que retorna as demandas em testes h� mais de 7 dias, informando tamb�m quem � o analista de testes que atuou nela, provavelmente era quem deveria ter alterado o status para testada
	function showInTest(){
	
	
	}
	
	//fun��o auxiliar que retorna quantos bugs est�o abertos para uma determinada demanda
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
	function showTotalBugs($id){
	#retorna os dados dos relacionamentos existentes para o caso aberto e iterando por sistema
	$query = "SELECT * FROM mantis_bug_relationship_system_table WHERE id_source=$id AND sistema_dest='Bugs' ORDER BY status ASC";
	
	$resultado = db_query($query);
	
		#exibe somente os sistemas que tem algum caso relacionado:
	if ($resultado->_numOfRows>0){  //verifica o n�mero de registros retornado da pesquisa
	
	// executa novamente a query para fazer a contagem dos resultados x abertos
	// � necess�rio executar novamente porque a fun��o do mantis que faz o fecth reinicia a variavel e mesmo criando uma c�pia n�o deu certo
	$open = 0;
	$openG = 0;
	$openP = 0;
	$j = 0;
	$contador = db_query($query); 
	while ($j = db_fetch_array($contador)) {
		#pega somente o n�mero
		$link = explode ('=', $j['id_dest']);
		$stat = explode("," , return_bug_status_severity($link[1], 'Bugs')); //recebe o status do caso relacionado em n�mero
		if ($stat[0] < 90) { //pega os abertos
		$open += 1;
			if ($stat[1] >= 60) { //pega os gravidade >= grande
				$openG += 1;
				//separa por status
				switch($stat[0]){
				//'10:novo,20:reaberto,30:admitido,40:confirmado,50:atribu�do,60:paralisado,80:resolvido,85:entregue,90:fechado';
					case 10:
					$novoG += 1;
					$vermelho .= trim($link[1]) .",";
					break;
					case 20:
					$reabertoG += 1;
					$vermelho .= trim($link[1]) .",";
					break;
					case 30:
					$admitidoG += 1;
					$vermelho .= trim($link[1]) .",";					
					break;
					case 40:
					$confirmadoG += 1;
					$vermelho .= trim($link[1]) .",";					
					break;
					case 50:
					$atribuidoG += 1;
					$vermelho .= trim($link[1]) .",";					
					break;
					case 60:
					$paralisadoG += 1;
					$vermelho .= trim($link[1]) .",";					
					break;
					case 80:
					$resolvidoG += 1;
					$vermelho .= trim($link[1]) .",";					
					break;
					case 85:
					$entregueG += 1;
					$vermelho .= trim($link[1]) .",";
					break;
				}					
					
			} else {
				$openP += 1;
				//separa por status
				switch($stat[0]){
				//'10:novo,20:reaberto,30:admitido,40:confirmado,50:atribu�do,60:paralisado,80:resolvido,85:entregue,90:fechado';
					case 10:
					$novoP += 1;
					$azul .= trim($link[1]) .",";
					break;
					case 20:
					$reabertoP += 1;
					$azul .= trim($link[1]) .",";
					break;
					case 30:
					$admitidoP += 1;
					$azul .= trim($link[1]) .",";
					break;
					case 40:
					$confirmadoP += 1;
					$azul .= trim($link[1]) .",";
					break;
					case 50:
					$atribuidoP += 1;
					$azul .= trim($link[1]) .",";
					break;
					case 60:
					$paralisadoP += 1;
					$azul .= trim($link[1]) .",";
					break;
					case 80:
					$resolvidoP += 1;
					$azul .= trim($link[1]) .",";
					break;
					case 85:
					$entregueP += 1;
					$azul .= trim($link[1]) .",";
					break;
				}
			}
		}
	}
	}
	if ($openG < 1) {
		$openG = 0;
		}
	if ($openP < 1) {
		$openP = 0;
		}
	
	$statusToolG = array("Novo: " => $novoG, "Reaberto: " => $reabertoG, "Admitido: " => $admitidoG, "Atribuído: " => $atribuidoG, "Paralisado: " => $paralisadoG, "Resolvido: " => $resolvidoG, "Entregue: " => $entregueG);//.','.$reabertoG.','.$admitidoG.','.$confirmadoG.','.$atribuidoG.','.$paralisadoG.','.$resolvidoG.','.$entregueG;
	$statusToolP =  array("Novo: " => $novoP,  "Reaberto: " => $reabertoP, "Admitido: " => $admitidoP, "Atribuído: " => $atribuidoP, "Paralisado: " => $paralisadoP, "Resolvido: " => $resolvidoP, "Entregue: " => $entregueP);
	$total = $resultado->_numOfRows;
	$links = substr($vermelho, 0, -1) . ":" . substr($azul, 0, -1);
	$resultado = array($openG, $openP, $statusToolG, $statusToolP, $links);
	return $resultado;
	
	}
	
	#fun��o que retorna os dados dos casos relacionados (bugs pendentes) passar o array com os ids dos casos e a instancia e o tipo se � bugs cr�ticos ou demais casos
	function return_bugs_relatioship_filter( $id, $instancia='') {
	
	$cor = "#dfdfdf";
	
		if (strcasecmp($instancia, '')) { //instancia informada

		#pega os nomes dos sistemas dispon�veis no config_inc
		$sistema = config_get('sistemas_relaciona_enum_string');
		$sistema = explode(",", $sistema);

		#trata os dados da conexao da instancia relacionada
		$sistema_con = config_get('string_conexao_sistemas'); #a variaavel contem na casa todos os dados de acesso ao banco
		$sistema_con = explode(',', $sistema_con); #recebe as conex�es existentes
		
		//recebe os nomes dos status da instancia de bugs
		$string_st =  config_get('status_enum_string_Bugs'); //trata o status para o nome
		
		//recebe a string de severidade
		$string_sev = config_get('severity_enum_string_Bugs');

	for ( $i = 0 ; $i <= sizeof($sistema) ; $i++ ) {
	
		#faz o tratamento dos dados da conex�o de acordo com o sistema que est� a rela��o
		if (strcasecmp(trim($sistema[$i]),trim($instancia))  == 0 ) {
			$string_conexao = explode(":", $sistema_con[$i]);
			$host = $string_conexao[0];
			$bd = $string_conexao[1];
			$user = $string_conexao[2];
			$pass = $string_conexao[3];
		}
}

		#conexao com o segundo banco de dados
		$conexao = mssql_connect($host,$user,$pass) or die ("N�o foi poss�vel conectar ao banco: $bd no servidor: $host");
		mssql_select_db($bd) or die ("N�o foi poss�vel selecionar o banco"); #seleciona o banco de dados

		$ids = explode(":", $id);
		$vermelho = trim($ids[0]);  //armazena os ids dos bugs cr�ticos separados por v�rgula
		$azul = trim($ids[1]); //armazena os ids dos demais bugs separados por v�rgula
		
		echo '<img class="btn_fechar" src="images/delete.png" onclick="javascript:acao(\'div'. $id .'\',\'\',\'hide\')">';
		if (empty($vermelho)) { //verifica se possui conte�do na vari�vel para ent�o fazer a consulta dos casos no banco
			//se n�o possuir registros, entra aqui
		} else {  //cont�m registros
		#imprime o cabe�alho do grid
		echo '<table border="0" cellpadding="0" cellspacing="1">';
		echo '<tr><center><b><u>'. utf8_encode("Bugs Cr�ticos:") .'</u></b></center></tr>';
		//echo '(Total: '. $contador->_numOfRows .'|Abertos: '. $open .')';
		echo '<tr><td class="row-1_tittle" bgcolor='.$cor.' width="5%">'. utf8_encode("N�mero").'</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="7%">Status</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="7%">Gravidade</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="15%">Projeto</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="66%">Resumo</td></tr>';		
	
		foreach (explode(",", $vermelho) as $t_id){ //pega os bugs cr�ticos
		$query = "SELECT bugs.id, bugs.severity, bugs.status, bugs.project_id, pro.name project_name, bugs.summary
					FROM mantis_bug_table bugs 
					INNER JOIN mantis_project_table pro ON bugs.project_id = pro.id
					INNER JOIN mantis_bug_text_table txt ON bugs.bug_text_id = txt.id
					where bugs.id=$t_id";
		$resultado = mssql_query($query, $conexao) or die ("N�o foi poss�vel executar a consulta para exibir a rela��o na inst�ncia destino");
		$resultado = mssql_fetch_array($resultado);
		$st = $resultado['status'];
		$sev = $resultado['severity'];
		
		//tratamento da gravidade
		$severity = explode($sev . ':', $string_sev);
		$severity = explode(',' , $severity[1]);
		
		//trata o status		
		$status = explode($st . ':', $string_st);
		$status = explode(',' , $status[1]);
		
		//echo  "ID: " . $resultado['id'] . '<br>' . "Status: " . $status . '<br>' . "Gravidade: " . $severity . '<br>';	
				echo '<tr><td class="row-1" bgcolor="'.$cor.'"><a href="../bugs/view.php?id='. $resultado['id'] .'" target="_blank">'. $resultado['id'] .'</a></td>';
				echo '<td class="row-1" bgcolor="'.$cor.'">' . $status[0] .'</td>';
				echo '<td class="row-1" bgcolor="'.$cor.'">' . $severity[0] .'</td>';					
				echo '<td class="row-1" bgcolor="'.$cor.'">' . utf8_encode($resultado['project_name']) .'</td>';
				echo '<td class="row-1" bgcolor="'.$cor.'">' . utf8_encode($resultado ['summary']) .'</td></tr>';
		}  //fim do foreach dos bugs cr�ticos
		echo '</tr></table>';
		} //fim do else de verifica��o de conte�do

		if (empty($azul)) { //verifica se possui conte�do na vari�vel para ent�o fazer a consulta dos casos no banco
			//se n�o possuir registros, entra aqui
		} else {  //cont�m registros
//		echo "<b>Demais Bugs: </b><br>";

		//finaliza a primeira tabela e inicia a segunda
		echo '<table border="0" cellpadding="0" cellspacing="1">';

		echo '<tr><b><u>Demais Bugs:</u></b></tr>';		
		//echo '(Total: '. $contador->_numOfRows .'|Abertos: '. $open .')';
		echo '<tr><td class="row-1_tittle" bgcolor='.$cor.' width="5%">'. utf8_encode("N�mero").'</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="7%">Status</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="7%">Gravidade</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="15%">Projeto</td>';
		echo '<td class="row-1_tittle" bgcolor='.$cor.' width="66%">Resumo</td></tr>';	
		
		foreach (explode(",", $azul) as $t_id){
		$query = "SELECT bugs.id, bugs.severity, bugs.status, bugs.project_id, pro.name project_name, bugs.summary
					FROM mantis_bug_table bugs 
					INNER JOIN mantis_project_table pro ON bugs.project_id = pro.id
					INNER JOIN mantis_bug_text_table txt ON bugs.bug_text_id = txt.id
					where bugs.id=$t_id";
		$resultado = mssql_query($query, $conexao) or die ("N�o foi poss�vel executar a consulta para exibir a rela��o na inst�ncia destino");
		$resultado = mssql_fetch_array($resultado);
		$st = $resultado['status'];
		$sev = $resultado['severity'];
		
		//tratamento da gravidade
		$severity = explode($sev . ':', $string_sev);
		$severity = explode(',' , $severity[1]);
		
		//trata o status		
		$status = explode($st . ':', $string_st);
		$status = explode(',' , $status[1]);		

				echo '<tr><td class="row-1" bgcolor='.$cor.'><a href="../bugs/view.php?id='. $resultado['id'] . ' " target="_blank" >'. $resultado['id'] .'</a></td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $status[0]	 .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . $severity[0] .'</td>';					
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($resultado['project_name']) .'</td>';
				echo '<td class="row-1" bgcolor='.$cor.'>' . utf8_encode($resultado ['summary']) .'</td></tr>';
		
		}  //fim do foreach dos bugs cr�ticos
			echo '</tr></table>';
		} //fim do else de verifica��o de conte�do		
		
		}  //fim do if da instancia informada
		else { //caso n�o informe instancia n�o faz nada
		}
	

	}
	
//fun��o que faz um count para saber a quantidade total de demandas de um projeto nos 5 status do filtro, para poder criar din�micamente os campos de data
function count_register($project_id, $status){
	
	$query = "SELECT name from mantis_project_table where id = $project_id";
	$resultado = db_query($query);  //executa a query
	$name = db_fetch_array($resultado); //trata os dados e pega o name
	$name = $name['name'];
	
	$query = "SELECT b.id
			FROM mantis_bug_table as b INNER JOIN mantis_project_table as p ON p.id = b.project_id
			where p.enabled = 1 AND b.status IN ($status) AND p.name like '%$name%'";
			
	$res = exec_query("Demandas", $query);
	while ($controle = mssql_fetch_array($res)) {
		$size++;
	}
	
	return $size;
}

//fun��o que faz o filtro din�mico de acordo com o que foi selecionado na tela de cadastro de tarefas din�micas e preenche o select
function integration($sistema, $project_id, $status, $users = 0) { //users n � obrigat�rio, se for enviado filtra por usu�rio relator

	//project_id recebe o id da inst�ncia tarefas, preciso pegar o nome do projeto no tarefas e fazer um like na outra inst�ncia para trazer todos os ids dos projetos - f�brica ou n�o para poder usar os ids nas querys
	$query = "SELECT name from mantis_project_table where id = $project_id";
	$resultado = db_query($query);  //executa a query
	$name = db_fetch_array($resultado); //trata os dados e pega o name
	$name = $name['name'];

	if ($sistema == 10) {
		$sistema = "Demandas";
		$query1 = "SELECT DISTINCT b.id as id_caso, b.summary as summary
				FROM mantis_bug_table as b INNER JOIN mantis_project_table as p ON p.id = b.project_id
				where p.enabled = 1 AND b.status IN ($status) AND p.name like '%$name%'";
			
		if ($users <> 0) {
			$query1 .= "AND b.reporter_id IN ($users)";	
		}
		
		$query1 .= " ORDER BY id_caso";
				
				
	} else if ($sistema == 50) {
			$sistema = "Bugs";
			$name = utf8_decode($name); //necess�rio fazer uma convers�o utf8 para funcionar a compara��o com o mantis de bugs
			$query1 = "SELECT DISTINCT b.id as id_caso, b.summary as summary
					FROM mantis_bug_table as b INNER JOIN mantis_project_table as p ON p.id = b.project_id
					where p.enabled = 1 AND b.status IN ($status) AND p.name like '%$name%'";
			
			if ($users <> 0) {
				$query1 .= "AND b.reporter_id IN ($users)";	
			}
			
			$query1 .= " ORDER BY id_caso";
		}

	//tamanho do select � din�mico, se houver mais de 5 registros limita em 5, se tiver menos mostra o tamanho ajustado
	$res = exec_query($sistema, $query1);
	while ($controle = mssql_fetch_array($res)) {
		$size++;
	}
	if ($size > 7) {
		$size = 7;
	}
	$count1 = 0;
	echo '<select class="select_bugs" name="casos[]" id="casos[]" onchange="javascript:show_date_number(this)" SIZE="'. $size .'" MULTIPLE >';
	$res1 = exec_query($sistema, $query1); //retorna todas as demandas em testes e em contru��o associadas ao id do projeto
		//se for bug preciso fazer o utf_decode, demandas n�o precisa
	if (strstr($sistema, "Demandas")) {
		while ($linha1 = mssql_fetch_array($res1)) {
		$count1++;
					
				//verifica se o texto � maior do que 100 caracteres, se for insere ... no final e exibe somente os 97 primeiros
				if (strlen(trim($linha1['summary'])) > 107) {
					echo '<option value= "'.trim($linha1['id_caso']).'">';
					echo $linha1['id_caso'] .':'. substr(trim($linha1['summary']), 0, 103) .'...';
					echo '</option>';	
				} else {
					
					echo '<option value= "'.trim($linha1['id_caso']).'">';
					echo $linha1['id_caso'] .':' . trim($linha1['summary']);
					echo '</option>';	
				}
		
		}
	} else {
		while ($linha1 = mssql_fetch_array($res1)) {
			$count1++;
			
			//verifica se o texto � maior do que 100 caracteres, se for insere ... no final e exibe somente os 97 primeiros
			if (strlen(trim($linha1['summary'])) > 97) {
				echo '<option value= "'.trim($linha1['id_caso']).'">';
				echo $linha1['id_caso'] .':'. utf8_encode(substr(trim($linha1['summary']), 0, 94)) .'...';
				echo '</option>';	
			} else {
				
				echo '<option value= "'.trim($linha1['id_caso']).'">';
				echo $linha1['id_caso'] .':' . utf8_encode(trim($linha1['summary']));
				echo '</option>';	
			}
			
		}		
	}
		echo '</select>';
	echo "<br/>". $count1 . " Registro(s)";
			
	return $count1;
}

function exec_query($instancia='', $query) {  //executa uma query em uma determinada inst�ncia j� configurada no config_inc.php e retorna o resultado da query, se n�o informar a query, executa na instancia local
	
	if (strcasecmp($instancia, '')) { //instancia informada
		
		#pega os nomes dos sistemas dispon�veis no config_inc
		$sistema = config_get('sistemas_relaciona_enum_string');
		$sistema = explode(",", $sistema);
		
		#trata os dados da conexao da instancia relacionada
		$sistema_con = config_get('string_conexao_sistemas'); #a variaavel contem na casa todos os dados de acesso ao banco
		$sistema_con = explode(',', $sistema_con); #recebe as conex�es existentes
		
		for ( $i = 0 ; $i <= sizeof($sistema) ; $i++ ) {
			
			#faz o tratamento dos dados da conex�o de acordo com o sistema que est� a rela��o
			if (strcasecmp(trim($sistema[$i]),trim($instancia))  == 0 ) {
				$string_conexao = explode(":", $sistema_con[$i]);
				$host = $string_conexao[0];
				$bd = $string_conexao[1];
				$user1 = $string_conexao[2];
				$pass = $string_conexao[3];
			}
		}
		
		#conexao com o segundo banco de dados
		$conexao = mssql_connect($host,$user1,$pass) or die ("N�o foi poss�vel conectar ao banco: $bd no servidor: $host");
		mssql_select_db($bd) or die ("N�o foi poss�vel selecionar o banco4"); #seleciona o banco de dados
		
		$resultado = mssql_query($query, $conexao) or die ("N�o foi poss�vel executar a consulta ao banco: $bd no servidor: $host");
		return $resultado;
		
	}
	else { //instancia n�o informada, executa na instancia local, tem q tratar os resulados com: db_fetch_array
		
		$resultado = db_query($query);
		return $resultado;
		
	}
	
}

# retorna todos os usu�rios de outra inst�ncia (bugs/demandas) com um n�vel de acesso = que o informado no projeto informado/selecionado
# vari�vel $show se for enviada como 1, j� exibe o select na tela, se n�o for enviada retorna o array de usu�rios
function user_project_permiss_open_inst( $p_inst, $p_project = ALL_PROJECTS, $show = 0 ) {
	$b_level = 25; //perfil m�nimo que pode abrir bugs, pegar o valor no sistema
	$d_level = 25; //perfil m�nimo que pode abrir demandas, pegar o valor no sistema
	$t_project = db_prepare_int( $p_project );
	
	//$t_project recebe o id da inst�ncia tarefas, preciso pegar o nome do projeto no tarefas e fazer um like na outra inst�ncia para trazer todos os ids dos projetos - f�brica ou n�o para poder usar os ids nas querys
	$query = "SELECT name from mantis_project_table where id = $t_project";
	$resultado = db_query($query);  //executa a query
	$name = db_fetch_array($resultado); //trata os dados e pega o name
	$name = $name['name'];

	if ($p_inst == 10) {
		$sistema = "Demandas";
		//retornar todos os usu�rios que est�o no projeto informado com o perfil >= relator e os adminstradores na tabela usu�rio
		$query1 = "SELECT DISTINCT u.id, u.realname
				FROM mantis_user_table AS u 
				LEFT JOIN mantis_project_user_list_table AS pl ON u.id = pl.user_id
				WHERE pl.access_level >= $d_level AND u.enabled = 1 AND pl.project_id IN (
				SELECT DISTINCT p.id
				FROM mantis_project_table as p INNER JOIN mantis_bug_table as b ON p.id = b.project_id
				WHERE p.enabled = 1 AND p.name like '%$name%') OR u.access_level = 90 AND u.enabled = 1";
				
				//esse c�digo foi duplicado aqui por causa da necessidade de fazer utf8_decode para bugs e n�o para demandas		
				$result = exec_query($sistema, $query1);
		
				# Get the list of users
				$t_users = array();
				while ($user = mssql_fetch_array($result)) {
					//projeto informado
					$t_users[] = $user;
				}
		
	} else if ($p_inst == 50) {
			$sistema = "Bugs";
			$name = utf8_decode($name); //necess�rio fazer uma convers�o utf8 para funcionar a compara��o com o mantis de bugs
			//retornar todos os usu�rios que est�o no projeto informado com o perfil >= relator e os adminstradores na tabela usu�rio
			$query1 = "SELECT DISTINCT u.id, u.realname
					FROM mantis_user_table AS u 
					LEFT JOIN mantis_project_user_list_table AS pl ON u.id = pl.user_id
					WHERE pl.access_level >= $b_level AND u.enabled = 1 AND pl.project_id IN (
					SELECT DISTINCT p.id
					FROM mantis_project_table as p INNER JOIN mantis_bug_table as b ON p.id = b.project_id
					WHERE p.enabled = 1 AND p.name like '%$name%') OR u.access_level = 90 AND u.enabled = 1";
			
			//esse c�digo foi duplicado aqui por causa da necessidade de fazer utf8_encode para bugs e n�o para demandas		
			$result = exec_query($sistema, $query1);
			
			# Get the list of users
			$t_users = array();
			while ($user = mssql_fetch_array($result)) {
				//projeto informado
				$user['realname'] = utf8_encode($user['realname']);
				$t_users[] = $user;
			}
		}

	
	if ($show > 0) {
		
		//ordena o array pelo nome para ser exibido em ordem alfab�tica
		foreach ($t_users as $key => $row) {
			$cont[$key] = $row['realname'];	
		}
		array_multisort($cont, SORT_ASC, $t_users);
		
		echo '<b>';
		echo lang_get('by_reporter');
		echo '</b><br>';
		echo '<select multiple="true" class="selType" name="handlerFilter[]" id="handlerFilter[]">';
		for ($i = 0; $i < count($t_users); $i++ ) {
			$selected = 'selected';
			echo '<option value="'. $t_users[$i]['id'] . '" '. $selected .'>';
			echo $t_users[$i]['realname'];
			echo '</option>';
		}
		echo '</select>';
		
	} else {
		
		return $t_users;
	}
}


?>