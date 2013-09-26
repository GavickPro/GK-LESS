<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php _e( 'GK LESS', 'gk_less') ?></h2>
    
    <form method="post" action="options.php" id="gk_less_admin_index">
		<?php settings_fields( 'gk_less_options' ); ?>
		<p><?php _e('GK LESS is a simple plugin which adds the LESS parsing feature to your WordPress.', 'gk_less'); ?></p>
		<table class="widefat">
	        <thead>
	           <tr>
	             <th colspan="2">
	             	<input type="submit" name="submit" value="Save Settings" class="button-primary" />
	             </th>
	           </tr>
	        </thead>
	        <tfoot>
	           <tr>
	             <th colspan="2">
	             	<input type="submit" name="submit" value="Save Settings" class="button-primary" />
	             </th>
	           </tr>
	        </tfoot>
	        <tbody>
	           <tr>
	             <td width="20%" align="right">
	                 <p title="<?php _e('Please remember that the compilation process is time consuming - disable the compiler if it is not necessary.', 'gk_less'); ?>"><?php _e('Compiler state:', 'gk_less'); ?></p>
	             </td>
	             <td>
	            	 <p>
	            	 	<label>
	            	 	<input type="radio" name="gk_less_state" value="on" <?php checked(get_option('gk_less_state', 'on'), 'on'); ?>  /> &nbsp; <?php _e('Enabled', 'gk_less'); ?>
	            	 	</label>
	            	 	 &nbsp; 
	            	 	<label>
	            	 	<input type="radio" name="gk_less_state" value="off" <?php checked(get_option('gk_less_state', 'on'), 'off'); ?>  /> &nbsp; <?php _e('Disabled', 'gk_less'); ?>
	            	 	</label>
	            	 </p>
	             </td>
	           </tr>
	           
	           <tr>
	             <td width="20%" align="right">
	                 <p title="<?php _e('You can enable the compilation only when user adds in the URL variable recompile=true - it is useful when you have a live website and you cannot recompile the code on each website request.', 'gk_less'); ?>"><?php _e('Compile on demand:', 'gk_less'); ?></p>
	             <td>
	            	 <p>
	            	 	<label>
	            	 	<input type="radio" name="gk_less_compile_on_demand" value="on" <?php checked(get_option('gk_less_compile_on_demand', 'off'), 'on'); ?>  /> &nbsp; <?php _e('Enabled', 'gk_less'); ?>
	            	 	</label>
	            	 	 &nbsp; 
	            	 	<label>
	            	 	<input type="radio" name="gk_less_compile_on_demand" value="off" <?php checked(get_option('gk_less_compile_on_demand', 'off'), 'off'); ?>  /> &nbsp; <?php _e('Disabled', 'gk_less'); ?>
	            	 	</label>
	            	 </p>
	             </td>
	           </tr>
	           
	           <tr>
	             <td width="20%" align="right">
	                 <p title="<?php _e('You can enable the admin bar button with useful links and options.', 'gk_less'); ?>"><?php _e('Admin bar button:', 'gk_less'); ?></p>
	             </td>
	             <td>
	            	 <p>
	            	 	<label>
	            	 	<input type="radio" name="gk_less_adminbar" value="on" <?php checked(get_option('gk_less_adminbar', 'on'), 'on'); ?>  />  &nbsp; <?php _e('Enabled', 'gk_less'); ?>
	            	 	</label>
	            	 	 &nbsp; 
	            	 	<label>
	            	 	<input type="radio" name="gk_less_adminbar" value="off" <?php checked(get_option('gk_less_adminbar', 'on'), 'off'); ?>  /> &nbsp; <?php _e('Disabled', 'gk_less'); ?>
	            	 	</label>
	            	 </p>
	             </td>
	           </tr>
	           
	           <tr>
	             <td width="20%" align="right">
	                 <p><?php _e('Input directory:', 'gk_less'); ?></p>
	             </td>
	             <td>
	            	 <p>
	            	 	<?php echo get_bloginfo('home'); ?>/
	            	 	<input type="text" name="gk_less_input_dir" value="<?php echo get_option('gk_less_input_dir'); ?>" placeholder="<?php _e('Your directory with *.less files', 'gk_less'); ?>" size="55" />
	            	 </p>
	             </td>
	           </tr>
	           
	           <tr>
	             <td width="20%" align="right">
	                 <p><?php _e('Files to compile:', 'gk_less'); ?></p>
	             </td>
	             <td>
	            	 <p>
	            	 	<textarea name="gk_less_input_files" cols="80" rows="10"><?php echo get_option('gk_less_input_files'); ?></textarea>
	            	 </p>
	            	 <p>
	            	 	<small><?php _e('If the above list is blank, then all files will be compiled.', 'gk_less'); ?></small>
	            	 </p>
	             </td>
	           </tr>
	           
	           <tr>
	             <td width="20%" align="right">
	                 <p><?php _e('Output directory', 'gk_less'); ?></p>
	             </td>
	             <td>
	            	 <p>
	            	    <?php echo get_bloginfo('home'); ?>/
	            	 	<input type="text" name="gk_less_output_dir" value="<?php echo get_option('gk_less_output_dir'); ?>" placeholder="<?php _e('Your directory with *.css files', 'gk_less'); ?>" size="55" />
	            	 </p>
	            	 <p><small><?php _e('<strong>Warning!</strong> Please remember that the existing *.css files can be overrided by new ones generated from the *.less files.', 'gk_less'); ?></small></p>
	             </td>
	           </tr>
	        </tbody>
	    </table>
    </form>
</div>