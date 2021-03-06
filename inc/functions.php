<?php
function getItem($item, $table, $attr, $value, $connect) {
	$getItem = "SELECT $item FROM $table WHERE $attr = '$value'";
	return mysqli_query($connect, $getItem)->fetch_object()->$item;
}

function hasNoDuplicates(array $checkArray) {
  return count($checkArray) === count(array_flip($checkArray));
}

function hasItStarted($connect) {
  $dateNow = dateNow();
  $timeNow = timeNow();
  $startDate = startDate($connect);
  $startTime = startTime($connect);
  if ($dateNow > $startDate || ($dateNow == $startDate && $timeNow >= $startTime)){
    return true;
  } else {
		return false;
	}
}

function hasGPStarted($gp_id, $connect) {
  $dateNow = dateNow();
  $timeNow = timeNow();
	$startDate = getItem('startDate', 'gps', 'id', $gp_id, $connect);
	$startTime = getItem('startTime', 'gps', 'id', $gp_id, $connect);
	if ($dateNow > $startDate || ($dateNow == $startDate && $timeNow >= $startTime)){
    return true;
  } else {
  	return false;
  }
}

function hasGPFinished($gp_id, $connect) {
	$check = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM riders_results WHERE gp_id = $gp_id AND podium = 1 LIMIT 1"));
	if ($check) {
		return true;
	} else {
		return false;
	}
}

function gpsFinishedAmount($connect) {
	include 'static_queries.php';
	$amount = 0;
	foreach ($gpsClosed as $gp) {
		if (hasGPFinished($gp['id'], $connect)) {
			$amount++;
		}
	}
	return $amount;
}

function getSurName($fullName) {
	return substr($fullName, strrpos($fullName, ' '));
}

function isAdmin($user_id, $connect) {
	return getItem('admin', 'users', 'id', $user_id, $connect);
}

function dateCreated($user_id, $connect) {
	return getItem('date_created', 'users', 'id', $user_id, $connect);
}

function hasPaid($user_id, $connect) {
  return getItem('payment', 'users', 'id', $user_id, $connect);
}

function isValid($user_id, $connect) {
	if (hasPaid($user_id, $connect)) {
		return true;
	} else {
		return false;
	}
}

function validUsers($connect) {
	include 'static_queries.php';
	$amount = 0;
	foreach ($users as $user){
		if (isValid($user['id'], $connect)) {
			$amount++;
		}
	}
	return $amount;
}

function getUserName($user_id, $connect) {
  $user = mysqli_query($connect, "SELECT first_name, last_name FROM users WHERE id = $user_id")->fetch_array();
  $userName = $user['first_name']." ".$user['last_name'];
  return $userName;
}

function getUserNameShort($user_id, $connect) {
  $user = mysqli_query($connect, "SELECT first_name, last_name FROM users WHERE id = $user_id")->fetch_array();
  $userNameShort = $user['first_name']." ".$user['last_name'][0];
  return utf8_decode($userNameShort);
}

function getUserPicksInGP($user_id, $gp_id, $connect) {
	$picks = mysqli_query($connect, "SELECT pick_1, pick_2, pick_3 FROM users_picks WHERE user_id = $user_id AND gp_id = $gp_id")->fetch_array();
	if ($picks) {
		$picksArray = array($picks['pick_1'], $picks['pick_2'], $picks['pick_3']);
		$picksArray = array_filter($picksArray);
		return $picksArray;
	}
	return array();
}

function calculateUserResultsInGP($user_id, $gp_id, $connect) {
	include 'static_queries.php';
	$points = 0;
	$count1 = 0;
	$count2 = 0;
	$count3 = 0;
	$races = 0;
	$userPicks = getUserPicksInGP($user_id, $gp_id, $connect);
	foreach ($userPicks as $pick) {
		$riderResultsInGP = getRiderResultsInGP($pick, $gp_id, $connect);
		$points += $riderResultsInGP[0];
		switch ($riderResultsInGP[1]) {
			case 1:
				$count1++;
				break;
			case 2:
				$count2++;
				break;
			case 3:
				$count3++;
				break;
			default:
				break;
		}
		$races += $riderResultsInGP[2];
	}
	return array($points, $count1, $count2, $count3, $races);
}

