<div id='simplycdn' class='tab-pane'>
	<h3 class="title">
		<?php esc_html_e( 'Simply CDN', 'simply-static-pro' ); ?></h3>
	<table class='form-table'>
		<tbody>
		<tr>
			<th>
				<label for='security-token'><?php esc_html_e( 'Security Token', 'simply-cdn-helper' ); ?></label>
			</th>
			<td>
				<input type='security-token' id='security-token' name='security-token' value='[SECURITY_TOKEN]' class='widefat'/>
				<div id='deploymentHelpBlock' class='help-block'>
					<p>
						<?php esc_html_e( 'Copy and paste the security token from your Simply CDN Project dashboard to connect ot the plattform.', 'simply-cdn-helper' ); ?></p>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for='static-url'><?php esc_html_e( 'Static URL', 'simply-cdn-helper' ); ?></label>
			</th>
			<td>
				<input type='url' id='static-url' name='static-url' value='[STATIC_URL]' class='widefat'/>
				<div id='deploymentHelpBlock' class='help-block'>
					<p>
						<?php esc_html_e( "Add the static URL of your website here. It will enable a little quick link feature in your admin bar to view the static site.", 'simply-cdn-helper' ); ?>
					</p>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for='404-path'><?php esc_html_e( 'Path to your 404 page', 'simply-cdn-helper' ); ?></label>
			</th>
			<td>
				<input type='text' id='404-path' name='404-path' value='[404_PATH]' class='widefat'/>
				<div id='deploymentHelpBlock' class='help-block'>
                    <p>
						<?php esc_html_e( "Add the path to your custom 404 page. Simply CDN will provide a basic one as a default.", 'simply-cdn-helper' ); ?>
                    </p>
				</div>
			</td>
		</tr>
        <tr>
            <th>
                <label for='use-forms-hook'><?php _e( "Use Forms integration", 'simply-cdn-helper' ); ?></label>
            </th>
            <td>
                <input type="checkbox" name="use-forms-hook" id="use-forms-hook" [USE_FORMS_WEBHOOK] />
                <p>
		            <?php esc_html_e( "We automatically send you submissions of your forms via e-mail from message@simplystatic.io", 'simply-cdn-helper' ); ?>
                </p>
            </td>
        </tr>
        <tr>
            <th>
                <label for='clear.cache'><?php esc_html_e( 'Clear Cache', 'simply-cdn-helper' ); ?></label>
            </th>
            <td>
                <a class='button button-secondary' id="sch-clear-cache"><?php esc_html_e( 'Clear Cache', 'simply-cdn-helper' ); ?></a>
                <div id='deploymentHelpBlock' class='help-block'>
                    <p>
						<?php esc_html_e( "Clear the cache of your static website. This is done automatically on each static export.", 'simply-cdn-helper' ); ?>
                    </p>
                </div>
            </td>
        </tr>
		</tbody>
	</table>
	<table class='form-table'>
		<tbody>
		<tr>
			<th></th>
			<td>
				<p class='submit'>
					<input class='button button-primary' type='submit' name='save'
					       value='<?php esc_html_e( 'Save Changes', 'simply-static-pro' ); ?>'/>
				</p>
			</td>
		</tr>
		</tbody>
	</table>
</div>