<?php $pageTitle = "Admin";
include "header.php";

if ($loggedInUser) :
  if (isAdmin($loggedInUser, $connect)) :
    include 'inc/admin_backend.php'; ?>

    <div class="row">
      <div class="col-xs-12">
        <?php include 'inc/alerts.php'; ?>
      </div>
    </div>

    <div class="row equal">
      <?php if ($lastGP) : ?>
        <div class="col-sm-6 col-md-4">
          <div class="well">
            <?php if (!hasGPFinished($lastGP['id'], $connect)) {
              $gp_id = $lastGP['id'];
            } else {
              $gp_id = $nextGP['id'];
            } ?>
            <h4><?=$gp_id?>. <?=getItem('name', 'cities', 'id', getItem('city_id', 'gps', 'id', $gp_id, $connect), $connect)?> – Report Race</h4>
            <form method="post" action="">
              <div class="row form-group">
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <input type="checkbox" name="first">
                    </span>
                    <input type="text" class="form-control" value="First" style="background-color: #1f1f1f; color: #c5c5c5;" disabled>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <input type="checkbox" name="final">
                    </span>
                    <input type="text" class="form-control" value="Final" style="background-color: #1f1f1f; color: #c5c5c5;" disabled>
                  </div>
                </div>
              </div>
              <?php $p = 3;
              for ($i=1; $i<=4; $i++) : ?>
                <div class="row form-group">
                  <div class="col-xs-9">
                    <select name="rider_id_<?=$i?>" class="form-control">
                      <option value="0">Select Rider <?=$i?></option>
                      <?php foreach ($riders as $rider) :
                        if (!$rider['wc_city_id'] || $rider['wc_city_id'] == getItem('city_id', 'gps', 'id', $gp_id, $connect)) : ?>
                          <option value="<?=$rider['id']?>"><?=$rider['name']?></option>
                        <?php endif;
                       endforeach ?>
                    </select>
                  </div>
                  <div class="col-xs-3">
                    <input type="text" name="points_<?=$i?>" value="<?=$p?>" class="form-control">
                  </div>
                </div>
              <?php $p--;
              endfor ?>


              <input type="hidden" name="gp_id" value="<?=$gp_id?>">
              <button type="submit" class="btn btn-success text-uppercase form-control" name="report_race">
                <span class="glyphicon glyphicon-ok"></span>
              </button>
            </form>
          </div>
        </div>
      <?php endif ?>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Rider Result</h4>
          <form method="post" action="">
            <div class="row form-group">
              <div class="col-xs-12">
                <select name="rider_id" class="form-control">
                  <option value="0">Select Rider</option>
                  <?php foreach ($riders as $rider) : ?>
                    <option value="<?=$rider['id']?>"><?=$rider['name']?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
            <div class="row form-group">
              <div class="col-xs-5">
                <select name="gp_id" class="form-control">
                  <?php foreach ($gpsClosed as $gp) : ?>
                    <option value="<?=$gp['id']?>"><?=$gp['id']?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></option>
                  <?php endforeach ?>
                </select>
              </div>
              <div class="col-xs-4">
                <select name="points" class="form-control">
                  <?php for ($i=0; $i<=21; $i++) : ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor ?>
                </select>
              </div>
              <div class="col-xs-3">
                <select name="podium" class="form-control">
                  <option value="0"></option>
                  <option value="3">3rd</option>
                  <option value="2">2nd</option>
                  <option value="1">1st</option>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-success text-uppercase form-control" name="rider_result">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Confirm Payment</h4>
          <form method="post" action="">
            <div class="form-group">
              <select name="user_id" class="form-control">
                <option value="0">Select User</option>
                <?php foreach ($users as $user) : if (!hasPaid($user['id'], $connect)) : ?>
                  <option value="<?=$user['id']?>"><?=$user['first_name']." ".$user['last_name']?></option>
                <?php endif; endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success text-uppercase form-control" name="payment_confirm">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Revoke Payment</h4>
          <form method="post" action="">
            <div class="form-group">
              <select name="user_id" class="form-control">
                <option value="0">Select User</option>
                <?php foreach ($users as $user) : if (hasPaid($user['id'], $connect)) : ?>
                  <option value="<?=$user['id']?>"><?=$user['first_name']." ".$user['last_name']?></option>
                <?php endif; endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-danger text-uppercase form-control" name="payment_revoke">
              <span class="glyphicon glyphicon-remove"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Grant Admin Access</h4>
          <form method="post" action="">
            <div class="form-group">
              <select name="user_id" class="form-control">
                <option value="0">Select User</option>
                <?php foreach ($users as $user) : if (!isAdmin($user['id'], $connect)) : ?>
                  <option value="<?=$user['id']?>"><?=$user['first_name']." ".$user['last_name']?></option>
                <?php endif; endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success text-uppercase form-control" name="admin_grant">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Revoke Admin Access</h4>
          <form method="post" action="">
            <div class="form-group">
              <select name="user_id" class="form-control">
                <option value="0">Select User</option>
                <?php foreach ($users as $user) : if (isAdmin($user['id'], $connect)) : ?>
                  <option value="<?=$user['id']?>"><?=$user['first_name']." ".$user['last_name']?></option>
                <?php endif; endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-danger text-uppercase form-control" name="admin_revoke">
              <span class="glyphicon glyphicon-remove"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Add Wild Card</h4>
          <form method="post" action="">
            <div class="row form-group">
              <div class="col-xs-12">
                <select name="city_id" class="form-control">
                  <option value="0">Select City</option>
                  <?php foreach ($cities as $city) : ?>
                    <option value="<?=$city['id']?>"><?=$city['name']?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
            <div class="row form-group">
              <div class="col-xs-12">
                <input type="text" class="form-control" placeholder="Name" name="wild_card">
              </div>
            </div>
            <div class="row form-group">
              <div class="col-xs-12">
                <select name="nation_id" class="form-control">
                  <option value="0">Select Nation</option>
                  <?php foreach ($nations as $nation) : ?>
                    <option value="<?=$nation['id']?>"><?=$nation['name']?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-success text-uppercase form-control" name="add_wild_card">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Activate Substitute Rider</h4>
          <form method="post" action="">
            <div class="form-group">
              <select name="rider_id" class="form-control">
                <option value="0">Select Rider</option>
                <?php foreach ($subRiders as $rider) : ?>
                  <option value="<?=$rider['id']?>"><?=$rider['name']?></option>
                <?php endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success text-uppercase form-control" name="activate_substitute">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Activate Rider</h4>
          <form method="post" action="">
            <div class="form-group">
              <select name="rider_id" class="form-control">
                <option value="0">Select Rider</option>
                <?php foreach ($riders as $rider) : if (!getItem('active', 'riders', 'id', $rider['id'] ,$connect)) : ?>
                  <option value="<?=$rider['id']?>"><?=$rider['name']?></option>
                <?php endif; endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success text-uppercase form-control" name="activate_rider">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Deactivate Rider</h4>
          <form method="post" action="">
            <div class="form-group">
              <select name="rider_id" class="form-control">
                <option value="0">Select Rider</option>
                <?php foreach ($riders as $rider) : if (getItem('active', 'riders', 'id', $rider['id'] ,$connect)) : ?>
                  <option value="<?=$rider['id']?>"><?=$rider['name']?></option>
                <?php endif; endforeach ?>
              </select>
            </div>
            <button type="submit" class="btn btn-danger text-uppercase form-control" name="deactivate_rider">
              <span class="glyphicon glyphicon-remove"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Update Rider Name</h4>
          <form method="post" action="">
            <div class="row form-group">
              <div class="col-xs-12">
                <select class="form-control" name="rider_id">
                  <option value="0">Select Rider</option>
                  <?php foreach ($allRiders as $rider) : ?>
                    <option value="<?=$rider['id']?>"><?=$rider['name']?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>
            <div class="row form-group">
              <div class="col-xs-12">
                <input type="text" class="form-control" placeholder="New Name" name="new_rider_name">
              </div>
            </div>
            <button type="submit" class="btn btn-success text-uppercase form-control" name="update_rider_name">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>

      <div class="col-sm-6 col-md-4">
        <div class="well">
          <h4>Update Standings</h4>
          <form method="post" action="">
            <button type="submit" class="btn btn-success text-uppercase form-control" name="update_standings">
              <span class="glyphicon glyphicon-ok"></span>
            </button>
          </form>
        </div>
      </div>
    </div>

    <?php else :
    array_push($errors, "You don't have admin rights");
    include 'inc/alerts.php';
  endif;
else:
  array_push($errors, "You're not logged in");
  include 'inc/alerts.php';
endif;

include "footer.php"; ?>
