<?php $pageTitle = 'Account';
include "header.php"; ?>

<div class="row">
  <div class="col-sm-6">
    <div class="well">
      <h4>Sign In</h4>
      <?php if (isset($_POST['signin_user']) || (isset($_POST['forgot_password']))) { include 'inc/alerts.php'; }
      if ($loggedInUser) : ?>
        <a href="/users.php?id=<?=$loggedInUser?>" role="button" class="btn btn-success text-uppercase" style="width: 100%; margin-bottom: 15px;"><span class="glyphicon glyphicon-stats"></span> Go to My Page</a>
      <?php endif ?>
      <form method="post" action="">
        <div class="row form-group">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
              <input type="email" class="form-control" placeholder="Email" name="email" <?=($loggedInUser) ? 'disabled' : null ?>>
            </div>
          </div>
          <br class="hidden-md hidden-lg">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
              <input type="password" class="form-control" placeholder="Password" name="password" <?=($loggedInUser) ? 'disabled' : null ?>>
            </div>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-5">
            <div class="input-group">
              <span class="input-group-addon">
                <input type="checkbox" name="remember" <?=($loggedInUser) ? 'disabled' : null ?>>
              </span>
              <input type="text" class="form-control" value="Keep me signed in" style="background-color: #1f1f1f; color: #c5c5c5;" disabled>
            </div>
          </div>
          <br class="hidden-md hidden-lg">
          <div class="col-md-7">
            <button type="submit" class="btn btn-success text-uppercase" style="width: 100%;" name="signin_user" <?=($loggedInUser) ? 'disabled' : null ?>>
              <span class="glyphicon glyphicon-log-in"></span> Sign In
            </button>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6 col-sm-offset-6 col-lg-4 col-lg-offset-8">
            <button type="submit" class="btn btn-warning btn-sm" style="width: 100%;" name="forgot_password" <?=($loggedInUser) ? 'disabled' : null ?>>
              <span class="glyphicon glyphicon-exclamation-sign"></span> Forgot Password
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-sm-6">
    <div class="well">
      <h4>Register for <?=$eventTitle?></h4>
      <?php if (isset($_POST['reg_user'])) { include 'inc/alerts.php'; } ?>
      <form method="post" action="account.php">
        <div class="row form-group">
          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="First Name" name="first_name" <?=($loggedInUser) ? 'disabled' : null ?>>
          </div>
          <br class="hidden-md hidden-lg">
          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Last Name" name="last_name" <?=($loggedInUser) ? 'disabled' : null ?>>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
              <input type="email" class="form-control" placeholder="Email" name="email" <?=($loggedInUser) ? 'disabled' : null ?>>
            </div>
          </div>
          <br class="hidden-md hidden-lg">
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
              <input type="password" class="form-control" placeholder="Password" name="password" <?=($loggedInUser) ? 'disabled' : null ?>>
            </div>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-5">
            <div class="input-group">
              <span class="input-group-addon">
                <input type="checkbox" name="remember" <?=($loggedInUser) ? 'disabled' : null ?>>
              </span>
              <input type="text" class="form-control" value="Keep me signed in" style="background-color: #1f1f1f; color: #c5c5c5;" disabled>
            </div>
          </div>
          <br class="hidden-md hidden-lg">
          <div class="col-md-7">
            <button type="submit" class="btn btn-success text-uppercase" style="width: 100%;" name="reg_user" <?=($loggedInUser) || hasItStarted($connect) ? 'disabled' : null ?>>
              <span class="glyphicon glyphicon-log-in"></span> Register
            </button>
          </div>
        </div>
        <div class="text-right" style="margin-top: 10px;">
          Registration is open until: <?=startDate($connect)?> <?=startTime($connect)?><br/>
          Current time: <?=dateNow()?> <?=timeNow()?>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
