<?php
//contact form that is sent to administrator
$name = Input::get('name');
$email = Input::get('email');
$message = Input::get('message');
$date_time = date("F j, Y, g:i a");
$userIpAddress = Request::getClientIp();
?> 

<h1>We been contacted by.... </h1>

<p>
Name: <?php echo ($name); ?> <br>
Email address: <?php echo ($email);?> <br>
Message: <?php echo ($message);?><br>
Date: <?php echo($date_time);?><br>
User IP address: <?php echo($userIpAddress);?><br>

</p>