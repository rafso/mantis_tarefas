<?php /* ID: dpserver.php 2007/04/10 12:46 weboholic */
require_once( 'dpconfig.php' );

# Convert this file into an XMLRPC server
$mantis_server = new PHPXMLRPCServer();
$mantis_server->addFunction( 'MantisRPC','MantisRPC' );
$mantis_server->startServer();

function MantisRPC( $args ) {	
	$args_count = $args->getNumParams();
	$username = $args->getParam(0);
	$password = $args->getParam(1);
	$function = $args->getParam(2);
	
	$func_args = array();
	for( $i = 3;$i < $args_count;$i++ ) {
		$temp = $args->getParam($i);
		$func_args[] = $temp->scalarval();
	}
	
	if( !MantisLogin( $username->scalarval(),$password->scalarval() ) ) return XMLRPCError( 'Invalid user and/or pass' );
	if( !in_array( $function->scalarval(),getRemoteFunctions() ) ) return XMLRPCError( 'Function not found in Remote List' );
	$result = call_user_func_array( $function->scalarval(),$func_args );
	
	// DO NOT MODIFY CHECK README
	// when logging plain text into mantis, after successful operations mantis resets the pass
					$uid = user_get_id_by_name($username->scalarval() ) ;
					$t_user_table	= config_get( 'mantis_user_table' );
					$query = "UPDATE ". $t_user_table ." SET password='". $password->scalarval() ."' WHERE id='". $uid ."'";
					db_query( $query );

	return new xmlrpcresp( processResult($result) );
}

function XMLRPCError( $msg ) {
	global $xmlrpcerruser;
	return new xmlrpcresp( 0,$xmlrpcerruser,$msg );
}

function MantisLogin( $username,$password ) {
	$offline_file = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR .'mantis_offline.php';
	if( file_exists($offline_file) ) return false;

	# if no user name supplied, then attempt to login as anonymous user.
	if( is_blank($username) ) {
		if( config_get( 'allow_anonymous_login' ) != 'ON' ) return false;
		$username = config_get( 'anonymous_account' );
		$password = null; # do not use password validation.
	}
	if( auth_attempt_script_login($username,$password) === false ) return false;
	
	return true;
}

function is_associative_array( $array ) {
	if( !is_array($array) || empty($array) ) return false;
	$keys = array_keys($array);
	return array_keys($keys) !== $keys;
}

function getRemoteFunctions() {
	$func = array();
	
	#mantis native core functions
	$func[] = 'user_get_accessible_projects';
	$func[] = 'user_get_access_level';
	$func[] = 'user_get_id_by_name';
	$func[] = 'bug_get';
	$func[] = 'filter_get_bug_rows';
	$func[] = 'project_get_id_by_name';
	$func[] = 'project_get_row';
	$func[] = 'project_create';
	$func[] = 'project_update';
	
	#mantis helper functions
	$func[] = 'getMantisBugByProjectId';
	$func[] = 'getMantisBugByProjectName';
	$func[] = 'getMantisBugById';
	$func[] = 'addExtraBugData';
	$func[] = 'checkUserPermForProject';
	$func[] = 'getUserAccessLevel';
	$func[] = 'addEditProjectByName';
	
	return $func;
}

function processResult( $result ) {	
	$ret = new xmlrpcval();
	if( is_object($result) ) $result = get_object_vars($result);	
	if( is_associative_array($result) ) {
		$ar = array();
		$keys = array_keys($result);
		foreach( $keys as $k ) {
			$tmp = new xmlrpcval( array( $k => new xmlrpcval($result[$k]) ),'struct' );	
			$ar[] = $tmp;			
		}
		$ret->addArray($ar);
	} elseif( is_array($result) ) {
		foreach( $result as $key => $value ) {
			if( !is_string($value) ) {
				$tmp = processResult($value);
			} else {
				$tmp = new xmlrpcval();
				$tmp->addScalar($value);
			}
			$result[$key] = $tmp;
		}
		$ret->addArray($result);
	} elseif( is_bool($result) ) {
		$ret->addScalar( $result,'boolean' );
	} else {
		$ret->addScalar($result);
	}
	
	return $ret;
}