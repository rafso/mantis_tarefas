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
	# $Id: relationship_mantis.php,v 0.1 2009-03-26 15:32:44 
	# --------------------------------------------------------

	# ======================================================================
	# Author: Raphael Soares' <rafsopd at gmail.com> BRAZIL
	# ======================================================================

	# esta página é chamada pela relationship_api.php
	
	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path . 'relationship_api.php' );
	
	#trata os dados enviados de acordo com o sistema que foi selecionado
	$url = explode('@', gpc_get_string('sistema')); #a url de acesso do sistema é enviado junto com os dados de acesso ao sistema
	$sistema = $url[0]; #a variaavel contem na casa 0 todos os dados de acesso ao banco e na casa 1 a url de acesso ao sistema
	$instancia = $url[2];
	$url = $url[1];
	$sistema = explode(':', $sistema);
	$host = $sistema[0];
	$bd = $sistema[1];
	$user = $sistema[2];
	$pass = $sistema[3];
	#os dados da conexao não estão sendo usado nesse arquivo, mas vão ser necessários quando for preciso atualizar a tabela da instancia que está sendo inserido o caso (id_dest)
	
	
	#organiza os dados enviados para relação
	$type = gpc_get_string('relacao');
	
	#verifica se o tipo é filho ou pai, para colocar da forma correta a relação na outra instancia
	if ($type === 'Ã© pai de'){
	$type_dest = 'Ã© filho de';
	}
	if ($type === 'Ã© filho de'){
	$type_dest = 'Ã© pai de';
	}
	
	$src_bug_id = gpc_get_int('src_bug_id'); #id do bug de origem - que será o destino da outra instancia
	$dest_bug =  gpc_get_int('dest_bug_id'); #id do bug destino - que será o origem da outra instancia
	$dest_bug_link = "http://" . $url . "view.php?id=" . $dest_bug; #link completo com id do bug destino
	$src_bug_link = "http://" . config_get('url_instancia_local') . "view.php?id=" . $src_bug_id; #link completo do bug local para ser inserido na tabela destino
	$instancia_local = config_get('instancia_local'); #nome da instancia local

	#conexao com o segundo banco de dados
	$conexao = mssql_connect($host,$user,$pass) or die ("Não foi possível conectar ao banco");
	mssql_select_db($bd) or die ("Não foi possível selecionar o banco"); #seleciona o banco de dados

	#retorna os dados do caso informado em outro sistema
	$query = "SELECT mantis_bug_table.status, mantis_project_table.name, summary FROM mantis_bug_table INNER JOIN mantis_project_table ON(mantis_bug_table.project_id = mantis_project_table.id) where mantis_bug_table.id=$dest_bug";

	#pega os dados do bug no outro banco e insere na tabela
	$resultado = mssql_query($query, $conexao) or die ("Não foi possível executar a consulta");
	
	while ($linha = mssql_fetch_array($resultado)) {
			$status = $linha['status'];
			$project = $linha['name'];
			$summary = $linha['summary'];
	}
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
	
	#insere dados na tabela da outra instância

	#retorna os dados do caso informado em outro sistema
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

	
	mssql_query($query, $conexao) or die ("Não foi possível executar a consulta");
	
	#redireciona para o bug que estava, em um segundo momento efetuar uma validação para somente redirecionar se o bug existir realmente, caso contrario emitir mensagem
	print_successful_redirect_to_bug($src_bug_id);
	
?>