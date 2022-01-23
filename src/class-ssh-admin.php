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
	}

	/**
	 * Register settings in WordPress.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'ssh_options_group', 'ssh_app_site_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_options_group', 'ssh_static_url', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );	

		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_user', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_password', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_host', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_mail_from', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_name_from', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssh_smtp_options_group', 'ssh_smtp_mail_port', array( 'type' => 'number', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
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
			<h1><?php echo esc_html_e( 'Simply Static Hosting', 'simply-static-hosting' ); ?></h1>
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
					<p>
						<label for="ssh_static_url"><?php echo esc_html_e( 'Static URL (optional)', 'simply-static-hosting' ); ?></label></br>
						<input type="url" id="ssh_static_url" name="ssh_static_url" value="<?php echo esc_html( get_option( 'ssh_static_url' ) ); ?>" />
					</p>
					<?php submit_button(); ?>
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
				<p>
					<h2><?php echo esc_html_e( 'Setup SMTP', 'simply-static-hosting' ); ?></h2>
				</p>
				<p>
						<?php echo esc_html_e( 'The native PHP Mailer is deactivated on the Simply Static plattform for security reasons.', 'simply-static-hosting' ); ?>
				</p>
				<p style="margin-bottom: 50px;">
				<?php echo esc_html_e( 'Please add your SMTP credentials from your mail server if you want to send e-mails.', 'simply-static-hosting' ); ?>
				</p>
				<form method="post" action="options.php">
				<?php settings_fields( 'ssh_smtp_options_group' ); ?>
				<p>
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
			</div>
		</div>
		<style>
		.ssh-container .wrap {
			background: #fafafa;
			padding: 30px;
			box-sizing: border-box;
			width: 45%;
			float: left;
			min-height: 580px;
			margin-bottom:15px;
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
		</style>
		<?php
	}
}
