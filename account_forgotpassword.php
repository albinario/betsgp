<?php $pageTitle = "Reset Password";
include "header.php";

if (isset($_GET['fp_code'])) :
  $fp_code = $_GET['fp_code'];
  $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT id FROM users WHERE fp_code = '$fp_code'"));
  $user_id = $user['id']; ?>
  <div class="row">
    <div class="col-sm-4 col-sm-offset-4">
      <div class="well">
        <h4>Change Password</h4>
        <?php if (isset($_POST['update_password'])) { include 'inc/alerts.php'; } ?>
        <form method="post" action="/account_forgotpassword.php?fp_code=<?=$fp_code?>">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input type="text" class="form-control" placeholder="<?=getUserName($user_id, $connect)?>" disabled>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
              <input type="password" class="form-control" placeholder="New Password" name="password">
            </div>
            <input type="hidden" name="user_id" value="<?=$user_id?>">
          </div>
          <button type="submit" class="btn btn-success text-uppercase form-group" style="width: 100%;" name="update_password">
            <span class="glyphicon glyphicon-ok"></span> Update Password
          </button>
        </form>
      </div>
    </div>
  </div>
<?php endif;
include "footer.php"; ?>
