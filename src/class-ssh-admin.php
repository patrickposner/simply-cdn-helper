<?php

namespace ssh;

/**
 * Class to handle admin settings
 */
class Admin {
	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Admin.
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor for Admin.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
		add_action( 'updated_option', array( $this, 'update_host_name' ), 10, 3 );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function add_admin_scripts() {
		wp_enqueue_script( 'ssh-admin', SIMPLY_STATIC_HOSTING_URL . '/assets/ssh-admin.js', array( 'jquery' ), '1.0', true );

		$args = array(
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'mailer_nonce'  => wp_create_nonce( 'ssh-mailer-nonce' ),
			'cache_nonce'   => wp_create_nonce( 'ssh-cache-nonce' ),
			'mail_sent'     => __( 'Test E-Mail sent successfully.', 'simply-static-hosting' ),
			'cache_cleared' => __( 'Cache cleared successfully.', 'simply-static-hosting' ),
		);

		wp_localize_script( 'ssh-admin', 'ssh_ajax', $args );

	}

	/**
	 * Register settings in WordPress.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'ssh_options_group', 'ssh_app_site_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_cdn_group', 'ssh_static_url', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_cdn_group', 'ssh_404_path', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_user', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_password', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_connection', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_host', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_mail_from', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_name_from', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_mail_port', array( 'type' => 'number', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_branding_group', 'ssh_admin_logo', array( 'type' => 'number', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
	}

	/**
	 * Register menu page for settings.
	 *
	 * @return void
	 */
	public function register_menu_page() {
		add_submenu_page( 'simply-static', __( 'Hosting', 'simply-static-hosting' ), __( 'Hosting', 'simply-static-hosting' ), 'manage_options', 'simply-static_hosting', array( $this, 'render_options' ), 10 );
	}

