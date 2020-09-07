<?php
session_start();

// initializing variables
$first_name = "";
$last_name = "";
$email = "";
$errors = array();
$successes = array();
$infos = array();
$gpSuccesses = array();
$gpErrors = array();
for ($i=1; $i<=$gpsAmount; $i++) {
  $gpSuccesses[$i] = array();
  $gpErrors[$i] = array();
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
  $first_name = mysqli_real_escape_string($connect, $_POST['first_name']);
  $first_name = strtolower($first_name);
  $first_name = ucfirst($first_name);
  $last_name = mysqli_real_escape_string($connect, $_POST['last_name']);
  $last_name = strtolower($last_name);
  $last_name = ucfirst($last_name);
  $email = mysqli_real_escape_string($connect, $_POST['email']);
  $email = strtolower($email);
  $password = mysqli_real_escape_string($connect, $_POST['password']);
  if (empty($first_name)) { array_push($errors, "First name is required"); }
  if (empty($last_name)) { array_push($errors, "Last name is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password)) { array_push($errors, "Password is required"); }
  // if ($password_1 != $password_2) { array_push($errors, "The two passwords do not match"); }

  $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users WHERE first_name = '$first_name' AND last_name = '$last_name' OR email = '$email' LIMIT 1"));
  if ($user) { // if user exists
    if ($user['first_name'] === $first_name && $user['last_name'] === $last_name) {
      array_push($errors, "Name ".$first_name." ".$last_name." is already taken");
    }
    if ($user['email'] === $email) {
      array_push($errors, "Email ".$email." already exists");
    }
  }
  if (!$errors) {
    $password = md5($password);
    mysqli_query($connect, "INSERT INTO users (first_name, last_name, email, password, payment)
    VALUES('$first_name', '$last_name', '$email', '$password', 1)");
    $user_id = mysqli_query($connect, "SELECT id FROM users WHERE email = '$email'")->fetch_object()->id;
    mysqli_query($connect, "INSERT INTO users_standings (user_id, points, p_1, p_2, p_3, races) VALUES ($user_id, 0, 0, 0, 0, 0)");
    $userName = $first_name.' '.$last_name;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $userName;
    if (!empty($_POST['remember'])) {
      setcookie('user_id', $user_id, time() + (10 * 365 * 24 * 60 * 60));
    } else {
      setcookie('user_id', "", time() - 3600);
    }
    array_push($successes, "You are now registered and signed in as ".$userName);
    array_push($infos, "You can now edit your picks, find the link in the menu above");
    // array_push($infos, "Make sure to process your payment before the start of the event");
  }
}

// SIGN IN USER
if (isset($_POST['signin_user'])) {
  $email = mysqli_real_escape_string($connect, $_POST['email']);
  $password = mysqli_real_escape_string($connect, $_POST['password']);
  if (!$email) { array_push($errors, "Email is required"); }
  if (!$password) { array_push($errors, "Password is required"); }
  if (!$errors) {
    $password = md5($password);
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1"));
    if ($user) {
      $user_id = $user['id'];
      $userName = $user['first_name'].' '.$user['last_name'];
      $_SESSION['user_id'] = $user_id;
      $_SESSION['user_name'] = $userName;
      if (!empty($_POST['remember'])) {
        setcookie('user_id', $user_id, time() + (10 * 365 * 24 * 60 * 60));
      } else {
        setcookie('user_id', "", time() - 3600);
      }
      array_push($successes, "You are now signed in as ".$userName);
      // if ($fields['payment']) {
      //   array_push($successes, "Your payment is confirmed");
      // } else {
      //   array_push($errors, "Your payment is not yet confirmed");
      // }
    } else {
      array_push($errors, "Wrong username/password combination");
    }
  }
}

// Forgot Password
if (isset($_POST['forgot_password'])) {
  $email = mysqli_real_escape_string($connect, $_POST['email']);
  if (!$email) {
    array_push($errors, "Enter your email");
  } else {
    $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users WHERE email='$email' LIMIT 1"));
    if (!$user) {
      array_push($errors, "Email is not associated with any account");
    }
  }
  if (!$errors) {
    $uniqidStr = md5(uniqid(mt_rand()));
    mysqli_query($connect, "UPDATE users SET fp_code = '$uniqidStr' WHERE email = '$email'");
    $resetPassLink = $siteURL.'/account_forgotpassword.php?fp_code='.$uniqidStr;
    $to = $email;
    $from = 'From: '.$eventTitle;
    $subject = 'Forgot Password';
    $message = "Reset my password using this link: ".$resetPassLink;
    $mail = mail($to, $subject, $message, $from);
    if ($mail) { array_push($successes, "Password recovery email sent to ".$to.", don't forget to check your spam folder"); }
  }
}

// Update Password:
if (isset($_POST['update_password'])) {
  $password = mysqli_real_escape_string($connect, $_POST['password']);
  $user_id = $_POST['user_id'];
  if (!$password) { array_push($errors, "Password is required"); }
  if (!$errors) {
    $password = md5($password);
    mysqli_query($connect, "UPDATE users SET password = '$password' WHERE id = '$user_id'");
    array_push($successes, "Password updated");
  }
}

// Sign Out User
if (isset($_GET['signout'])) {
  session_destroy();
  unset($_SESSION['user_id']);
  setcookie('user_id', "", time() - 3600);
  header("location: index.php");
}
