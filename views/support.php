<?php
	require_once(FLIPWALL_PLUGIN_PATH . '/views/javascript.php'); 
	require_once(FLIPWALL_PLUGIN_PATH . '/views/inc.header.php'); 
?>
<style>
/*================================================
PAGE-SUPPORT:*/
div.dup-support-all {font-size:13px; line-height:20px}
div.dup-support-txts-links {width:100%;font-size:14px; font-weight:bold; line-height:26px; text-align:center}
div.dup-support-hlp-area {width:265px; height:175px; float:left; border:1px solid #dfdfdf; border-radius:4px; margin:6px; line-height:18px;box-shadow: 0 8px 6px -6px #ccc;}
table.dup-support-hlp-hdrs {border-collapse:collapse; width:100%; border-bottom:1px solid #dfdfdf}
table.dup-support-hlp-hdrs {background-color:#efefef;}
table.dup-support-hlp-hdrs td {
	padding:2px; height:52px;
	font-weight:bold; font-size:17px;
	background-image:-ms-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
	background-image:-moz-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
	background-image:-o-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
	background-image:-webkit-gradient(linear, left top, left bottom, color-stop(0, #FFFFFF), color-stop(1, #DEDEDE));
	background-image:-webkit-linear-gradient(top, #FFFFFF 0%, #DEDEDE 100%);
	background-image:linear-gradient(to bottom, #FFFFFF 0%, #DEDEDE 100%);
}
table.dup-support-hlp-hdrs td img{margin-left:7px}
div.dup-support-hlp-txt{padding:10px 4px 4px 4px; text-align:center}
div.dup-support-give-area {width:400px; height:185px; float:left; border:1px solid #dfdfdf; border-radius:4px; margin:10px; line-height:18px;box-shadow: 0 8px 6px -6px #ccc;}
div.dup-spread-word {display:inline-block; border:1px solid red; text-align:center}
@-webkit-keyframes approve-keyframe  { 
    from {-webkit-transform:rotateX(0deg) rotateY(0deg) rotateZ(0deg);}
    to {-webkit-transform:rotateX(0deg) rotateY(0deg) rotateZ(30deg);}
}
img#dup-support-approved { -webkit-animation:approve-keyframe 12s 1s infinite alternate backwards}
form#dup-donate-form input {opacity:0.7;}
form#dup-donate-form input:hover {opacity:1.0;}
img#dup-img-5stars {opacity:0.7;}
img#dup-img-5stars:hover {opacity:1.0;}
</style>

<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "1a44d92e-2a78-42c3-a32e-414f78f9f484"}); </script> 

<div class="wrap dup-wrap dup-support-all">

	<!-- h2 required here for general system messages -->
	<h2 style='display:none'></h2>

	<?php FLIPWALL_header(__("Support", 'wpflipwall') ) ?>
	<hr size="1" />

	<div style="width:850px; margin:auto; margin-top: 20px">
		<table style="width:825px">
			<tr>
				<td valign="top" style="padding-top:10px; font-size:14px">
				<?php 
					_e("Created for Admins, Developers and Designers the Flipwall will streamline your workflows and help you quickly clone a WordPress application.  If you run into an issue please read through the", 'wpflipwall');
					printf(" <a href='http://www.SaschaLabs.net/flipwall-docs' target='_blank'>%s</a> ", __("knowledgebase", 'wpflipwall'));
					_e('in detail for many of the quick and common answers.', 'wpflipwall')
				?>
				</td>
				<td >
					<a href="http://www.SaschaLabs.net/labs/flipwall" target="_blank">
						<img src="<?php echo FLIPWALL_PLUGIN_URL  ?>assets/img/logo-box.png" style='text-align:top; margin:-15px 0px 0px 0px'  />
					</a>
				</td>
			</tr>
		</table><br/>
		
		
		<!--  =================================================
		NEED HELP?
		==================================================== -->
		<h2><?php _e('Need Help?', 'wpflipwall') ?></h2>

		<!-- HELP LINKS -->
		<div class="dup-support-hlp-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img src="<?php echo FLIPWALL_PLUGIN_URL  ?>assets/img/books.png" /></td>
					<td><?php _e('Knowledgebase', 'wpflipwall') ?></td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php  _e('Complete online documentation!', 'wpflipwall');?>
				<select id="dup-support-kb-lnks" style="margin-top:18px; font-size:14px; min-width: 170px">
					<option> <?php _e('Choose A Section', 'wpflipwall') ?> </option>
					<option value="http://www.SaschaLabs.net/flipwall-quick"><?php _e('Quick Start', 'wpflipwall') ?></option>
					<option value="http://www.SaschaLabs.net/flipwall-guide"><?php _e('User Guide', 'wpflipwall') ?></option>
					<option value="http://www.SaschaLabs.net/flipwall-faq"><?php _e('FAQs', 'wpflipwall') ?></option>
					<option value="http://www.SaschaLabs.net/flipwall-log"><?php _e('Change Log', 'wpflipwall') ?></option>
					<option value="http://www.SaschaLabs.net/labs/flipwall"><?php _e('Product Page', 'wpflipwall') ?></option>
				</select>
			</div>
		</div>
		

		<!-- APPROVED HOSTING -->
		<div class="dup-support-hlp-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img id="dup-support-approved" src="<?php echo FLIPWALL_PLUGIN_URL  ?>assets/img/approved.png"  /></td>
					<td><?php _e('Approved Hosting', 'wpflipwall') ?></td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php _e('Servers that work with Flipwall!', 'wpflipwall'); ?>
				<br/><br/>
				<div class="dup-support-txts-links">
					<button class="button button-primary button-large" onclick="window.open('http://www.SaschaLabs.net/flipwall-hosts', 'litg');"><?php _e('Get Hosting!', 'wpflipwall') ?></button> &nbsp; 
				</div>
			</div>
		</div>
		

		<!-- ONLINE SUPPORT -->
		<div class="dup-support-hlp-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td><img src="<?php echo FLIPWALL_PLUGIN_URL  ?>assets/img/support.png" /></td>
					<td><?php _e('Online Support', 'wpflipwall') ?></td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php _e("Work with IT Profressionals!" , 'wpflipwall');	?> 
				<br/><br/>
				
				<div class="dup-support-txts-links">
					<button class="button  button-primary button-large" onclick="Flipwall.OpenSupportWindow(); return false;"><?php _e('Get Help!', 'wpflipwall') ?></button> &nbsp; 
				</div>	
			</div>
		</div> <br style="clear:both" /><br/><br/><br/>
		
		
		
		
		<!--  ==================================================
		SUPPORT FLIPWALL
		==================================================== -->
		<h2><?php _e('Support Flipwall', 'wpflipwall') ?></h2>
		
		<!-- PARTNER WITH US -->
		<div class="dup-support-give-area">
			<table class="dup-support-hlp-hdrs">
				<tr >
					<td style="height:30px; text-align: center;">
						<span style="display: inline-block; margin-top: 5px"><?php _e('Partner with Us', 'wpflipwall') ?></span>
					</td>
				</tr>
			</table>
			<table style="text-align: center;width:100%; font-size:11px; font-style:italic; margin-top:15px">
				<tr>
					<td class="dup-support-grid-img" style="padding-left:40px">
						<div class="dup-support-cell" onclick="jQuery('#dup-donate-form').submit()">
							<form id="dup-donate-form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" > 
								<input name="cmd" type="hidden" value="_s-xclick" /> 
								<input name="hosted_button_id" type="hidden" value="EYJ7AV43RTZJL" /> 
								<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="<?php echo FLIPWALL_PLUGIN_URL  ?>assets/img/paypal.png" type="image" />
								<div style="margin-top:-5px"><?php _e('Keep Active and Online', 'wpflipwall') ?></div>
								<img src="https://www.paypalobjects.com/WEBSCR-640-20110401-1/en_US/i/scr/pixel.gif" border="0" alt="" width="1" height="1" /> 
							</form>
						</div>
					</td>
					<td style="padding-right:40px;" valign="top">
						<a href="http://wordpress.org/extend/plugins/flipwall" target="_blank"><img id="dup-img-5stars" src="<?php echo FLIPWALL_PLUGIN_URL  ?>assets/img/5star.png" /></a>
						<div  style="margin-top:-4px"><?php _e('Leave 5 Stars', 'wpflipwall') ?></div></a>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<a href="http://www.SaschaLabs.net/tools" target="_blank" style="font-size:14px">
						<i class="fa fa-rocket"></i> <?php _e('Check out other great resources', 'wpflipwall') ?>...</a>
					</td>
				</tr>
			</table>
		</div> 
		 

		<!-- SPREAD THE WORD  -->
		<div class="dup-support-give-area">
			<table class="dup-support-hlp-hdrs">
				<tr>
					<td style="height:30px; text-align: center;">
						<span style="display: inline-block; margin-top: 5px"><?php _e('Spread the Word', 'wpflipwall') ?></span>
					</td>
				</tr>
			</table>
			<div class="dup-support-hlp-txt">
				<?php
					$title = __("Duplicate Your WordPress", 'wpflipwall');
					$summary = __("Rapid WordPress Duplication by www.SaschaLabs.net", 'wpflipwall');
					$share_this_data = "st_url='" . FLIPWALL_HOMEPAGE . "' st_title='{$title}' st_summary='{$summary}'";
				?>
				<div style="width:100%; padding:20px 10px 0px 10px" align="center">
					<span class='st_facebook_vcount' displayText='Facebook' <?php echo $share_this_data; ?> ></span>
					<span class='st_twitter_vcount' displayText='Tweet' <?php echo $share_this_data; ?> ></span>
					<span class='st_googleplus_vcount' displayText='Google +' <?php echo $share_this_data; ?> ></span>
					<span class='st_linkedin_vcount' displayText='LinkedIn' <?php echo $share_this_data; ?> ></span>
					<span class='st_email_vcount' displayText='Email' <?php echo $share_this_data; ?> ></span>
				</div><br/>
			</div>
		</div>
		<br style="clear:both" /><br/>
	
	</div>
</div><br/><br/><br/><br/>

<script type="text/javascript">
jQuery(document).ready(function($) {
	
	Flipwall.OpenSupportWindow = function() {
		var url = 'http://www.SaschaLabs.net/flipwall/resources/';
		window.open(url, 'litg');
	}

	//ATTACHED EVENTS
	jQuery('#dup-support-kb-lnks').change(function() {
		if (jQuery(this).val() != "null") 
			window.open(jQuery(this).val())
	});
		
});
</script>