function getUserResultsInGP($user_id, $gp_id, $connect) {
	$results = mysqli_query($connect, "SELECT * FROM users_results WHERE user_id = $user_id AND gp_id = $gp_id")->fetch_array();
	return array($results['points'], $results['p_1'], $results['p_2'], $results['p_3'], $results['races'], $results['position']);
}

function getUserGPsRaced($user_id, $connect) {
	return mysqli_query($connect, "SELECT COUNT(*) AS gps_raced FROM users_results WHERE user_id = $user_id AND races > 0")->fetch_object()->gps_raced;
}

function getUsersAmountInGP($gp_id, $connect) {
	return mysqli_query($connect, "SELECT COUNT(*) AS amount FROM users_results WHERE gp_id = $gp_id")->fetch_object()->amount;
}

function getUserResultsTotal($user_id, $connect) {
	$results = mysqli_query($connect, "SELECT * FROM users_standings WHERE user_id = $user_id")->fetch_array();
	return array($results['points'], $results['p_1'], $results['p_2'], $results['p_3'], $results['races'], $results['position'], $results['prev_position']);
}

function getRidersResultsInGP($gp_id, $connect) {
	return mysqli_query($connect, "SELECT * FROM riders_results WHERE gp_id = $gp_id ORDER BY points DESC, podium = 0, podium, races DESC, rider_id");
}

function getRiderResultsInGP($rider_id, $gp_id, $connect) {
	$results = mysqli_query($connect, "SELECT * FROM riders_results WHERE rider_id = $rider_id AND gp_id = $gp_id")->fetch_array();
	return array($results['points'], $results['podium'], $results['races']);
}

function getRiderPickersInGP($rider_id, $gp_id, $connect) {
	return mysqli_query($connect, "SELECT p.user_id, points, position FROM users_picks p, users_standings s WHERE p.user_id = s.user_id AND gp_id = $gp_id AND (pick_1 = $rider_id OR pick_2 = $rider_id OR pick_3 = $rider_id) ORDER BY position, p.id");
}

function getRiderTimesPickedInGP($rider_id, $gp_id, $connect) {
	return mysqli_query($connect, "SELECT COUNT(*) AS times_picked FROM users_picks WHERE gp_id = $gp_id AND (pick_1 = $rider_id OR pick_2 = $rider_id OR pick_3 = $rider_id)")->fetch_object()->times_picked;
}

function getRiderTimesPickedTotal($rider_id, $connect) {
	return mysqli_query($connect, "SELECT COUNT(*) AS times_picked FROM users_picks, gps WHERE gp_id = gps.id AND (pick_1 = $rider_id OR pick_2 = $rider_id OR pick_3 = $rider_id) AND (startDate < CURRENT_DATE() OR (startDate = CURRENT_DATE() AND startTime < CURRENT_TIME()))")->fetch_object()->times_picked;
}

function getRiderPointsTotal($rider_id, $connect) {
	return mysqli_query($connect, "SELECT SUM(points) AS points_total FROM riders_results WHERE rider_id = $rider_id")->fetch_object()->points_total;
}

function getUsersAmountInRiderGPs($rider_id, $connect) {
	$usersAmount = 0;
	$gps = mysqli_query($connect, "SELECT gp_id FROM riders_results WHERE rider_id = $rider_id");
	foreach ($gps as $gp) {
		$usersAmount += getUsersAmountInGP($gp['gp_id'], $connect);
	}
	return $usersAmount;
}

function getRiderGPsRaced($rider_id, $connect) {
	return mysqli_query($connect, "SELECT COUNT(*) AS gps_raced FROM riders_results WHERE rider_id = $rider_id AND races > 0")->fetch_object()->gps_raced;
}

