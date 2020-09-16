<?php

if (isset($_POST['update_standings'])) {
  updateStandings($connect);
  array_push($successes, "Standings updated and sorted");
}

if (isset($_POST['report_race'])) {
  $gp_id = $_POST['gp_id'];
  for ($i=1; $i<=4; $i++) {
    $rider_id = $_POST['rider_id_'.$i];
    $points = $_POST['points_'.$i];
    $podium = 0;
    $checkRiderQuery = "SELECT * FROM riders_results WHERE rider_id = '$rider_id' AND gp_id = '$gp_id' LIMIT 1";
    $checkRider = mysqli_fetch_assoc(mysqli_query($connect, $checkRiderQuery));
    if ($rider_id) {
      if (!$checkRider) {
        $insertRiderPoints = "INSERT INTO riders_results (rider_id, gp_id, points, races) VALUES ('$rider_id', '$gp_id', '$points', 1)";
        mysqli_query($connect, $insertRiderPoints);
      } else {
        if ($_POST['final']) {
          if ($points == 3) {
            $podium = 1;
          } elseif ($points == 2) {
            $podium = 2;
          } elseif ($points == 1) {
            $podium = 3;
          }
        }
        $updatePodium = '';
        if ($podium) {
          $updatePodium = ", podium = '$podium'";
        }
        $updateRiderPoints = "UPDATE riders_results SET points = points + '$points', races = races + 1".$updatePodium." WHERE rider_id = '$rider_id' AND gp_id = '$gp_id'";
        mysqli_query($connect, $updateRiderPoints);
        // array_push($successes, "Rider result updated");
      }
      $users = mysqli_query($connect, "SELECT user_id FROM users_picks WHERE gp_id = $gp_id AND (pick_1 = $rider_id OR pick_2 = $rider_id OR pick_3 = $rider_id)");
      foreach ($users as $user) {
        $user_id = $user['user_id'];
        updateUserResultsInGP($user_id, $gp_id, $points, $podium, $connect);
      }
    }
  }
  if ($_POST['first']) { setPreviousPositions($connect); }
  setUsersPositionsInGP($gp_id, $connect);
  setStandingsPositions($connect);
  array_push($successes, "Race is in the books");
}

if (isset($_POST['rider_result'])) {
  $rider_id = $_POST['rider_id'];
  $gp_id = $_POST['gp_id'];
  $points = $_POST['points'];
  $podium = $_POST['podium'];
  if (!$rider_id) { array_push($errors, "Rider not selected"); }
  if (!$gp_id) { array_push($errors, "GP not selected"); }
  $checkRiderQuery = "SELECT * FROM riders_results WHERE rider_id = '$rider_id' AND gp_id = '$gp_id' LIMIT 1";
  $checkRider = mysqli_fetch_assoc(mysqli_query($connect, $checkRiderQuery));
  if (!$errors) {
    if (!$checkRider) {
      $insertRiderPoints = "INSERT INTO riders_results (rider_id, gp_id, points, podium) VALUES ('$rider_id', '$gp_id', '$points', '$podium')";
      mysqli_query($connect, $insertRiderPoints);
      array_push($successes, "Rider result added");
    } else {
      $updateRiderPoints = "UPDATE riders_results SET points = '$points', podium = '$podium' WHERE rider_id = '$rider_id' AND gp_id = '$gp_id'";
      mysqli_query($connect, $updateRiderPoints);
      array_push($successes, "Rider result updated");
    }
  }
  if ($podium == 1) {
    updateUsersResultsInGP($gp_id, $connect);
    array_push($successes, 'GP updated and sorted');
    updateStandings($connect);
    array_push($successes, "Standings updated and sorted");
  }
}

if (isset($_POST['payment_confirm'])) {
  $user_id = $_POST['user_id'];
  if (!$user_id) { array_push($errors, "User not selected"); }
  if (!$errors) {
    $userName = getUserName($user_id, $connect);
    $updatePayment = "UPDATE users SET payment = 1 WHERE id = '$user_id'";
    mysqli_query($connect, $updatePayment);
    array_push($successes, "Payment is now confirmed for $userName");
  }
}

