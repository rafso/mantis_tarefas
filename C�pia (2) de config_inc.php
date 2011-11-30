<?php
	$g_hostname = "Driver={SQL Server};SERVER=corps031;DATABASE=db_demandas;UID=demandas;PWD=12demandasfsw;";
	$g_db_type = "odbc_mssql";
	$g_database_name = 'db_demandas';
	$g_db_username = 'demandas';
	$g_db_password = '12demandasfsw';


	$g_send_reset_password	= OFF;


	#############################
	# Mantis Email Settings
	#############################

	# --- email variables -------------
	$g_administrator_email	= 'fabrica.software@br.prosegur.com';
	$g_webmaster_email		= 'fabrica.software@br.prosegur.com';

	# the 'From: ' field in emails
	$g_from_email			= 'aviso.prosegur@br.prosegur.com';
	$g_smtp_username = 'acessoprosegur';
	$g_smtp_password = 'acessopro';

	# the return address for bounced mail
	$g_return_path_email	= '';

	# allow email notification
	#  note that if this is disabled, sign-up and password reset messages will
	#  not be sent.
	$g_enable_email_notification	= ON;

	# Whether user's should receive emails for their own actions
	$g_email_receive_own	= ON;

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

    #e-mail para anotações adicionadas
    $g_notify_flags['bugnote']	= array('reporter'	=> ON,
									'handler'	=> ON,
									'monitor'	=> ON,
									'threshold_min'	=> 90,
									'bugnotes'	=> ON);

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
	#$g_default_language		= 'portuguese_brazil';
    $g_default_language		= 'portuguese_brazil';

	# Fallback for automatic language selection
	#$g_fallback_language	= 'portuguese_brazil';
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
	$g_absolute_path_default_upload_folder = 'c:\xampp\arquivos_mantis/';

	###############################
	# Mantis Display Settings
	###############################

	# --- sitewide variables ----------
	$g_window_title			= '';	 # browser window title
	$g_page_title			= '';	 # title at top of html page (empty by default, since there is a logo now)

	# --- footer menu -----------------
	# Display another instance of the menu at the bottom.  The top menu will still remain.
	$g_show_footer_menu		= OFF;


	# --- Queries --------------------
	# Shows the total number/unique number of queries executed to serve the page.
	$g_show_queries_count	= OFF;

	# Shows the list of all queries that are executed in chronological order from top
	# to bottom.  This option is only effective when $g_show_queries_count is ON.
	# WARNING: Potential security hazard.  Only turn this on when you really
	# need it (for debugging/profiling)
	$g_show_queries_list	= OFF;

	################################
	# Mantis Bug History Settings
	################################

	# --- bug history visible by default when you view a bug ----
	# change to ON or OFF
	$g_history_default_visible	= ON;

	# --- bug history ordering ----
	# change to ASC or DESC
	$g_history_order		= 'ASC';

	# --- advanced views --------------
	# BOTH, SIMPLE_ONLY, ADVANCED_ONLY
	$g_show_report			= SIMPLE_ONLY;
	$g_show_update			= SIMPLE_ONLY;
	$g_show_view			= BOTH;

	# Default bug severity when reporting a new bug
	$g_default_bug_severity = TEXT;


	###########################
	# Mantis Enum Strings
	###########################

	# --- enum strings ----------------
	# status from $g_status_index-1 to 79 are used for the onboard customization (if enabled)
	# directly use Mantis to edit them.
	# $g_access_levels_enum_string		= '10:viewer,25:reporter,40:updater,55:developer,70:manager,90:administrator';
	$g_access_levels_enum_string		= '10:viewer,25:reporter,55:developer,70:manager,90:administrator';
	$g_project_status_enum_string		= '10:development,30:release,50:stable,70:obsolete';
	$g_project_view_state_enum_string	= '10:public,50:private';
	$g_view_state_enum_string			= '10:public,50:private';

	$g_priority_enum_string				= '10:none,20:low,30:normal,40:high,50:urgent,60:immediate';
	$g_severity_enum_string				= '10:feature,20:trivial,30:text,40:tweak,50:minor';
	$g_reproducibility_enum_string		= '10:always,30:sometimes,50:random,70:have not tried,90:unable to duplicate,100:N/A';
	//$g_reproducibility_enum_string		= '100:N/A';
	$g_status_enum_string				= '10:new,20:reenviado,30:acknowledged,40:confirmed,50:assigned,70:testes,80:resolved,85:recusado,90:closed';
	  # @@@ for documentation, the values in this list are also used to define variables in the language files
	  #  (e.g., $s_new_bug_title referenced in bug_change_status_page.php )
	  # Embedded spaces are converted to underscores (e.g., "working on" references $s_working_on_bug_title).
	  # they are also expected to be english names for the states
	$g_resolution_enum_string			= '10:open,20:fixed,30:reopened,80:suspended,90:wont fix';
	$g_projection_enum_string			= '10:none,30:tweak,50:minor fix,70:major rework,90:redesign';
	$g_eta_enum_string					= '10:none,20:< 1 day,30:2-3 days,40:< 1 week,50:< 1 month,60:> 1 month';
	$g_sponsorship_enum_string          = '0:Unpaid,1:Requested,2:Paid';



	#Adicionado para habilitar os relatórios gráficos
	$g_use_jpgraph			= ON;
	$g_jpgraph_path			= '.' . DIRECTORY_SEPARATOR . 'jpgraph\src' . DIRECTORY_SEPARATOR;   # dont forget the ending slash!

	# what width is used to scale the graphs.
	$g_graph_window_width = 1600;
	# bar graph aspect ration (height / width)
	$g_graph_bar_aspect = 0.5;

    	#####################
	# Mostra os bugs visitados recentemente
	#####################

	# Whether to show the most recently visited issues or not.  At the moment we always track them even if this flag is off.
	$g_recently_visited = ON;

	# The maximum number of issues to keep in the recently visited list.
	$g_recently_visited_count = 15;
	$g_status_colors		= array( 'new'			=> '#ffa0a0', # red,
									 'feedback'		=> '#ff50a8', # purple
									 'acknowledged'	=> '#ffd850', # orange
									 'confirmed'	=> '#ffffb0', # yellow
									 'assigned'		=> '#c8c8ff', # blue
									 'resolved'		=> '#ccee88', # buish-green
									 'closed'		=> '#e8e8e8', # light gray
									 'reenviado'	=> '#eec0ee',
									 'recusado'		=> '#ff3300',
									 'testes'       => '#c8c880');

									 	# Boxes to be shown and their order
	# A box that is not to be shown can have its value set to 0
	$g_my_view_boxes = array (
		'assigned'      => '1',
		'unassigned'    => '2',
		'reported'      => '3',
		'resolved'      => '4',
		'recent_mod'	=> '5',
		'monitored'		=> '6',
		'feedback'		=> '0',
		'verify'		=> '0'
	);


	# allow the use of Javascript?
	$g_use_javascript		= ON;


	######################
	# Habilita o relacionamento entre os bugs
	######################

	# Enable support for bug relationships where a bug can be a related, dependent on, or duplicate of another.
	# See relationship_api.php for more details.
	$g_enable_relationship = ON;
	# Enable relationship graphs support.
	$g_relationship_graph_enable		= ON;
	# Default dependency orientation. If you have issues with lots of childs
	# or parents, leave as 'horizontal', otherwise, if you have lots of
	# "chained" issue dependencies, change to 'vertical'.
	$g_relationship_graph_orientation	= 'vertical';
		# If set to ON, clicking on an issue on the relationship graph will open
	# the bug view page for that issue, otherwise, will navigate to the
	# relationship graph for that issue.
	$g_relationship_graph_view_on_click	= ON;


	# --- account pref defaults ------- necessário configurar para que todos recebam e-mail ao alterar o status (e-mail_on_status=ON)
	$g_default_advanced_report		= OFF;
	$g_default_advanced_view		= OFF;
	$g_default_advanced_update		= OFF;
	$g_default_refresh_delay		= 30;    # in minutes
	$g_default_redirect_delay		= 2;     # in seconds
	$g_default_bugnote_order		= 'ASC';
	$g_default_email_on_new			= ON;
	$g_default_email_on_assigned	= ON;
	$g_default_email_on_feedback	= ON;
	$g_default_email_on_resolved	= ON;
	$g_default_email_on_closed		= ON;
	$g_default_email_on_reopened	= ON;
	$g_default_email_on_bugnote		= ON;
	$g_default_email_on_status		= ON; # @@@ Unused
	$g_default_email_on_priority	= 0; # @@@ Unused
	$g_default_email_on_new_minimum_severity		= OFF; # 'any'
	$g_default_email_on_assigned_minimum_severity	= OFF; # 'any'
	$g_default_email_on_feedback_minimum_severity	= OFF; # 'any'
	$g_default_email_on_resolved_minimum_severity	= OFF; # 'any'
	$g_default_email_on_closed_minimum_severity		= OFF; # 'any'
	$g_default_email_on_reopened_minimum_severity	= OFF; # 'any'
	$g_default_email_on_bugnote_minimum_severity	= OFF; # 'any'
	$g_default_email_on_status_minimum_severity		= OFF; # 'any'
	$g_default_email_on_priority_minimum_severity	= OFF; # @@@ Unused
	$g_default_email_bugnote_limit					= 0;
	
	
	
	#####################
	# Time tracking
	#####################

	# Turn on Time Tracking accounting
	$g_time_tracking_enabled = ON;

	# A billing sums
	$g_time_tracking_with_billing = ON;

	# Stop watch to build time tracking field
	$g_time_tracking_stopwatch = ON;

	# access level required to view time tracking information
	$g_time_tracking_view_threshold = DEVELOPER;

	# access level required to add/edit time tracking information
	$g_time_tracking_edit_threshold = DEVELOPER;

	# access level required to run reports
	$g_time_tracking_reporting_threshold = MANAGER;

	#allow time tracking to be recorded without a bugnote
	$g_time_tracking_without_note = ON;
	
		$g_display_errors = array(
		E_WARNING => 'none',
		E_NOTICE => 'none',
		E_USER_ERROR => 'none',
		E_USER_WARNING => 'none',
		E_USER_NOTICE => 'none'
	);
	
	
	#nível de acesso mínimo para ver os relacionamentos, ideal para não exibir para o cliente: , VIEWER, REPORTER, DEVELOPER, ADMINISTRATOR, etc
	//bugs
	$g_set_relationship_systems_Bugs_view = DEVELOPER; //quem pode visualizar as demandas relacionadas ao bug
	$g_set_relationship_systems_Bugs_add = DEVELOPER; //quem pode relacionar uma demanda a esse bug	
	$g_set_relationship_systems_Bugs_del = DEVELOPER; //quem pode deletar uma relação de demanda com esse bug	
	//duvidas
	$g_set_relationship_systems_Dúvidas_view = VIEWER;  //quem pode visualizar as duvidas relacionadas ao bug
	$g_set_relationship_systems_Dúvidas_add = REPORTER;  //quem pode relacionar uma dúvida a esse bug
	$g_set_relationship_systems_Dúvidas_del = REPORTER;  //quem pode deletar uma dúvida relacionada a esse bug	


	#habilita o relacionamento com outras instâncias do mantis
	$g_relationship_system_enable = ON;

	#nome do sistema que irá aparecer no listbox para ser selecionado
	#$g_sistemas_relaciona_enum_string = 'Mantis BugTracking,OtimizaÃ§Ã£oBD,Dúvidas,Demandas,Testes';
	$g_sistemas_relaciona_enum_string = 'Dúvidas,Bugs';

	#nome da instância local
	$g_instancia_local = 'Demandas';
	$g_url_instancia_local = '/demandas/';

	#dados de conexao com o banco de cada sistema,forma de uso: informar os dados 'servidor:banco:usuario:senha' de cada sistema separado por vírgula.
	$g_string_conexao_sistemas = 'corps031:db_dudas:dudas:fsw37dudas,corps031:db_bugs:bugs:fswbugs77';

	#dados de acesso web de cada sistema, necessário para ser montado o link direto ao caso, sem o HTTP://
	$g_url_sistemas = '/dudas/,/bugs/';
	
	#nomes dos status dos sistemas, funcionando somente em português, a variavel deve ser $g_status_enum_string_ + o nome do sistema exatamente como escrito a cima na sistemas_relaciona
	#Dúvidas
	$g_status_enum_string_Dúvidas = '10:novo,20:reenviado,50:atribuído,80:resolvido,85:Aguardando atualização,90:fechado';
	
	#Bugs
	$g_status_enum_string_Bugs = '10:novo,20:reaberto,30:admitido,40:confirmado,50:atribuído,60:paralisado,80:resolvido,85:entregue,90:fechado';
	
	
	# --- cookie prefix ---------------
	# Altera o prefixo do cookie, util para evitar a perda da sessão em instâncias diferentes, setar o nome da instância
	$g_cookie_prefix		= 'DEMANDAS';

?>