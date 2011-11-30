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
	# $Id: delete_relacao_system.php,v 0.1 2009-03-30 15:32:44 
	# --------------------------------------------------------

	# ======================================================================
	# Author: Raphael Soares' <rafsopd at gmail.com> BRAZIL
	# ======================================================================

	# esta página é chamada pela relationship_api.php
	
	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path . 'relationship_api.php' );
	
	
	$id_deletar = gpc_get_string('id'); #recebe o id do registro que será apagado no banco
	$bug = gpc_get_string('bug'); #recebe o numero do bug atual para redirecionar a página
	
	#query que deleta local
	$query ="DELETE FROM mantis_bug_relationship_system_table WHERE id=$id_deletar";
	
	#query que recebe o ID do caso da instancia relacionada
	$query = "select id_dest from dbo.mantis_bug_relationship_system_table where id=68";
	
	#pergunta se o usuário tem certeza que deseja apagar o bug	
	helper_ensure_confirmed( lang_get( 'delete_relationship_sure_msg' ), lang_get( 'delete_relationship_button' ) );
	
	#executa a query
	db_query($query)
	
	#redireciona a página
	print_successful_redirect_to_bug($bug);
	
?>