if (isset($_POST['payment_revoke'])) {
  $user_id = $_POST['user_id'];
  if (!$user_id) { array_push($errors, "User not selected"); }
  if (!$errors) {
    $userName = getUserName($user_id, $connect);
    $updatePayment = "UPDATE users SET payment = 0 WHERE id = '$user_id'";
    mysqli_query($connect, $updatePayment);
    array_push($successes, "Payment is now revoked for $userName");
  }
}

if (isset($_POST['admin_grant'])) {
  $user_id = $_POST['user_id'];
  if (!$user_id) { array_push($errors, "User not selected"); }
  if (!$errors) {
    $userName = getUserName($user_id, $connect);
    $updateAdmin = "UPDATE users SET admin = 1 WHERE id = '$user_id'";
    mysqli_query($connect, $updateAdmin);
    array_push($successes, "Admin access for $userName is now granted");
  }
}

if (isset($_POST['admin_revoke'])) {
  $user_id = $_POST['user_id'];
  if (!$user_id) { array_push($errors, "User not selected"); }
  if (!$errors) {
    $userName = getUserName($user_id, $connect);
    $updateAdmin = "UPDATE users SET admin = NULL WHERE id = '$user_id'";
    mysqli_query($connect, $updateAdmin);
    array_push($successes, "Admin access for $userName is now revoked");
  }
}

if (isset($_POST['update_rider_name'])) {
  $rider_id = $_POST['rider_id'];
  $new_rider_name = mysqli_real_escape_string($connect, $_POST['new_rider_name']);
  if (!$rider_id) { array_push($errors, "Rider not selected"); }
  if (!$new_rider_name) { array_push($errors, "No new name entered"); }
  if (!$errors) {
    $updateRider = "UPDATE riders SET name = '$new_rider_name' WHERE id = '$rider_id'";
    mysqli_query($connect, $updateRider);
    array_push($successes, "Rider updated");
  }
}

if (isset($_POST['add_wild_card'])) {
  $city_id = $_POST['city_id'];
  $nation_id = $_POST['nation_id'];
  $wild_card = mysqli_real_escape_string($connect, $_POST['wild_card']);
  if (!$city_id) { array_push($errors, "City not selected"); }
  if (!$nation_id) { array_push($errors, "Nation not selected"); }
  if (!$wild_card) { array_push($errors, "No wild card entered"); }
  if (!$errors) {
    $insertWildCard = "INSERT INTO riders (name, number, nation_id, wc_city_id, substitute, active) VALUES ('$wild_card', '16', '$nation_id', '$city_id', 0, 1)";
    mysqli_query($connect, $insertWildCard);
    array_push($successes, "Wild Card added");
  }
}

if (isset($_POST['activate_substitute'])) {
  $rider_id = $_POST['rider_id'];
  if (!$rider_id) { array_push($errors, "Rider not selected"); }
  if (!$errors) {
    mysqli_query($connect, "UPDATE riders SET substitute = 0 WHERE id = '$rider_id'");
    array_push($successes, "Rider activated");
  }
}

if (isset($_POST['activate_rider'])) {
  $rider_id = $_POST['rider_id'];
  if (!$rider_id) { array_push($errors, "Rider not selected"); }
  if (!$errors) {
    mysqli_query($connect, "UPDATE riders SET active = 1 WHERE id = '$rider_id'");
    array_push($successes, "Rider activated");
  }
}

if (isset($_POST['deactivate_rider'])) {
  $rider_id = $_POST['rider_id'];
  if (!$rider_id) { array_push($errors, "Rider not selected"); }
  if (!$errors) {
    mysqli_query($connect, "UPDATE riders SET active = 0 WHERE id = '$rider_id'");
    array_push($successes, "Rider deactivated");
  }
}
