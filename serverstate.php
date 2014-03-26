<?php
/*
Plugin Name: Serverstate
Description: Server Monitoring für WordPress. Dashboard-Widget mit Reaktionszeiten und Erreichbarkeitsmessungen der Website. Setzt einen Serverstate-Account voraus.
Author: Sergej M&uuml;ller
Author URI: http://wpcoder.de
Plugin URI: http://wordpress.org/plugins/serverstate/
Version: 0.5.1
*/


/* Quit */
defined('ABSPATH') OR exit;


/* Konstanten */
define('SERVERSTATE_FILE', __FILE__);
define('SERVERSTATE_BASE', plugin_basename(__FILE__));


/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'Serverstate',
		'instance'
	)
);
register_activation_hook(
	__FILE__,
	array(
		'Serverstate_Install',
		'init'
	)
);
register_uninstall_hook(
	__FILE__,
	array(
		'Serverstate_Uninstall',
		'init'
	)
);


/* Autoload Init */
spl_autoload_register('serverstate_autoload');

/* Autoload Funktion */
function serverstate_autoload($class) {
	if ( in_array($class, array('Serverstate', 'Serverstate_Dashboard', 'Serverstate_Install', 'Serverstate_Uninstall')) ) {
		require_once(
			sprintf(
				'%s/inc/%s.class.php',
				dirname(__FILE__),
				strtolower($class)
			)
		);
	}
}