<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "inc/connect_db_live.php";
include 'inc/config.php';
include 'inc/static_queries.php';
include 'inc/functions.php';
include 'inc/account_backend.php';

if (isset($_COOKIE["user_id"])) {
  $loggedInUser = $_COOKIE["user_id"];
} elseif (isset($_SESSION['user_id'])) {
  $loggedInUser = $_SESSION['user_id'];
} else {
  $loggedInUser = 0;
}

$validUsers = validUsers($connect); ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?=$eventTitle?> – <?=$pageTitle?></title>
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="shortcut icon" href="/graphics/favicon.ico" type="image/x-icon">
    <link rel="ICON" href="/graphics/favicon.ico">
    <link rel="manifest" href="/manifest.json">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Facebook and Twitter integration -->
    <meta name="title" property="og:title" content="<?=$eventTitle?>">
    <meta name="type" property="og:type" content="website">
    <meta name="image" property="og:image" content="https://betsgp.com/graphics/sgp_logo_3.png">
    <meta name="image_type" property="og:image:type" content="image/png">
    <meta name="image_width" property="og:image:width" content="">
    <meta name="image_height" property="og:image:height" content="">
    <meta name="author" content="Albin Lindeborg">
    <meta name="url" property="og:url" content="<?=$siteURL?>">
    <meta name="site_name" property="og:site_name" content="<?=$eventTitle?>">
    <meta name="description" property="og:description" content="Bet on the 2020 Speedway Grand Prix">
    <meta name="facebook" property="fb:app_id" content="fapp">
    <meta name="twitter:title" content="<?=$eventTitle?>">
    <meta name="twitter:image" content="<?=$siteURL?>/graphics/sgp_logo_3.png">
    <meta name="twitter:url" content="">
    <meta name="twitter:card" content="">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-127763376-5"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-127763376-5');
    </script>
  </head>
  <body>
    <div class="container text-uppercase">
      <nav class="navbar navbar-inverse">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand hidden-lg" href="index.php">
              <span><img src="graphics/sgp_logo.png" style="max-height: 100%;"></span>
            </a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a class="navbar-brand hidden-md hidden-sm hidden-xs" href="index.php"><img src="graphics/sgp_logo.png" style="max-height: 100%;" /></a></li>
              <li class="nav-button active"><a href="/index.php"><span class="glyphicon glyphicon-home"></span></a></li>
              <li class="nav-button"><a href="/standings.php">Standings</a></li>
              <li class="nav-button"><a href="/stats.php">Stats</a></li>
              <li class="nav-button"><a href="/gps.php">GP's</a></li>
              <li class="nav-button"><a href="/riders.php">Riders</a></li>
              <li class="nav-button"><a href="/users.php">Participants</a></li>
              <li class="nav-button"><a href="/comments.php">Comments</a></li>
              <li class="nav-button"><a href="/rules.php">Rules</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <?php if (!$loggedInUser) : ?>
                <li class="btn-success"><a href="/account.php" style="color: white;"><span class="glyphicon glyphicon-log-in"></span> Sign In <?=(!hasItStarted($connect)) ? '/ Register ' : null ?></a></li>
              <?php else: ?>
                <li class="btn-success"><a href="/users.php?id=<?=$loggedInUser?>" style="color: white;"><span class="glyphicon glyphicon-stats"></span> My Page</a></li>
                <li class="btn-danger"><a href="/index.php?signout=1" style="color: white;"><span class="glyphicon glyphicon-log-out"></span> Sign Out</a></li>
              <?php endif ?>
            </ul>
          </div>
        </div>
      </nav>
    </div>

    <div class="container">
