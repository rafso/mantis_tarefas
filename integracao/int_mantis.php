<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 *
 * Filename $RCSfile: int_mantis.php,v $
 *
 * @version $Revision: 1.11 $
 * @modified $Date: 2007/03/05 07:08:33 $ $Author: franciscom $
 *
 * @author Andreas Morsing
 *
 * Constants used throughout TestLink are defined within this file
 * they should be changed for your environment
 *
 * 20070304 - franciscom - 
 * 1. added an specialized version of checkBugID
 * 2. added new method checkBugID_existence()
 *
 *
 * 20070302 - BUGID 
 * Problems on getBugSummaryString($id), when DB is MS SQL
 * On MS-SQL fetch_array() does not returns numeric indexes, then
 * only choice is accessing my field name (IMHO better)
 *
 * Removed also DBNAME on Queries because causes problems with MS-SQL
 *
**/
/** Interface name */
define('BUG_INTERFACE_CLASSNAME',"mantisInterface");

class mantisInterface extends bugtrackingInterface
{
	//members to store the bugtracking information
	var $m_dbHost = BUG_TRACK_DB_HOST;
	var $m_dbName = BUG_TRACK_DB_NAME;
	var $m_dbUser = BUG_TRACK_DB_USER;
	var $m_dbPass = BUG_TRACK_DB_PASS;
	var $m_dbType = BUG_TRACK_DB_TYPE;
	var $m_showBugURL = BUG_TRACK_HREF;
	var $m_enterBugURL = BUG_TRACK_ENTER_BUG_HREF;
	
	/**
	 * Return the URL to the bugtracking page for viewing 
	 * the bug with the given id. 
	 *
	 * @param int id the bug id
	 * 
	 * @return string returns a complete URL to view the bug
	 **/
	function buildViewBugURL($id)
	{
		return $this->m_showBugURL.urlencode($id);
	}
	
	/**
	 * Returns the status of the bug with the given id
	 * this function is not directly called by TestLink. 
	 *
	 * @return string returns the status of the given bug (if found in the db), or false else
	 **/
	function getBugStatus($id)
	{
		if (!$this->isConnected())
			return false;

		$status = false;
		
		// 20070302 - {$this->m_dbName}.mantis_bug_table -> mantis_bug_table
		// Problems with MS-SQL
		$query = "SELECT status FROM mantis_bug_table WHERE id='" . $id."'";
		
		$result = $this->m_dbConnection->exec_query($query);
		if ($result)
		{
			$status = $this->m_dbConnection->fetch_array($result);
			if ($status)
			{
				$status = $status['status'];
			}	
			else
				$status = null;
		}
		return $status;
		
	}
		
	/**
	 * Returns the status in a readable form (HTML context) for the bug with the given id
	 *
	 * @param int id the bug id
	 * 
	 * @return string returns the status (in a readable form) of the given bug if the bug
	 * 		was found , else false
	 **/
	function getBugStatusString($id)
	{
		$status = $this->getBugStatus($id);
		
		$str = htmlspecialchars($id);
		//if the bug wasn't found the status is null and we simply display the bugID
		if ($status !== false)
		{
			//the status values depends on your mantis configuration at config_inc.php in $g_status_enum_string, 
			//below is the default:
			//'10:new,20:feedback,30:acknowledged,40:confirmed,50:assigned,80:resolved,90:closed'
			//strike through all bugs that have a resolved or closed status.. 
			if ($status == 80 || $status == 90)
				$str = "<del>" . $id . "</del>";
		}
		return $str;
	}
	/**
	 * Fetches the bug summary from the matnis db
	 *
	 * @param int id the bug id
	 * 
	 * @return string returns the bug summary if bug is found, else false
	 **/
	function getBugSummaryString($id)
	{
		if (!$this->isConnected())
			return false;

		$status = null;
		// 20070302 - {$this->m_dbName}.mantis_bug_table -> mantis_bug_table
		// Problems with MS-SQL
		$query = "SELECT summary FROM mantis_bug_table WHERE id='" . $id."'";
		
		$result = $this->m_dbConnection->exec_query($query);
		if ($result)
		{
			$summary = $this->m_dbConnection->fetch_array($result);

			// 20070302 - BUGID - on MS-SQL fetch_array() does not returns numeric indexes, then
			//                    only choice is accessing my field name (IMHO better)
			if ($summary)
				$summary = $summary['summary'];
			else
				$summary = null;
		}
		return $summary;
	}

  /**
	 * checks a bug id for validity  
	 * 
	 * @return bool returns true if the bugid has the right format, false else
	 **/
	function checkBugID($id)
	{
	  $status_ok=1;	
	  $ereg_forbidden_chars='[a-zA-Z,$-+]';
 		if (eregi($ereg_forbidden_chars, $id))
		{
			$status_ok=0;	
		} 	
    else 
    {
      $status_ok=(intval($id) > 0);	
    }
		return $status_ok;
	}	

  /**
	 * checks is bug id is present on BTS
	 * 
	 * @return bool 
	 **/
	function checkBugID_existence($id)
	{
	  $status_ok=0;	
		$query = "SELECT status FROM mantis_bug_table WHERE id='" . $id."'";
		$result = $this->m_dbConnection->exec_query($query);
		if ($result && ($this->m_dbConnection->num_rows($result) == 1) )
		{
      $status_ok=1;    
    }
		return $status_ok;
	}	
	

}
?>