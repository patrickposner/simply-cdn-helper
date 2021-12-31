<?php

namespace ssch;

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
		register_setting( 'ssch_options_group', 'ssch_username', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssch_options_group', 'ssch_app_password', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssch_options_group', 'ssch_app_site_id', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );

		register_setting( 'ssch_smtp_options_group', 'ssch_smtp_user', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssch_smtp_options_group', 'ssch_smtp_password', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssch_smtp_options_group', 'ssch_smtp_host', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssch_smtp_options_group', 'ssch_smtp_mail_from', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssch_smtp_options_group', 'ssch_smtp_name_from', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
		register_setting( 'ssch_smtp_options_group', 'ssch_smtp_mail_port', array( 'type' => 'number', 'sanitize_callback' => 'sanitize_text_field', 'default' => NULL ) );
	}

	/**
	 * Register menu page for settings.
	 *
	 * @return void
	 */
	public function register_menu_page() {
		add_submenu_page( 'simply-static', __( 'Cloud', 'simply-static-cloud' ), __( 'Cloud', 'simply-static-cloud' ), 'manage_options', 'simply-static_cloud', array( $this, 'render_options' ), 10 );
	}

	/**
	 * Render options form.
	 *
	 * @return void
	 */
	public function render_options() {
		$data = Api::get_site_data();

		?>
		<div class="scch-container">
			<h1><?php echo esc_html_e( 'Simply Static Cloud', 'simply-static-cloud-helper' ); ?></h1>
			<div class="wrap">
				<p>
					<h2><?php esc_html_e( 'Connection Details', 'simply-static-cloud-helper' ); ?></h2>
				</p>
				<?php if ( $data ) : ?>
				<table class='widefat striped'>
					<thead>
						<tr>
							<th><?php esc_html_e( 'Connection', 'simply-static-cloud-helper' ); ?></th>
							<th><?php esc_html_e( 'Your data', 'simply-static-cloud-helper' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php esc_html_e( 'Site-ID', 'simply-static-cloud-helper' ); ?></td>
							<td><?php echo esc_html( $data->site->id ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Region', 'simply-static-cloud-helper' ); ?></td>
							<td><?php echo esc_html( $data->server->region ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Server (Host)', 'simply-static-cloud-helper' ); ?></td>
							<td><?php echo esc_html( $data->server->ip_address ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Server (User)', 'simply-static-cloud-helper' ); ?></td>
							<td><?php echo esc_html( $data->site->site_user ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Server (Password)', 'simply-static-cloud-helper' ); ?></td>
							<td><a href="#" target="_blank"><?php esc_html_e( 'Your SSH-Key', 'simply-static-cloud-helper' ); ?></a></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Database (Host)', 'simply-static-cloud-helper' ); ?></td>
							<td><?php echo esc_html( $data->server->ip_address ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Database (User)', 'simply-static-cloud-helper' ); ?></td>
							<td><?php echo esc_html( $data->site->site_user ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Database (Password)', 'simply-static-cloud-helper' ); ?></td>
							<td><a href="#" target="_blank"><?php esc_html_e( 'Your SSH-Key', 'simply-static-cloud-helper' ); ?></a></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (Storage Zone)', 'simply-static-cloud-helper' ); ?></td>
							<td>ssc-<?php echo esc_html( $data->cdn->storage_zone ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (Pull Zone)', 'simply-static-cloud-helper' ); ?></td>
							<td>ssc-<?php echo esc_html( $data->cdn->pull_zone ); ?></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (CNAME)', 'simply-static-cloud-helper' ); ?></td>
							<td><a href="https://ssc-<?php echo esc_html( $data->cdn->pull_zone ); ?>.b-cdn.net" target="_blank">ssc-<?php echo esc_html( $data->cdn->pull_zone ); ?>.b-cdn.net</a></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'CDN (Subdirectory)', 'simply-static-cloud-helper' ); ?></td>
							<td><?php echo esc_html( $data->cdn->sub_directory ); ?></td>
						</tr>
					</tbody>
				</table>
				<?php else : ?>
					<p><?php esc_html_e( 'Please connect your site to the simplystatic.io plattform to get access to your server data.', 'simply-static-cloud-helper' ); ?></p>
				<?php endif; ?>
			</div>
			<div class="wrap">
				<div>
					<p>
						<h2><?php echo esc_html_e( 'Connect your website', 'simply-static-cloud-helper' ); ?></h2>
					</p>
					<p>
						<?php echo esc_html_e( 'Add the credentials that was send to you by e-mail after you made you purchase on simplystatic.io.', 'simply-static-cloud-helper' ); ?>
					</p>
					<p style="margin-bottom: 50px;">
						<?php echo esc_html_e( 'You need an active connection to get your access credentials to your server and to automatically deploy your site to the CDN.', 'simply-static-cloud-helper' ); ?>
					</p>
					<form method="post" action="options.php">
					<?php settings_fields( 'ssch_options_group' ); ?>
					<p>
						<label for="ssch_username"><?php echo esc_html_e( 'Username', 'simply-static-cloud-helper' ); ?></label></br>
						<input type="text" id="ssch_username" name="ssch_username" value="<?php echo esc_html( get_option( 'ssch_username' ) ); ?>" />
					</p>
					<p>
						<label for="ssch_app_password"><?php echo esc_html_e( 'Application Password', 'simply-static-cloud-helper' ); ?></label></br>
						<input type="password" id="ssch_app_password" name="ssch_app_password" value="<?php echo esc_html( get_option( 'ssch_app_password' ) ); ?>" />
					</p>
					<p>
						<label for="ssch_app_site_id"><?php echo esc_html_e( 'Site-ID', 'simply-static-cloud-helper' ); ?></label></br>
						<input type="text" id="ssch_app_site_id" name="ssch_app_site_id" value="<?php echo esc_html( get_option( 'ssch_app_site_id' ) ); ?>" />
					</p>
					<?php submit_button(); ?>
					</form>
				</div>
				<div>
				</div>
			</div>
			<div class="wrap">
				<p>
					<h2><?php echo esc_html_e( 'Setup SMTP', 'simply-static-cloud-helper' ); ?></h2>
				</p>
				<p>
						<?php echo esc_html_e( 'The native PHP Mailer is deactivated on the Simply Static plattform for security reasons.', 'simply-static-cloud-helper' ); ?>
				</p>
				<p style="margin-bottom: 50px;">
				<?php echo esc_html_e( 'Please add your SMTP credentials from your mail server if you want to send e-mails.', 'simply-static-cloud-helper' ); ?>
				</p>
				<form method="post" action="options.php">
				<?php settings_fields( 'ssch_smtp_options_group' ); ?>
				<p>
					<label for="ssch_smtp_user"><?php echo esc_html_e( 'SMTP User', 'simply-static-cloud-helper' ); ?></label></br>
					<input type="text" id="ssch_smtp_user" name="ssch_smtp_user" value="<?php echo esc_html( get_option( 'ssch_smtp_user' ) ); ?>" placeholder="user@example.com" />
				</p>
				<p>
					<label for="ssch_smtp_password"><?php echo esc_html_e( 'SMTP Password', 'simply-static-cloud-helper' ); ?></label></br>
					<input type="password" id="ssch_smtp_password" name="ssch_smtp_password" value="<?php echo esc_html( get_option( 'ssch_smtp_password' ) ); ?>" />
				</p>
				<p>
					<label for="ssch_smtp_host"><?php echo esc_html_e( 'SMTP Host', 'simply-static-cloud-helper' ); ?></label></br>
					<input type="text" id="ssch_smtp_host" name="ssch_smtp_host" value="<?php echo esc_html( get_option( 'ssch_smtp_host' ) ); ?>" placeholder="smtp.example.com" />
				</p>
				<p>
					<label for="ssch_smtp_mail_from"><?php echo esc_html_e( 'E-Mail (from)', 'simply-static-cloud-helper' ); ?></label></br>
					<input type="text" id="ssch_smtp_mail_from" name="ssch_smtp_mail_from" value="<?php echo esc_html( get_option( 'ssch_smtp_mail_from' ) ); ?>" placeholder="website@example.com" />
				</p>
				<p>
					<label for="ssch_smtp_name_from"><?php echo esc_html_e( 'Name (from)', 'simply-static-cloud-helper' ); ?></label></br>
					<input type="text" id="ssch_smtp_name_from" name="ssch_smtp_name_from" value="<?php echo esc_html( get_option( 'ssch_smtp_name_from' ) ); ?>" placeholder="e.g Website Name" />
				</p>
				<p>
					<label for="ssch_smtp_mail_port"><?php echo esc_html_e( 'SMTP Port', 'simply-static-cloud-helper' ); ?></label></br>
					<input type="number" id="ssch_smtp_mail_port" name="ssch_smtp_mail_port" value="<?php echo esc_html( get_option( 'ssch_smtp_mail_port' ) ); ?>" placeholder="25" />
				</p>
				<?php submit_button(); ?>
				</form>
			</div>
		</div>
		<style>
		.scch-container .wrap {
			background: #fafafa;
			padding: 30px;
			box-sizing: border-box;
			width: 45%;
			float: left;
			min-height: 580px;
			margin-bottom:15px;
		}

		.scch-container #submit {
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
		.scch-container #submit:hover {
			background-color: #6500cc;
			color: #fff;
			border-color: transparent;
			box-shadow: 0 0 30px 0 rgba(114,0,229,.4);
		}
		.scch-container input {
			width: 100%;
		}
		</style>
		<?php
	}
}
