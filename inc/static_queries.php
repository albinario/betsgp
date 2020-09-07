<?php
$users = mysqli_query($connect, "SELECT * FROM users ORDER BY id");
$usersAlphabetical = mysqli_query($connect, "SELECT * FROM users ORDER BY first_name");

$gpsClosed = mysqli_query($connect, "SELECT * FROM gps WHERE startDate < CURRENT_DATE() OR (startDate = CURRENT_DATE() AND startTime < CURRENT_TIME()) ORDER BY id DESC");
$gpsUpcoming = mysqli_query($connect, "SELECT * FROM gps WHERE startDate > CURRENT_DATE() OR (startDate = CURRENT_DATE() AND startTime > CURRENT_TIME()) ORDER BY id");
$lastGP = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM gps WHERE startDate < CURRENT_DATE() OR (startDate = CURRENT_DATE() AND startTime < CURRENT_TIME()) ORDER BY id DESC LIMIT 1"));
$nextGP = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM gps WHERE startDate > CURRENT_DATE() OR (startDate = CURRENT_DATE() AND startTime > CURRENT_TIME()) ORDER BY id LIMIT 1"));
$gpsAmount = mysqli_num_rows($gpsClosed)+mysqli_num_rows($gpsUpcoming);

$riders = mysqli_query($connect, "SELECT * FROM riders WHERE substitute = 0 ORDER BY id");
$subRiders = mysqli_query($connect, "SELECT * FROM riders WHERE substitute = 1 ORDER BY id");
$allRiders = mysqli_query($connect, "SELECT * FROM riders ORDER BY name");
$cities = mysqli_query($connect, "SELECT * FROM cities ORDER BY id");
$nations = mysqli_query($connect, "SELECT * FROM nations ORDER BY id");

$standings = array();
$standings[0] = mysqli_query($connect, "SELECT * FROM users_standings ORDER BY points = 0, points DESC, p_1 DESC, p_2 DESC, p_3 DESC, races DESC, position, user_id");
$standings[1] = mysqli_query($connect, "SELECT * FROM users_standings ORDER BY points = 0, p_1 DESC, position, user_id");
$standings[2] = mysqli_query($connect, "SELECT * FROM users_standings ORDER BY points = 0, p_2 DESC, position, user_id");
$standings[3] = mysqli_query($connect, "SELECT * FROM users_standings ORDER BY points = 0, p_3 DESC, position, user_id");
$standings[4] = mysqli_query($connect, "SELECT * FROM users_standings ORDER BY points = 0, races DESC, position, user_id");
$standingsTopTen = mysqli_query($connect, "SELECT * FROM users_standings ORDER BY points = 0, position, user_id LIMIT 10");
?>