function getRiderRaces($rider_id, $connect) {
	$races = mysqli_query($connect, "SELECT SUM(races) AS races FROM riders_results WHERE rider_id = $rider_id")->fetch_object()->races;
	if ($races) {
		return $races;
	} else {
		return 0;
	}
}

function getRiderResultsTotal($rider_id, $connect) {
	$points = getRiderPointsTotal($rider_id, $connect);
	$races = getRiderRaces($rider_id, $connect);
	$gpsRaced = getRiderGPsRaced($rider_id, $connect);
	$timesPicked = getRiderTimesPickedTotal($rider_id, $connect);
	if (!$points) { $points = 0; }
	$results = array();
	$results[0] = $points;
	for ($i=1; $i<=3; $i++) {
		$results[$i] = mysqli_query($connect, "SELECT COUNT(*) AS podium FROM riders_results WHERE rider_id = $rider_id AND podium = $i")->fetch_object()->podium;
	}
	$results[4] = $races;
	$results[5] = $gpsRaced;
	$results[6] = $timesPicked;
	return $results;
}

function getUsersResultsInGP($gp_id, $connect) {
	return mysqli_query($connect, "SELECT r.user_id, r.points, r.p_1, r.p_2, r.p_3, r.races, r.position, s.points AS total FROM users_results r, users_standings s WHERE r.user_id = s.user_id AND gp_id = $gp_id ORDER BY r.position, s.position");
}

function getUsersResultsInGPTopThree($gp_id, $connect) {
	return mysqli_query($connect, "SELECT r.user_id, r.points, r.p_1, r.p_2, r.p_3, r.races, r.position FROM users_results r, users_standings s WHERE r.user_id = s.user_id AND gp_id = $gp_id AND r.position <= 3 ORDER BY r.position, s.position");
}

function updateUserResultsInGP($user_id, $gp_id, $points, $podium, $connect) {
	$p_1 = 0;
	$p_2 = 0;
	$p_3 = 0;
	if ($podium) {
		switch ($podium) {
			case 1:
				$p_1 = 1;
				break;
			case 2:
				$p_2 = 1;
				break;
			case 3:
				$p_3 = 1;
				break;
			default:
				break;
		}
	}
	$checkUser = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users_results WHERE user_id = $user_id AND gp_id = $gp_id LIMIT 1"));
	if ($checkUser) {
		mysqli_query($connect, "UPDATE users_results SET points = points + $points, p_1 = p_1 + $p_1, p_2 = p_2 + $p_2, p_3 = p_3 + $p_3, races = races + 1 WHERE user_id = $user_id AND gp_id = $gp_id");
	} else {
		mysqli_query($connect, "INSERT INTO users_results (user_id, gp_id, points, p_1, p_2, p_3, races) VALUES ($user_id, $gp_id, $points, $p_1, $p_2, $p_3, 1)");
	}
	mysqli_query($connect, "UPDATE users_standings SET points = points + $points, p_1 = p_1 + $p_1, p_2 = p_2 + $p_2, p_3 = p_3 + $p_3, races = races + 1 WHERE user_id = $user_id");
}

function updateUsersResultsInGP($gp_id, $connect) {
	include 'static_queries.php';
	foreach ($users as $user) {
		$user_id = $user['id'];
		if (isValid($user_id, $connect)) {
			$results = calculateUserResultsInGP($user_id, $gp_id, $connect);
			$checkUser = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users_results WHERE user_id = $user_id AND gp_id = $gp_id LIMIT 1"));
			if ($checkUser) {
				mysqli_query($connect, "UPDATE users_results SET points = $results[0], p_1 = $results[1],	p_2 = $results[2], p_3 = $results[3], races = $results[4] WHERE user_id = $user_id");
			} else {
				mysqli_query($connect, "INSERT INTO users_results (user_id, gp_id, points, p_1, p_2, p_3, races) VALUES ($user_id, $gp_id, $results[0], $results[1], $results[2], $results[3], $results[4])");
			}
		}
	}
	setUsersPositionsInGP($gp_id, $connect);
	// setRidersPositionsInGP($gp_id, $connect);
}

