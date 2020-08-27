<?php
$user_id = $loggedInUser;

if (isset($_POST['insert_picks'])) {
  $gp_id = $_POST['gp_id'];
  $pick_1 = $_POST['pick_1'];
  $pick_2 = $_POST['pick_2'];
  $pick_3 = $_POST['pick_3'];
  if ($pick_1 == 0 && $pick_2 == 0 && $pick_3 == 0) { array_push($gpErrors[$gp_id], "You didn't pick any riders"); }
  $picksArray = array($pick_1, $pick_2, $pick_3);
  $picksArray = array_filter($picksArray);
  if (!hasDuplicates($picksArray)) { array_push($gpErrors[$gp_id], "You can't pick the same rider twice"); }
  if (!$gpErrors[$gp_id] && !hasGPStarted($gp_id, $connect)) {
    $insertPicks = "INSERT INTO users_picks (user_id, gp_id, pick_1, pick_2, pick_3) VALUES ('$user_id', '$gp_id', '$pick_1', '$pick_2', '$pick_3')";
    mysqli_query($connect, $insertPicks);
    array_push($gpSuccesses[$gp_id], "Your picks were successfully saved");
  }
}

if (isset($_POST['update_picks'])) {
  $gp_id = $_POST['gp_id'];
  $pick_1 = $_POST['pick_1'];
  $pick_2 = $_POST['pick_2'];
  $pick_3 = $_POST['pick_3'];
  $picksArray = array($pick_1, $pick_2, $pick_3);
  $picksArray = array_filter($picksArray);
  if (!hasDuplicates($picksArray)) { array_push($gpErrors[$gp_id], "You can't pick the same rider twice"); }
  if (!$gpErrors[$gp_id] && !hasGPStarted($gp_id, $connect)) {
    $updatePicks = "UPDATE users_picks SET pick_1 = '$pick_1', pick_2 = '$pick_2', pick_3 = '$pick_3' WHERE user_id = '$user_id' AND gp_id = '$gp_id'";
    mysqli_query($connect, $updatePicks);
    array_push($gpSuccesses[$gp_id], "Your picks were successfully updated");
  }
}
