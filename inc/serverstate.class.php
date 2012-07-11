<?php


/**
* Serverstate
*
* @since 0.1
*/

class Serverstate
{


	/**
	* Pseudo-Konstruktor der Klasse
	*
	* @since   0.1
	* @change  0.1
	*/
	
	public static function instance()
	{
		new self();
	}
	
	
	/**
	* Konstruktor der Klasse
	*
	* @since   0.1
	* @change  0.1
	*/

	public function __construct()
	{
		/* Filter */
		if ( (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) or (defined('DOING_CRON') && DOING_CRON) or (defined('DOING_AJAX') && DOING_AJAX) or (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) ) {
			return;
		}
		
		/* BE only */
		if ( !is_admin() ) {
			return;
		}
		
		/* Hooks */
		add_action(
			'wpmu_new_blog',
			array(
				'Serverstate_Install',
				'init'
			)
		);
		add_action(
			'delete_blog',
			array(
				'Serverstate_Uninstall',
				'init'
			)
		);
		add_action(
			'wp_dashboard_setup',
			array(
				'Serverstate_Dashboard',
				'init'
			)
		);
		add_filter(
			'plugin_row_meta',
			array(
				__CLASS__,
				'add_meta_link'
			),
			10,
			2
		);
		add_filter(
			'plugin_action_links_' .SERVERSTATE_BASE,
			array(
				__CLASS__,
				'add_action_link'
			)
		);
	}


	/**
	* Hinzufügen der Meta-Links
	*
	* @since   0.1
	* @change  0.1
	*
	* @param   array   $input  Array mit Links
	* @param   string  $file   Name des Plugins
	* @return  array           Array mit erweitertem Link
	*/

	public static function add_meta_link($input, $file)
	{
		/* Restliche Plugins? */
		if ( $file !== SERVERSTATE_BASE ) {
			return $input;
		}
		
		return array_merge(
			$input,
			array(
				'<a href="http://wpcoder.de" target="_blank">Weitere Plugins des Autors</a>'
			)
		);
	}
	
	
	/**
	* Hinzufügen des Action-Links
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function add_action_link($input)
	{
		/* Rechte? */
		if ( !current_user_can('manage_options') ) {
			return $input;
		}
		
		/* Zusammenführen */
		return array_merge(
			$input,
			array(
				sprintf(
					'<a href="%s">%s</a>',
					add_query_arg(
						array(
							'edit' => 'serverstate_dashboard#serverstate_dashboard'
						),
						admin_url('/')
					),
					__('Settings')
				)
			)
		);
	}
}