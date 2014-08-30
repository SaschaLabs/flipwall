<script type="text/javascript">
/* DESCRIPTION: Methods and Objects in this file are global and common in 
 * nature use this file to place all shared methods and varibles */	

//UNIQUE NAMESPACE
Flipwall			= new Object();
Flipwall.UI		= new Object();
Flipwall.Pack		= new Object();
Flipwall.Settings = new Object();
Flipwall.Tools	= new Object();
Flipwall.Tasks	= new Object();

//GLOBAL CONSTANTS
Flipwall.DEBUG_AJAX_RESPONSE = false;
Flipwall.AJAX_TIMER = null;


/* ============================================================================
*  BASE NAMESPACE: All methods at the top of the Flipwall Namespace  
*  ============================================================================	*/

/*	----------------------------------------
*	METHOD: Starts a timer for Ajax calls */ 
Flipwall.StartAjaxTimer = function() {
	Flipwall.AJAX_TIMER = new Date();
};

/*	----------------------------------------
*	METHOD: Ends a timer for Ajax calls */ 
Flipwall.EndAjaxTimer = function() {
	var endTime = new Date();
	Flipwall.AJAX_TIMER =  (endTime.getTime()  - Flipwall.AJAX_TIMER) /1000;
};

/*	----------------------------------------
*	METHOD: Reloads the current window
*	@param data		An xhr object  */ 
Flipwall.ReloadWindow = function(data) {
	if (Flipwall.DEBUG_AJAX_RESPONSE) {
		Flipwall.Pack.ShowError('debug on', data);
	} else {
		window.location.reload(true);
	}
};

//Basic Util Methods here:
Flipwall.OpenLogWindow = function(log) {
	var logFile = log || null;
	if (logFile == null) {
		window.open('?page=flipwall-tools', 'Log Window');
	} else {
		window.open('<?php echo FLIPWALL_SSDIR_URL; ?>' + '/' + log)
	}
};


/* ============================================================================
*  UI NAMESPACE: All methods at the top of the Flipwall Namespace  
*  ============================================================================	*/

/*  ----------------------------------------
 *  METHOD:   */
Flipwall.UI.SaveViewStateByPost = function (key, value) {
	if (key != undefined && value != undefined ) {
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			dataType: "json",
			data: {action : 'SLFW_UI_SaveViewStateByPost', key: key, value: value},
			success: function(data) {},
			error: function(data) {}
		});	
	}
}

/*  ----------------------------------------
 *  METHOD:   */
Flipwall.UI.AnimateProgressBar = function(id) {
	//Create Progress Bar
	var $mainbar   = jQuery("#" + id);
	$mainbar.progressbar({ value: 100 });
	$mainbar.height(25);
	runAnimation($mainbar);

	function runAnimation($pb) {
		$pb.css({ "padding-left": "0%", "padding-right": "90%" });
		$pb.progressbar("option", "value", 100);
		$pb.animate({ paddingLeft: "90%", paddingRight: "0%" }, 3500, "linear", function () { runAnimation($pb); });
	}
}


/*	----------------------------------------
* METHOD: Toggle MetaBoxes */ 
Flipwall.UI.ToggleMetaBox = function() {
	var $title = jQuery(this);
	var $panel = $title.parent().find('.dup-box-panel');
	var $arrow = $title.parent().find('.dup-box-arrow i');
	var key   = $panel.attr('id');
	var value = $panel.is(":visible") ? 0 : 1;
	$panel.toggle();
	Flipwall.UI.SaveViewStateByPost(key, value);
	(value) 
		? $arrow.removeClass().addClass('fa fa-caret-up') 
		: $arrow.removeClass().addClass('fa fa-caret-down');
	
}


jQuery(document).ready(function($) {
	//Init: Toggle MetaBoxes
	$('div.dup-box div.dup-box-title').each(function() { 
		var $title = $(this);
		var $panel = $title.parent().find('.dup-box-panel');
		var $arrow = $title.find('.dup-box-arrow');
		$title.click(Flipwall.UI.ToggleMetaBox); 
		($panel.is(":visible")) 
			? $arrow.html('<i class="fa fa-caret-up"></i>')
			: $arrow.html('<i class="fa fa-caret-down"></i>');
	});
});	

</script>