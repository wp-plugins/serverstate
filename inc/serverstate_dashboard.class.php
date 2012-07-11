<?php


/**
* Serverstate_Dashboard
*
* @since 0.1
*/

class Serverstate_Dashboard
{
	
	
	/**
	* Installation auch für MU-Blog
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function init()
	{
		/* Filter */
		if ( !current_user_can('level_2') ) {
			return;
		}
		
		/* Version definieren */
		self::_define_version();

		/* Widget */
		wp_add_dashboard_widget(
			'serverstate_dashboard',
			'Serverstate',
			array(
				__CLASS__,
				'print_frontview'
			),
			array(
				__CLASS__,
				'print_backview'
			)
		);

		/* CSS laden */
		add_action(
			'admin_print_styles',
			array(
				__CLASS__,
				'add_style'
			)
		);

		/* JS laden */
		add_action(
			'admin_print_scripts',
			array(
				__CLASS__,
				'add_js'
			)
		);
	}
	
	
	/**
	* Ausgabe der Stylesheets
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function add_style()
	{
		/* CSS registrieren */
		wp_register_style(
			'serverstate',
			plugins_url('/css/dashboard.css', SERVERSTATE_FILE),
	  		array(),
	  		SERVERSTATE_VERSION
		);

	  	/* CSS ausgeben */
	  	wp_enqueue_style('serverstate');
	}


	/**
	* Ausgabe von JavaScript
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function add_js() {
		/* Registrieren */
		wp_register_script(
			'serverstate',
			plugins_url('/js/dashboard.js', SERVERSTATE_FILE),
			array(),
			SERVERSTATE_VERSION
		);
		wp_register_script(
			'google_jsapi',
			'https://www.google.com/jsapi',
			false
		);

		/* Einbinden */
		wp_enqueue_script('google_jsapi');
		wp_enqueue_script('serverstate');

		/* Übergeben */
		wp_localize_script(
			'serverstate',
			'serverstate',
			self::_get_stats()
		);
	}
	
	
	/**
	* Ausgabe der Frontseite
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function print_frontview()
	{ ?>
		<div id="serverstate_chart">
			<noscript>Zur Darstellung der Statistik wird JavaScript benötigt.</noscript>
		</div>
	<?php }


	/**
	* Ausgabe der Backseite
	*
	* @since   0.1
	* @change  0.1
	*/

	public static function print_backview()
	{
		/* Rechte */
		if ( !current_user_can('manage_options') ) {
			return;
		}
		
		/* Optionen */
		$options = wp_parse_args(
			get_option('serverstate'),
			array(
				'nickname'  => '',
				'password'  => '',
				'sensor_id' => ''
			)
		);
		
		/* Speichern */
		if ( !empty($_POST['serverstate']) && is_array($_POST['serverstate']) ) {
			/* Formular-Referer */
			check_admin_referer('_serverstate');
			
			/* Zuweisen */
			$input = $_POST['serverstate'];
			
			/* Benutzername */
			if ( !empty($input['nickname']) ) {
				$input['nickname'] = sanitize_text_field($input['nickname']);
			}
			
			/* Passwort */
			if ( !empty($input['password']) ) {
				if ( $input['password'] != $options['password'] ) {
					$input['password'] = md5(sanitize_text_field($input['password']));
				}
			}
			
			/* Sensor ID */
			if ( !empty($input['sensor_id']) ) {
				$input['sensor_id'] = intval($input['sensor_id']);
			}
			
			/* Refresh */
			$options = $input;
			
			/* Save */
			update_option(
				'serverstate',
				$options
			);

			/* Entleeren */
			delete_transient('serverstate');
		}

		

		/* Security */
		wp_nonce_field('_serverstate'); ?>

		<table class="form-table">
			<tr>
				<td>
					<label>Benutzername:</label>
					<input type="text" name="serverstate[nickname]" autocomplete="off" value="<?php esc_attr_e($options['nickname']) ?>" />
				</td>
				<td>
					Noch kein Serverstate-Account?
				</td>
			</tr>
			<tr>
				<td>
					<label>Passwort:</label>
					<input type="password" name="serverstate[password]" autocomplete="off" value="<?php esc_attr_e($options['password']) ?>" />
				</td>
				<td>
					<a href="http://serverstate.de/?referrer=245049071" target="_blank" class="button-secondary">Bei Serverstate anmelden →</a>
				</td>
			</tr>
			<tr>
				<td>
					<label>Sensor ID:</label>
					<input type="text" name="serverstate[sensor_id]" autocomplete="off" value="<?php esc_attr_e($options['sensor_id']) ?>" />
				</td>
				<td>
					<em>Partnerlink. Danke.</em>
				</td>
			</tr>
			
		</table>

		<?php
	}
	
	
	/**
	* Rückgabe der Statistik-Werte
	*
	* @since   0.1
	* @change  0.1
	*
	* @return  array  $data  Array mit Statistik- oder Fehlerwerten
	*/
	
	private static function _get_stats()
	{
		/* Cronjob */
		if ( ! $data = get_transient('serverstate') ) {
			/* API Call */
			$response = self::_api_call();
			
			/* Array? */
			if ( is_array($response) ) {
				$data = self::_prepare_stats($response);
			} else {
				$data['error'] = $response;
			}
			
			/* Merken */
			set_transient(
			   'serverstate',
			   $data,
			   60 * 60 * 1 // = 1 Stunde
			 );
		}
		
		return $data;
	}
	
	
	/**
	* Call an die Serverstate-API
	*
	* @since   0.1
	* @change  0.1
	*
	* @return  mixed  $data  Array mit API-Werten oder Fehlermeldungen
	*/
	
	private static function _api_call()
	{
		/* Optionen */
		$options = get_option('serverstate');
		
		/* Init */
		$data = array(
			'day'      => array(),
			'uptime'   => array(),
			'response' => array()
		);
		
		/* Leer? */
		if ( empty($options['nickname']) or empty($options['password']) or empty($options['sensor_id']) ) {
			return sprintf(
				'Bitte Zugangsdaten im Dashboard-Widget <a href="%s">verfollständigen</a>.',
				add_query_arg(
					array(
						'edit' => 'serverstate_dashboard#serverstate_dashboard'
					),
					admin_url('/')
				)
			);
		}
		
		/* Tage loopen */
		for ($i = 0; $i < 30; $i ++) {
			/* URL erfragen */
			$response = wp_remote_get(
				esc_url_raw(
					add_query_arg(
						array(
							'nickname' => urlencode($options['nickname']),
							'password' => $options['password'],
							'sensor_id' => $options['sensor_id'],
							'day' => date('d.m.Y', strtotime('-' .$i. ' day'))
						),
						'http://serverstate.de/api/1/daily_report/'
					),
					'http'
				),
				array(
					'timeout' => 30
				)
			);

			/* Fehler? */
			if ( is_wp_error($response) ) {
				return $response->get_error_message();
			}
			
			/* Body */
			$body = wp_remote_retrieve_body($response);
			
			/* Fehler? */
			if ( $body == 'ERROR_INVALID_AUTH' ) {
				return sprintf(
					'Bitte Zugangsdaten im Dashboard-Widget <a href="%s">überprüfen</a>.',
					add_query_arg(
						array(
							'edit' => 'serverstate_dashboard#serverstate_dashboard'
						),
						admin_url('/')
					)
				);
			}
			
			/* Dekodieren */
			$xml = simplexml_load_string($body);
			
			/* Fehler? */
			if ( $xml === false ) {
				return 'Houston, wir haben ein Problem: Kein XML als Rückgabe?';
			}
			
			/* Zuweisen */
			$day = (string) $xml->day;
			$uptime = (int) $xml->uptime_percent;
			$response = (int) $xml->response_time;
			
			/* Ungültig? */
			if ( $uptime === -1 or $response === -1 ) {
				continue;
			}
			
			/* Zusammenführen */
			array_push($data['day'], $day);
			array_push($data['uptime'], $uptime);
			array_push($data['response'], $response);
		}
		
		/* Nichts gesammelt? */
		if ( empty($data['day']) ) {
			return 'Aktuell sind keine Daten zur Anzeige vorhanden.';
		}
		
		return $data;
	}
	
	
	/**
	* Vorbereitung der Werte für JS
	*
	* @since   0.1
	* @change  0.1
	*
	* @param   array  $data  Unbehandelter Array
	* @return  array  $data  Behandelter Array
	*/
	
	private static function _prepare_stats($data)
	{
		/* Leer? */
		if ( empty($data) ) {
			return array();
		}
		
		/* Einträge binden */
		return array_map(
			function($array) {
				return implode(',', $array);
			},
			$data
		);
	}
	
	
	/**
	* Plugin-Version als Konstante
	*
	* @since   0.1
	* @change  0.1
	*/
	
	private static function _define_version()
	{
		/* Auslesen */
		$meta = get_plugin_data(SERVERSTATE_FILE);
		
		/* Zuweisen */
		define('SERVERSTATE_VERSION', $meta['Version']);
	}
}