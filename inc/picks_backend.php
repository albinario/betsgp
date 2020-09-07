<?php
$user_id = $loggedInUser;

if (isset($_POST['submit_picks'])) {
  $gp_id = $_POST['gp_id'];
  $pick_1 = $_POST['pick_1'];
  $pick_2 = $_POST['pick_2'];
  $pick_3 = $_POST['pick_3'];
  if (hasGPStarted($gp_id, $connect)) { array_push($gpErrors[$gp_id], "GP has already started"); }
  if ($pick_1 == 0 && $pick_2 == 0 && $pick_3 == 0) { array_push($gpErrors[$gp_id], "You didn't pick any riders"); }
  $picksArray = array($pick_1, $pick_2, $pick_3);
  $picksArray = array_filter($picksArray);
  if (!hasNoDuplicates($picksArray)) { array_push($gpErrors[$gp_id], "You can't pick the same rider twice"); }
  if (!$gpErrors[$gp_id]) {
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users_picks WHERE user_id = '$user_id' AND gp_id = '$gp_id' LIMIT 1"));
    if (!$user) {
      mysqli_query($connect, "INSERT INTO users_picks (user_id, gp_id, pick_1, pick_2, pick_3) VALUES ('$user_id', '$gp_id', '$pick_1', '$pick_2', '$pick_3')");
      mysqli_query($connect, "INSERT INTO users_results (user_id, gp_id) VALUES ('$user_id', '$gp_id')");
      array_push($gpSuccesses[$gp_id], "Your picks were successfully saved");
    } else {
      mysqli_query($connect, "UPDATE users_picks SET pick_1 = '$pick_1', pick_2 = '$pick_2', pick_3 = '$pick_3' WHERE user_id = '$user_id' AND gp_id = '$gp_id'");
      array_push($gpSuccesses[$gp_id], "Your picks were successfully updated");
    }
  }
}

// if (isset($_POST['update_picks'])) {
//   $gp_id = $_POST['gp_id'];
//   $pick_1 = $_POST['pick_1'];
//   $pick_2 = $_POST['pick_2'];
//   $pick_3 = $_POST['pick_3'];
//   if (hasGPStarted($gp_id, $connect)) { array_push($gpErrors[$gp_id], "GP has already started"); }
//   $picksArray = array($pick_1, $pick_2, $pick_3);
//   $picksArray = array_filter($picksArray);
//   if (!hasNoDuplicates($picksArray)) { array_push($gpErrors[$gp_id], "You can't pick the same rider twice"); }
//   if (!$gpErrors[$gp_id]) {
//     mysqli_query($connect, "UPDATE users_picks SET pick_1 = '$pick_1', pick_2 = '$pick_2', pick_3 = '$pick_3' WHERE user_id = '$user_id' AND gp_id = '$gp_id'");
//     array_push($gpSuccesses[$gp_id], "Your picks were successfully updated");
//   }
// }
