<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="scribe-api-key"><?php _e( 'API Key', 'scribeseo' ); ?></label>
				<a class="scribe-help-marker" rel="popover" title="<?php _e( 'API Key', 'scribeseo' ); ?>" data-content="<?php _e( 'Enter your API key for Scribe SEO. You will find your API key by logging in to https://my.scribeseo.com. Be sure to include the scribe- at the beginning of the key and that you do not have an extra space at the end after pasting it in the box. If you require an API key, go to https://purchase.scribeseo.com.', 'scribeseo' ); ?>" href="#">?</a>
			</th>
			<td>
				<input class="regular-text" type="text" name="scribe[api-key]" id="scribe-api-key" value="<?php echo esc_attr( $settings['api-key'] ); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="scribe-seo-tool"><?php _e( 'SEO Tool', 'scribeseo' ); ?></label>
				<a class="scribe-help-marker" rel="popover" title="<?php _e( 'SEO Tool', 'scribeseo' ); ?>" data-content="<?php _e( 'Use the drop down box to select the compatible SEO theme, framework or plugin you are using to set the title and meta description for your posts. See http://scribeseo.com/compatibility for a complete list of supported SEO themes and plugins.', 'scribeseo' ); ?>" href="#">?</a>
			</th>
			<td>
<?php
			$hiddens = array();
			if ( is_wp_error( $dependencies ) ) {
				printf( __( 'The list of available dependencies could not be retrieved. Please check the <a href="%1$s">compatibility page</a> and submit a support ticket.', 'scribeseo' ), add_query_arg( array( 'page' => 'scribe-compatibility' ), admin_url( 'admin.php' ) ) );
			} else {
				$cumulative_count = count( $dependencies->plugins ) + count( $dependencies->themes );
				if ( 0 == $cumulative_count ) {
					printf( __( 'Please install and activate a valid SEO tool. You can see a full list of tools supported by Scribe at the Scribe SEO <a href="%s">compatibility</a> page.', 'scribeseo' ), 'http://scribeseo.com/compatibility/' );
				} elseif ( 1 == $cumulative_count ) {
					if ( ! empty( $dependencies->plugins ) ) {
						$plugin_dependency =  current( $dependencies->plugins );
						$hiddens[] = sprintf( '<input type="hidden" name="scribe-seo-tool-settings-plugin-%s" value="%s" />', sanitize_title_with_dashes( $plugin_dependency['name'] ), esc_attr( serialize( $plugin_dependency ) ) );
						echo '<input type="hidden" name="scribe[seo-tool]" value="plugin-' . esc_attr( $plugin_dependency->name ) . '" />';
						echo esc_html( $plugin_dependency->name );
					} else {
						$theme_dependency = current($dependencies->themes);
						$hiddens[] = sprintf( '<input type="hidden" name="scribe-seo-tool-settings-theme-%s" value="%s" />', sanitize_title_with_dashes( $theme_dependency->name), esc_attr( serialize( $theme_dependency ) ) );
						echo '<input type="hidden" name="scribe[seo-tool]" value="theme-' . esc_attr( $theme_dependency->name ) . '">';
						echo esc_html( $theme_dependency->name);
					}
?>
				<br />
				<small><em><?php _e( 'This tool was automatically chosen because it is the only supported tool you currently have activated.', 'scribeseo' ); ?></em></small>
<?php 
				} else {
?>
				<select class="scribe-select" name="scribe[seo-tool]" id="scribe-seo-tool">
					<option value=""><?php _e( '-- Select One --', 'scribeseo' ); ?></option>
					<?php if ( ! empty( $dependencies->plugins ) ) { ?>
					<optgroup label="<?php _e( 'Plugins', 'scribeseo' ); ?>">
<?php 
						foreach( $dependencies->plugins as $plugin_dependency ) {
							$hiddens[] = sprintf( '<input type="hidden" name="scribe-seo-tool-settings-plugin-%s" value="%s" />', sanitize_title_with_dashes( $plugin_dependency->name ), esc_attr( serialize( $plugin_dependency ) ) );
?>
						<option <?php selected( "plugin-{$plugin_dependency->name}", $settings['seo-tool'] ); ?> value="plugin-<?php echo esc_attr( $plugin_dependency->name ); ?>"><?php echo esc_html( $plugin_dependency->name ); ?></option>
						<?php } ?>
					</optgroup>
					<?php } ?>
					
					<?php if(!empty($dependencies->themes)) { ?>
					<optgroup label="<?php _e( 'Themes', 'scribeseo' ); ?>">
<?php 
						foreach( $dependencies->themes as $theme_dependency ) {
							$hiddens[] = sprintf( '<input type="hidden" name="scribe-seo-tool-settings-theme-%s" value="%s" />', sanitize_title_with_dashes( $theme_dependency->name ), esc_attr( serialize( $theme_dependency ) ) );
?>
						<option <?php selected( "theme-{$theme_dependency->name}", $settings['seo-tool'] ); ?> value="theme-<?php echo esc_attr( $theme_dependency->name); ?>"><?php echo esc_html( $theme_dependency->name ); ?></option>
						<?php } ?>
					</optgroup>
					<?php } ?>
				</select>
<?php
				}
			}
			echo implode( '', $hiddens );
