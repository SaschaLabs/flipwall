<?php
	$notice01  = SLFW_UI::GetViewStateValue('dup-notice01-chk');
?>

<div id='dup-list-alert-nodata'>
	<b><i class="fa fa-archive"></i> 
		<?php _e("No Packages Found.", 'wpflipwall'); ?><br/>
		<?php _e("Click the 'Create New' tab to build a package.", 'wpflipwall'); ?> <br/><br/>
	</b>
	<i>
		<?php
			printf("%s <a href='admin.php?page=flipwall-support'>%s</a> %s",
				__("Please visit the", 'wpflipwall'), 
				__("support section", 'wpflipwall'),
				__("for additional help topics", 'wpflipwall'));
		?>
	</i>
	
	<!-- NOTICE01: 0.5.0 and above -->
	<?php if(! $notice01)  :	?>
		<div id="dup-notice-01" class='dup-notice-msg'>
			<i class="fa fa-exclamation-triangle fa-lg"></i>
			<?php 
				_e("Older packages prior to 0.5.0 are no longer supported in this version.", 'wpflipwall'); 

				printf("  %s <a href='admin.php?page=flipwall-support'>%s</a> %s",
					__("To get an older package please visit the", 'wpflipwall'), 
					__("support page", 'wpflipwall'),
					__("and look for the Change Log link for additional instructions.", 'wpflipwall'));
			?><br/>
			<label for="dup-notice01-chk">
				<input type="checkbox" class="dup-notice-chk" id="dup-notice01-chk" name="dup-notice01-chk" onclick="Flipwall.UI.SaveViewStateByPost('dup-notice01-chk', 1); jQuery('#dup-notice-01').hide()" /> 
				<?php _e("Hide this message", 'wpflipwall'); ?>
			</label>
		</div><br/><br/>
	<?php else : ?>			
		<div style="height:75px">&nbsp;</div>
	<?php endif; ?>
</div>



