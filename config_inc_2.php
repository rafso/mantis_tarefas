<?php
	$g_hostname = "Driver={SQL Server};SERVER=Corps010;DATABASE=db_mantis;UID=mantis;PWD=desenv;"; 
	$g_db_type = "odbc_mssql"; 
	$g_database_name = 'mantis';
	$g_db_username = 'mantis';
	$g_db_password = 'desenv';

	$g_send_reset_password	= OFF;


	#############################
	# Mantis Email Settings
	#############################

	# --- email variables -------------
	$g_administrator_email	= 'aviso.prosegur@br.prosegur.com';
	$g_webmaster_email		= 'aviso.prosegur@br.prosegur.com';

	# the 'From: ' field in emails
	$g_from_email			= 'aviso.prosegur@br.prosegur.com';
	$g_smtp_username = 'acessoprosegur';
	$g_smtp_password = 'acessopro';

	# the return address for bounced mail
	$g_return_path_email	= 'aviso.prosegur@br.prosegur.com';

	# allow email notification
	#  note that if this is disabled, sign-up and password reset messages will
	#  not be sent.
	$g_enable_email_notification	= ON;

	# Whether user's should receive emails for their own actions
	$g_email_receive_own	= OFF;

	# if ON, allow the user to omit an email field
	# note if you allow users to create their own accounts, they
	#  must specify an email at that point, no matter what the value
	#  of this option is.  Otherwise they wouldn't get their passwords.
	$g_allow_blank_email	= OFF;

	# select the method to mail by:
	# 0 - mail()
	# 1 - sendmail
	# 2 - SMTP
	$g_phpMailer_method		= 2;

	# This option allows you to use a remote SMTP host.  Must use the phpMailer script
	# Name of smtp host, needed for phpMailer, taken from php.ini
	$g_smtp_host			= 'smtp.prosegur.net.br';

	#############################
	# Mantis Version String
	#############################
	# --- version variables -----------
	$g_mantis_version		= '';
	$g_show_version		= OFF;

	# --- language settings -----------

	# If the language is set to 'auto', the actual
	# language is determined by the user agent (web browser)
	# language preference.
	$g_default_language		= 'portuguese_brazil';

	# Fallback for automatic language selection
	$g_fallback_language	= 'portuguese_brazil';


	###################################
	# Mantis File Upload Settings
	###################################

	# --- file upload settings --------
	# This is the master setting to disable *all* file uploading functionality
	#
	# If you want to allow file uploads, you must also make sure that they are
	#  enabled in php.  You may need to add 'file_uploads = TRUE' to your php.ini
	#
	# See also: $g_upload_project_file_threshold, $g_upload_bug_file_threshold,
	#   $g_allow_reporter_upload
	$g_allow_file_upload	= ON;

	# Upload destination: specify actual location in project settings
	# DISK, DATABASE, or FTP.
	$g_file_upload_method	= DISK;

	# Maximum file size that can be uploaded
	# Also check your PHP settings (default is usually 2MBs)
	$g_max_file_size		= 5000000; # 5 MB


	# absolute path to the default upload folder.  Requires trailing / or \
	$g_absolute_path_default_upload_folder = 'c:\inetpub\wwwroot\net01\mantis\arquivos/';

	###############################
	# Mantis Display Settings
	###############################

	# --- sitewide variables ----------
	$g_window_title			= 'Prosegur';	 # browser window title
	$g_page_title			= '';	 # title at top of html page (empty by default, since there is a logo now)

	# --- footer menu -----------------
	# Display another instance of the menu at the bottom.  The top menu will still remain.
	$g_show_footer_menu		= OFF;


	# --- Queries --------------------
	# Shows the total number/unique number of queries executed to serve the page.
	$g_show_queries_count	= OFF;

	################################
	# Mantis Bug History Settings
	################################

	# --- bug history visible by default when you view a bug ----
	# change to ON or OFF
	$g_history_default_visible	= ON;

	# --- bug history ordering ----
	# change to ASC or DESC
	$g_history_order		= 'ASC';




?>

