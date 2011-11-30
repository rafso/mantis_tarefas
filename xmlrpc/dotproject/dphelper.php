<?php /* ID: dphelper.php 2007/04/10 12:46 weboholic */
function getMantisBugById( $bug_id ) {
	$bug = bug_get($bug_id);
	$bug_array = get_object_vars($bug);
	$bug_array['id'] = $bug_id;
	$bug_extra = addExtraBugData($bug_array);
	return $bug_extra;
}

function getMantisBugByProjectId( $project_id, $status = '' ) {
	$page_number = $per_page = $page_count = $bug_count = $custom_filter = null;
	$result = filter_get_bug_rows( $page_number,$per_page,$page_count,$bug_count,$custom_filter,$project_id );
	$bugs = array();
	foreach( $result as $r ) $bugs[] = addExtraBugData($r);
	if( count($bugs) == 0 ) return $project_id;
	return $bugs;
}

function addExtraBugData( $bug ) {
	$bug['project_name'] 		 							= project_get_name( $bug['project_id'] );
	if( $bug['reporter_id'] != '' ) $bug['reporter_name'] 	= user_get_field( $bug['reporter_id'],'username' );
	$bug['severity_name'] 		 							= get_enum_element( 'severity',$bug['severity'] );
	$bug['priority_name'] 	 	 							= get_enum_element( 'priority',$bug['priority'] );
	$bug['status_name'] 		 							= get_enum_element( 'status',$bug['status'] );
	$bug['reproducibility_name'] 							= get_enum_element( 'reproducibility',$bug['reproducibility'] );
	if( $bug['handler_id'] == '' ) $bug['handler_name'] 	= user_get_field( $bug['handler_id'],'username' );
	$bug['projection_name'] 	 							= get_enum_element( 'projection',$bug['projection'] );
	$bug['eta_name'] 			 							= get_enum_element( 'eta',$bug['eta'] );
	$bug['resolution_name'] 	 							= get_enum_element( 'resolution',$bug['resolution'] );
	$bug['description'] 		 							= bug_get_text_field( $bug['id'],'description' );
	return $bug;
}

function getMantisBugByProjectName( $project_name, $status = '' ) {
	$project_id = project_get_id_by_name( $project_name );
	if( $project_id == 0 ) return 0;
	return getMantisBugByProjectId( $project_id,$status );
}

function checkUserPermForProject( $uname,$pname ) {
	$uid = user_get_id_by_name( $uname );
	$pid = project_get_id_by_name( $pname );
	if( $pid == 0 ) return 0;
	if( in_array( $pid,user_get_accessible_projects($uid,true) ) ) {
		return 1;
	}
	return 2;
}

function getUserAccessLevel( $uname ) {
	return user_get_access_level( user_get_id_by_name( $uname ) );
}

function addEditProjectByName( $oldpname,$pname,$pdescr,$pstatus = 10 ) {
	$pid = project_get_id_by_name( $oldpname );
	if( $pid == 0 ) {
		project_create( $pname,$pdescr,$pstatus );
	} else {
		$row = project_get_row( $pid );
		project_update( $pid,$pname,$pdescr,$row['status'],$row['view_state'],$row['file_path'],$row['enabled'] );
	}
	return true;
}