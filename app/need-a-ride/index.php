<?php
//Include the necessary scripts
	$essentials->includeCSS("ride.superpackage.min.css");
	$essentials->includeJS("//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js");
	$essentials->includeJS("//tinymce.cachefly.net/4/tinymce.min.js");
	$essentials->includeJS("ride.superpackage.min.js");
	$essentials->includePluginClass("display/Ride_Request_Display");
	$essentials->includePluginClass("exceptions/No_Data_Returned");
	$essentials->includePluginClass("exceptions/Validation_Failed");
	$essentials->includePluginClass("processing/Ride_Request_Process");
	$essentials->requireLogin();
	$essentials->setTitle("Ask for a Ride");
	
//Instantiate the form element display and processor classes
	$params = $essentials->params ? $essentials->params[0] : 0;
	$userID = $essentials->user->ID;
	
	try {
		$display = new FFI\TA\Ride_Request_Display($params, $userID);
		new FFI\TA\Ride_Request_Process($params);
	} catch (No_Data_Returned $e) {
		wp_redirect($essentials->friendlyURL("need-a-ride"));
		exit;
	} catch (Validation_Failed $e) {
		echo $e->getMessage();
		exit;
	} catch (Exception $e) {
		wp_redirect($essentials->friendlyURL("need-a-ride"));
		exit;
	}
	
//Display the page
	echo "<h1>Ask for Ride</h1>
	
<form class=\"form-horizontal\" method=\"post\">\n";

//Display the splash section	
	echo "<section class=\"request\" id=\"splash\">
<div class=\"ad-container\">
<div class=\"ad-contents\">
<h2>Ask for a Ride</h2>
</div>
</div>
</section>

";
	
//Display the directions
	echo "<section class=\"welcome\">
<h2>Ask for a Ride</h2>
<p>If you are in need of ride back home or back to college, use this page to post your request. If a willing individual finds your request, he or she will be in touch with you. This page can also be used if you are a commuter and would like to schedule a recurring trip with a driver.</p>
</section>

";

//Display the (2 of the) 5 W questions section
	echo "<section class=\"step stripe\">
<header>
<h2>The Five W Questions</h2>
<h3>... but we really only care about two of them.</h3>
<h4 class=\"step\">1</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\" for=\"who\">Who:</label>
<div class=\"controls\">
" . $display->getWho() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"what\">What:</label>
<div class=\"controls\">
" . $display->getWhat() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"when\">When:</label>
<div class=\"controls\">
" . $display->getWhen() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"from-where-city\">From Where:</label>
<div class=\"controls\">
<div class=\"input-append input-prepend\">
" . $display->getFromWhere() . "
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"to-where-city\">To Where:</label>
<div class=\"controls\">
<div class=\"input-append input-prepend\">
" . $display->getToWhere() . "
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"why\">Why:</label>
<div class=\"controls\">
" . $display->getWhy() . "
</div>
</div>
</section>

";

//Display the trip details section
	echo "<section class=\"step\">
<header>
<h2>Trip Details</h2>
<h3>Tell us a little bit about your trip.</h3>
<h4 class=\"step\">2</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\" for=\"males\">Joining me will be:</label>
<div class=\"controls\">
<div class=\"input-append input-prepend\">
" . $display->getMales() . "
<span class=\"add-on\">male(s) and</span>
" . $display->getFemales() . "
<span class=\"add-on\">female(s)</span>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"time\">Please take me within:</label>
<div class=\"controls\">
<div class=\"input-append\">
" . $display->getMinutesWithin() . "
<span class=\"add-on\">minute(s) of my destination</span>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"reimburse\">I can pay for:</label>
<div class=\"controls\">
<div class=\"input-prepend input-append\">
<span class=\"add-on\">\$</span>
" . $display->getGasMoney() . "
<span class=\"add-on\">.00 of gas</span>
</div>
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\">I'll be bringing luggage:</label>
<div class=\"controls\">
" . $display->getLuggage() . "
</div>
</div>
</section>

";

//Display the trip recurrence section
	echo "<section class=\"step stripe\">
<header>
<h2>Trip Recurrence (Optional)</h2>
<h3>This section is probably best for commuters. Do you make this trip often, and would like to arrange a regular pick up schedule?</h3>
<h4 class=\"step\">3</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\">I need a ride regularly:</label>
<div class=\"controls\">
" . $display->getRecurrence() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\">I need a ride every:</label>
<div class=\"controls\">
" . $display->getRecurrenceDays() . "
</div>
</div>

<div class=\"control-group\">
<label class=\"control-label\" for=\"until\">Until:</label>
<div class=\"controls\">
" . $display->getEndDate() . "
</div>
</div>
</section>

";

//Display the comments section
	echo "<section class=\"step\">
<header>
<h2>Closing Thoughts (Optional)</h2>
<h3>Is there anything you'd like to share with your driver?</h3>
<h4 class=\"step\">4</h4>
</header>

<div class=\"control-group\">
<label class=\"control-label\">Comments:</label>
<div class=\"controls\">
" . $display->getComments() . "
</div>
</div>
</section>

";

//Display the comments section
	echo "<section class=\"step stripe\">
<header>
<h2>The Fine Print</h2>
<h3>... but we won't make ours so small that you can't read it.</h3>
<h4 class=\"step\">5</h4>
</header>

<p>This service provided by the Student Government Association at Grove City College is strictly intended to serve as a convenient service to Grove City College attendees. Neither the Student Government Association nor Grove City College can be responsible for any property damage, personal injury, or further inconveniences which may result from sharing a ride with another student or faculty member. It is solely your responsiblity to take any necessary steps before and during the trip to prevent property damage and/or personal injury.</p>
</section>

";

//Display the submit button
	echo "<section class=\"no-border step stripe\">
<button class=\"btn btn-warning\" type=\"submit\">Agree<span class=\"collapse\"> to Terms</span> &amp; Submit<span class=\"collapse\"> Request</span></button>
<a class=\"btn\" href=\"" . $essentials->friendlyURL("") . "\">Cancel</a>
</section>
</form>";
?>