function setUsersPositionsInGP($gp_id, $connect) {
	$position = 0;
	$count = 0;
	$prevResults = array();
	$table = mysqli_query($connect, "SELECT * FROM users_results WHERE gp_id = $gp_id ORDER BY points DESC, p_1 DESC, p_2 DESC, p_3 DESC, races DESC, user_id");
	while ($user = mysqli_fetch_array($table)) {
		$user_id = $user['user_id'];
		$count++;
		$userResults = array($user['points'], $user['p_1'], $user['p_2'], $user['p_3'], $user['races']);
		if ($userResults != $prevResults){
			$position = $count;
		}
		$updatePosition = "UPDATE users_results SET position = $position WHERE user_id = $user_id AND gp_id = $gp_id";
		mysqli_query($connect, $updatePosition);
		$prevResults = $userResults;
	}
}

// function setRidersPositionsInGP($gp_id, $connect) {
// 	$position = 0;
// 	$table = mysqli_query($connect, "SELECT *  FROM riders_results WHERE gp_id = $gp_id ORDER BY podium = 0, points DESC, podium, rider_id");
// 	while ($rider = mysqli_fetch_array($table)) {
// 		$rider_id = $rider['rider_id'];
// 		$position++;
// 		$updatePosition = "UPDATE riders_results SET position = $position WHERE rider_id = $rider_id AND gp_id = $gp_id";
// 		mysqli_query($connect, $updatePosition);
// 	}
// }

function calculateUserResultsTotal($user_id, $connect) {
	include 'static_queries.php';
	$points = 0;
	$count1 = 0;
	$count2 = 0;
	$count3 = 0;
	$races = 0;
	foreach ($gpsClosed as $gp) {
		$gp_id = $gp['id'];
		$resultInGP = calculateUserResultsInGP($user_id, $gp_id, $connect);
		$points += $resultInGP[0];
		$count1 += $resultInGP[1];
		$count2 += $resultInGP[2];
		$count3 += $resultInGP[3];
		$races += $resultInGP[4];
	}
	return array($points, $count1, $count2, $count3, $races);
}

function updateStandings($connect) {
	include 'static_queries.php';
	foreach ($users as $user) {
		$user_id = $user['id'];
		if (isValid($user_id, $connect)) {
			$resultsTotal = calculateUserResultsTotal($user_id, $connect);
			$updateUsersPoints = "UPDATE users_standings SET
				points = $resultsTotal[0],
				p_1 = $resultsTotal[1],
				p_2 = $resultsTotal[2],
				p_3 = $resultsTotal[3],
				races = $resultsTotal[4]
				WHERE user_id = $user_id";
			mysqli_query($connect, $updateUsersPoints);
		}
	}
	setStandingsPositions($connect);
}

function setStandingsPositions($connect) {
	include 'static_queries.php';
	$position = 0;
	$count = 0;
	$prevResults = array();
	while ($user = mysqli_fetch_array($standings[0])) {
		$user_id = $user['user_id'];
		$count++;
		$userResults = getUserResultsTotal($user_id, $connect);
		array_pop($userResults); //removes last item in array (Position)
		if ($userResults != $prevResults){
			$position = $count;
		}
		$updatePosition = "UPDATE users_standings SET position = $position WHERE user_id = $user_id";
		mysqli_query($connect, $updatePosition);
		$prevResults = $userResults;
	}
}

function setPreviousPositions($connect) {
	include 'static_queries.php';
	foreach ($standings[0] as $user) {
		$user_id = $user['user_id'];
		$position = $user['position'];
		mysqli_query($connect, "UPDATE users_standings SET prev_position = $position WHERE user_id = $user_id");
	}
}