	/**
	 * Render options form.
	 *
	 * @return void
	 */
	public function render_options() {
		$data = Api::get_site_data();

		?>
		<div class="ssh-container">
			<h1 style="text-align:center"><?php echo esc_html_e( 'Simply Static Hosting', 'simply-static-hosting' ); ?></h1>
			<div class="wrap">
				<div>
					<p>
						<h2><?php echo esc_html_e( 'Connect your website', 'simply-static-hosting' ); ?></h2>
					</p>
					<p>
						<?php echo esc_html_e( 'Add the site id that was sent to you by e-mail after you made your purchase on simplystatic.io.', 'simply-static-hosting' ); ?>
					</p>
					<p style="margin-bottom: 50px;">
						<?php echo esc_html_e( 'You need an active connection to get your access credentials to your server and to automatically deploy your site to the CDN.', 'simply-static-hosting' ); ?>
					</p>
					<form method="post" action="options.php">
					<?php settings_fields( 'ssh_options_group' ); ?>
					<p>
						<label for="ssh_app_site_id"><?php echo esc_html_e( 'Site-ID', 'simply-static-hosting' ); ?></label></br>
						<input type="text" id="ssh_app_site_id" name="ssh_app_site_id" value="<?php echo esc_html( get_option( 'ssh_app_site_id' ) ); ?>" />
					</p>
					<?php submit_button(); ?>
					<?php if ( ! empty( $data ) ) : ?>
					<p class="success"><?php echo esc_html_e( 'Your site is successfully connected to the platform.', 'simply-static-hosting' ); ?></p>
					<?php endif; ?>
					</form>
				</div>
				<div>
				</div>
			</div>
			<div class="wrap">
				<p>
					<h2><?php esc_html_e( 'Connection Details', 'simply-static-hosting' ); ?></h2>
				</p>
				<?php if ( $data ) : ?>
				<table class='widefat striped'>
					<thead>
						<tr>
							<th><?php esc_html_e( 'Connection', 'simply-static-hosting' ); ?></th>
							<th><?php esc_html_e( 'Your data', 'simply-static-hosting' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php esc_html_e( 'Site-ID', 'simply-static-hosting' ); ?></td>
							<td><?php echo esc_html( $data->site->id ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Region', 'simply-static-hosting' ); ?></td>
							<td><?php echo esc_html( $data->server->region ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Server (Host)', 'simply-static-hosting' ); ?></td>
							<td><?php echo esc_html( $data->server->ip_address ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Server (User)', 'simply-static-hosting' ); ?></td>
							<td><?php echo esc_html( $data->site->site_user ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Server (Password)', 'simply-static-hosting' ); ?></td>
							<td><a href="#" target="_blank"><?php esc_html_e( 'Your SSH-Key', 'simply-static-hosting' ); ?></a></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Database (Host)', 'simply-static-hosting' ); ?></td>
							<td><?php echo esc_html( $data->server->ip_address ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Database (User)', 'simply-static-hosting' ); ?></td>
							<td><?php echo esc_html( $data->site->site_user ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Database (Password)', 'simply-static-hosting' ); ?></td>
							<td><a href="#" target="_blank"><?php esc_html_e( 'Your SSH-Key', 'simply-static-hosting' ); ?></a></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (Storage Zone)', 'simply-static-hosting' ); ?></td>
							<td>sshm-<?php echo esc_html( $data->cdn->storage_zone ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (Pull Zone)', 'simply-static-hosting' ); ?></td>
							<td>sshm-<?php echo esc_html( $data->cdn->pull_zone ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (CNAME)', 'simply-static-hosting' ); ?></td>
							<td><a href="https://sshm-<?php echo esc_html( $data->cdn->pull_zone ); ?>.b-cdn.net" target="_blank">sshm-<?php echo esc_html( $data->cdn->pull_zone ); ?>.b-cdn.net</a></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (Subdirectory)', 'simply-static-hosting' ); ?></td>
							<td><?php echo esc_html( $data->cdn->sub_directory ); ?></td>
						</tr>
					</tbody>
				</table>
				<?php else : ?>
					<p><?php esc_html_e( 'Please connect your site to the simplystatic.io plattform to get access to your server data.', 'simply-static-hosting' ); ?></p>
				<?php endif; ?>
			</div>
			<div class="wrap">
				<div>
					<p>
						<h2><?php echo esc_html_e( 'Configure your static website', 'simply-static-hosting' ); ?></h2>
					</p>
					<p>
						<?php echo esc_html_e( 'Once your website is connected you can configure all settings related to the CDN here. This includes settings up redirects, proxy URLs and setting up a custom 404 error page.', 'simply-static-hosting' ); ?>
					</p>
					<form method="post" action="options.php">
					<?php settings_fields( 'ssh_cdn_group' ); ?>
					<p>
						<label for="ssh_static_url"><?php echo esc_html_e( 'Static URL', 'simply-static-hosting' ); ?></label></br>
						<input type="url" id="ssh_static_url" name="ssh_static_url" value="<?php echo esc_html( get_option( 'ssh_static_url' ) ); ?>" />
						<small><?php echo esc_html_e( 'Once you change this setting, your static website will be available under the new domain. Make sure you set your CNAME record before you change this setting.', 'simply-static-hosting' ); ?></small>
					</p>
					<p>
						<label for="ssh_404_path"><?php echo esc_html_e( 'Relative path to your 404 page', 'simply-static-hosting' ); ?></label></br>
						<input type="text" id="ssh_404_path" name="ssh_404_path" value="<?php echo esc_html( get_option( 'ssh_404_path' ) ); ?>" />
					</p>
					<?php submit_button(); ?>
					</form>
					<p>
					<h2><?php echo esc_html_e( 'Caching', 'simply-static-hosting' ); ?></h2>
						<?php echo esc_html_e( 'The CDN cache is cleared automatically after each static export. Sometimes you want to clear the cache manually to make sure you get the latest results in your browser.', 'simply-static-hosting' ); ?>
					</p>
					<p>
					<span class="button-secondary button ssh-secondary-button" id="ssh-clear-cache"><?php echo esc_html_e( 'Clear Cache', 'simply-static-hosting' ); ?></span>
					</p>
				</div>
				<div>
				</div>
			</div>
			<div class="wrap">
				<p>
					<h2><?php echo esc_html_e( 'Setup SMTP', 'simply-static-hosting' ); ?></h2>
				</p>
				<p>
					<?php echo esc_html_e( 'We provide you with a ready-to-go e-mail address and a from-name to receive e-mails from your website. If you like to use your own e-mail, please provide the SMTP credentials below.', 'simply-static-hosting' ); ?>
				</p>
				<form method="post" action="options.php">
				<?php settings_fields( 'ssh_smtp_options_group' ); ?>
				<p>
					<?php
					$url        = wp_parse_url( untrailingslashit( get_bloginfo( 'url' ) ) );
					$mail_parts = explode( '.', $url['host'] );
					$mail       = $mail_parts[0] . '@' . $mail_parts[1];

					printf( __( 'Your default e-mail address is %s, and we use %s as the from name to send e-mails from your website.', 'simply-static-hosting' ), '<b>' . esc_html( $mail ) . '</b>', '<b>' . esc_html( get_bloginfo( 'name' ) ) . '</b>' );
					?>
				</p>
					<label for="ssh_smtp_user"><?php echo esc_html_e( 'SMTP User', 'simply-static-hosting' ); ?></label></br>
					<input type="text" id="ssh_smtp_user" name="ssh_smtp_user" value="<?php echo esc_html( get_option( 'ssh_smtp_user' ) ); ?>" placeholder="user@example.com" />
				</p>
				<p>
					<label for="ssh_smtp_password"><?php echo esc_html_e( 'SMTP Password', 'simply-static-hosting' ); ?></label></br>
					<input type="password" id="ssh_smtp_password" name="ssh_smtp_password" value="<?php echo esc_html( get_option( 'ssh_smtp_password' ) ); ?>" />
				</p>
				<p>
					<label for="ssh_smtp_host"><?php echo esc_html_e( 'SMTP Host', 'simply-static-hosting' ); ?></label></br>
					<input type="text" id="ssh_smtp_host" name="ssh_smtp_host" value="<?php echo esc_html( get_option( 'ssh_smtp_host' ) ); ?>" placeholder="smtp.example.com" />
				</p>
				<p>
				<label for="ssh_smtp_connection"><?php echo esc_html_e( 'SMTP Secure Connection', 'simply-static-hosting' ); ?></label></br>
					<select name="ssh_smtp_connection">
						<?php if ( 'tls' === get_option( 'ssh_smtp_connection' ) ) : ?>
							<option value="tls" selected="selected"><?php echo esc_html_e( 'TLS', 'simply-static-hosting' ); ?></option>
							<option value="ssl"><?php echo esc_html_e( 'SSL', 'simply-static-hosting' ); ?></option>
						<?php else : ?>
							<option value="tls"><?php echo esc_html_e( 'TLS', 'simply-static-hosting' ); ?></option>
							<option value="ssl" selected="selected"><?php echo esc_html_e( 'SSL', 'simply-static-hosting' ); ?></option>
						<?php endif; ?>
					</select>					
				</p>
				<p>
					<label for="ssh_smtp_mail_from"><?php echo esc_html_e( 'E-Mail (from)', 'simply-static-hosting' ); ?></label></br>
					<input type="text" id="ssh_smtp_mail_from" name="ssh_smtp_mail_from" value="<?php echo esc_html( get_option( 'ssh_smtp_mail_from' ) ); ?>" placeholder="website@example.com" />
				</p>
				<p>
					<label for="ssh_smtp_name_from"><?php echo esc_html_e( 'Name (from)', 'simply-static-hosting' ); ?></label></br>
					<input type="text" id="ssh_smtp_name_from" name="ssh_smtp_name_from" value="<?php echo esc_html( get_option( 'ssh_smtp_name_from' ) ); ?>" placeholder="e.g Website Name" />
				</p>
				<p>
					<label for="ssh_smtp_mail_port"><?php echo esc_html_e( 'SMTP Port', 'simply-static-hosting' ); ?></label></br>
					<input type="number" id="ssh_smtp_mail_port" name="ssh_smtp_mail_port" value="<?php echo esc_html( get_option( 'ssh_smtp_mail_port' ) ); ?>" placeholder="25" />
				</p>
				<?php submit_button(); ?>
				</form>
				<form method="post" action="options.php">
				<p>
				<h2><?php echo esc_html_e( 'Test E-Mail', 'simply-static-hosting' ); ?></h2>
				<?php echo esc_html_e( 'Once you added and saved your SMTP settings, you can send a test e-mail to see if everything works correctly.', 'simply-static-hosting' ); ?>
				</p>
				<p>
					<label for="ssh_test_mail"><?php echo esc_html_e( 'E-Mail', 'simply-static-hosting' ); ?></label></br>
					<input type="text" id="ssh_test_mail" name="ssh_test_mail" value="" placeholder="john@doe.de" />
				</p>
				<p>
					<span class="ssh-secondary-button button" id="ssh-send-test-email"><?php echo esc_html_e( 'Send E-Mail', 'simply-static-hosting' ); ?></span>
				</p>
				</form>
			</div>
			<div class="wrap">
				<div>
					<p>
						<h2><?php echo esc_html_e( 'Branding', 'simply-static-hosting' ); ?></h2>
					</p>
					<p>
						<?php echo esc_html_e( "You may want to apply your corporate design to the client's admin area. We added a couple of settings to customize the appearance further.", 'simply-static-hosting' ); ?>
					</p>
					<form method="post" action="options.php">
					<?php settings_fields( 'ssh_branding_group' ); ?>
					<p>
						<label for="ssh_admin_logo"><?php echo esc_html_e( 'Add a URL to your logo here.', 'simply-static-hosting' ); ?></label></br>
						<input type="url" id="ssh_admin_logo" name="ssh_admin_logo" value="<?php echo esc_html( get_option( 'ssh_admin_logo' ) ); ?>" />
						<small><?php echo esc_html_e( 'This image will replace the Simply Static Hosting logo on the login page of your WordPress website.', 'simply-static-hosting' ); ?></small>
					</p>
					<?php submit_button(); ?>
					</form>
				</div>
				<div>
				</div>
			</div>
		</div>
		<style>
		.ssh-container .wrap {
			background: #fafafa;
			padding: 30px;
			box-sizing: border-box;
			min-height: auto;
			margin-bottom: 15px;
			max-width: 700px;
			margin: 20px auto;
		}

		.ssh-container #submit {
			background: #7200e5;
			background-color: rgb(114, 0, 229);
			background-color: #7200e5;
			color: #fff;
			border: 2px solid transparent;
			box-shadow: 0 0 20px 0 rgba(114,0,229,.2);
			margin: 0;
			display: inline-block;
			box-sizing: border-box;
			padding: 0 30px;
			vertical-align: middle;
			font-size: 15px;
			line-height: 36px;
			text-align: center;
			text-decoration: none;
			transition: .3s ease-in-out;
			transition-property: all;
			transition-property: color,background-color,background-position,background-size,border-color,box-shadow;
			border-radius: 5px;
			background-origin: border-box;
		}
		.ssh-container #submit:hover {
			background-color: #6500cc;
			color: #fff;
			border-color: transparent;
			box-shadow: 0 0 30px 0 rgba(114,0,229,.4);
		}
		.ssh-container input {
			width: 100%;
		}
		.ssh-container select {
			width: 100%;
			max-width: 100% !important;
		}

		.ssh-secondary-button  {
			background-color: #959595 !important;
			color: #fff;
			border: 2px solid transparent !important;
			box-shadow: 0 0 20px 0 rgba(114,0,229,.2);
			margin: 0;
			display: inline-block;
			box-sizing: border-box;
			padding: 0 30px;
			vertical-align: middle;
			font-size: 15px;
			line-height: 36px;
			text-align: center;
			text-decoration: none;
			transition: .3s ease-in-out;
			transition-property: all;
			transition-property: all;
			transition-property: all;
			transition-property: color,background-color,background-position,background-size,border-color,box-shadow;
			border-radius: 5px;
			background-origin: border-box;
			width: 100%;
			color:white !important;
		}

		.ssh-secondary-button:hover {
			background-color: #626262;
			color: #fff;
			border-color: transparent;
			box-shadow: 0 0 30px 0 rgba(98,98,98,.4);
		}

		.ssh-container .success {
		color: #2aa42a;
		}
		</style>
		<?php
	}

	/**
	 * Update hostname if static URL changed.
	 *
	 * @param string $option_name current option name.
	 * @param string $old_value old value opf the option.
	 * @param string $option_value new value of the option.
	 * @return void
	 */
	public function update_host_name( $option_name, $old_value, $option_value ) {
		if ( 'ssh_static_url' === $option_name && $old_value !== $option_value ) {
			// Send API request.
		}
	}
}