?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="scribe-your-url"><?php _e( 'Your URL', 'scribeseo' ); ?></label>
				<a class="scribe-help-marker" rel="popover" title="<?php _e( 'Your URL', 'scribeseo' ); ?>" data-content="<?php _e( 'By default, the URL of your site is entered (and required). If your current WordPress site is not public, please enter a URL that is on the web.', 'scribeseo' ); ?>" href="#">?</a>
			</th>
			<td>
				<input class="regular-text" type="text" name="scribe[your-url]" id="scribe-your-url" value="<?php esc_attr_e($settings['your-url']); ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="scribe-security-method"><?php _e( 'Security Method', 'scribeseo' ); ?></label>
				<a class="scribe-help-marker" rel="popover" title="<?php _e( 'Security Method', 'scribeseo' ); ?>" data-content='<?php _e( ' By default, all communications with our servers are not encrypted. This means that we are not using SSL to hide your information on the web. For some, this may be acceptable. Other users may not want this. If you require SSL connections, then enable "Enhanced SSL" as described below. Otherwise, leave it at "Basic Non-SSL.”', 'scribeseo' ); ?>' href="#">?</a>
			</th>
			<td>
				<select class="scribe-select" name="scribe[security-method]" id="scribe-security-method">
					<option <?php selected(false, $settings['security-method']); ?> value="0"><?php _e('Basic Non-SSL') ?></option>
					<option <?php selected(true, $settings['security-method']); ?> value="1"><?php _e('Enhanced SSL'); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="scribe-permissions-level"><?php _e( 'Permissions', 'scribeseo' ); ?></label>
				<a class="scribe-help-marker" rel="popover" title="<?php _e( 'Permissions', 'scribeseo' ); ?>" data-content="<?php _e( 'Scribe enables you to control which user roles have access to Scribe. Click the drop down box to restrict Permissions to Administrators, Editors, Authors, or Contributors or higher. Set the lowest Role within WordPress that is available to use Scribe.', 'scribeseo' ); ?>" href="#">?</a>
			</th>
			<td>
				<select class="scribe-select" name="scribe[permissions-level]" id="scribe-permissions-level">
					<option <?php selected('manage_options', $settings['permissions-level']); ?> value="manage_options"><?php _e('Administrator'); ?></option>
					<option <?php selected('delete_others_posts', $settings['permissions-level']); ?> value="delete_others_posts"><?php _e('Editor'); ?></option>
					<option <?php selected('delete_published_posts', $settings['permissions-level']); ?> value="delete_published_posts"><?php _e('Author'); ?></option>
					<option <?php selected('edit_posts', $settings['permissions-level']); ?> value="edit_posts"><?php _e('Contributor'); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<?php _e( 'Post Types', 'scribeseo' ); ?>
				<a class="scribe-help-marker" rel="popover" title="<?php _e( 'Post Types', 'scribeseo' ); ?>" data-content="<?php _e( 'By default, Posts and Pages in WordPress can use Scribe. If you have other Custom Post types, please select them for use with Scribe. Add a check mark next to each type of post that you want to be able to run Scribe SEO. No check mark indicates that Scribe will not be availble when editing the respective post type.', 'scribeseo' ); ?>" href="#">?</a>
			</th>
			<td>
				<ul>
					<?php foreach( get_post_types( array( 'show_ui' => true, 'public' => true ), 'objects' ) as $post_type_key => $post_type ) { ?>
					<li>
						<label>
							<input name="scribe[post-types][]" type="checkbox" <?php checked(true, in_array($post_type_key, $settings['post-types'])); ?> value="<?php echo esc_attr( $post_type_key ); ?>" />
							<?php echo esc_html( $post_type->labels->name ); ?>
						</label>
					</li>	
					<?php } ?>
				</ul>
			</td>
		</tr>
	</tbody>
</table>