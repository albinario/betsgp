<?php
if (isset($_GET['id'])) :
  $user_id = $_GET['id'];
  include 'inc/connect_db_live.php';
  $user = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users WHERE id = $user_id"));
  $userName = $user['first_name'].' '.$user['last_name'];
  $pageTitle = $userName;
  include 'header.php';
  $userResultsTotal = getUserResultsTotal($user_id, $connect);
  $gpsRaced = getUserGPsRaced($user_id, $connect); ?>

  <div class="row equal">
    <div class="col-sm-4 col-md-3">
      <div class="well text-center">
        <h4><?=$userName?></h4>
        <div class="text-left" style="margin-top: 10px;">
          <li>Total Points<span class="pull-right"><?=$userResultsTotal[0]?></span></li>
          <?php for ($i=1; $i<=3; $i++) : ?>
            <li><span class="pull-right"><img src="/graphics/medals/<?=$i?>.png" alt="" class="flag flag-sm" /> <?=$userResultsTotal[$i]?></span><br style="clear: both;"/></li>
          <?php endfor ?>
          <li style="margin-top: 5px;">GP's<span class="pull-right"><?=$gpsRaced?></span></li>
          <li>Average per GP<span class="pull-right"><?=($gpsRaced) ? round($userResultsTotal[0]/$gpsRaced, 2) : null ?></span></li>
          <li>Average per Rider<span class="pull-right"><?=($gpsRaced) ? round($userResultsTotal[0]/$gpsRaced/3, 2) : null ?></span></li>
          <li>Finished Races<span class="pull-right"><?=$userResultsTotal[4]?></span></li>
          <li>Overall Position<span class="pull-right"><?=($gpsRaced) ? $userResultsTotal[5].' / '.$validUsers : null ?></span></li>
        </div>
        <?php if ($loggedInUser == $user_id) : ?>
          <a class="btn btn-success text-uppercase hidden-sm hidden-md hidden-lg" role="button" href="#<?=$nextGP['id']?>" data-scroll style="margin-top: 10px; width: 100%;">
            Pick Riders in Next GP <span class="glyphicon glyphicon-chevron-down"></span>
          </a>
        <?php endif ?>
      </div>
    </div>

    <?php foreach ($gpsClosed as $gp) :
      $gp_id = $gp['id'];
      $hasGPFinished = hasGPFinished($gp_id, $connect);
      $usersAmountInGP = getUsersAmountInGP($gp_id, $connect); ?>
      <div class="col-sm-4 col-md-3">
        <div class="well">
          <a href="/gps.php?id=<?=$gp_id?>">
            <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', getitem('city_id', 'gps', 'id', $gp_id, $connect), $connect)?>.png" class="flag" /> <?=$gp_id?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></h4>
            <img src="/graphics/cities/<?=$gp['city_id']?>.jpg" class="img-rounded shadow" style="max-width: 100%; margin-bottom: 10px;" />
          </a>
          <?php $picks = getUserPicksInGP($user_id, $gp_id, $connect);
          if ($picks) :
            foreach ($picks as $pick) : ?>
              <li>
                <a href="/riders.php?id=<?=$pick?>">
                  <img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $pick, $connect)?>.png" alt="" class="flag flag-sm" /> <?=getItem('name', 'riders', 'id', $pick, $connect)?>
                  <span class="pull-right">
                    <?php $result = getRiderResultsInGP($pick, $gp_id, $connect);
                    $podium = $result[1];
                    if ($podium) : ?>
                      <img src="/graphics/medals/<?=$podium?>.png" alt="" class="flag flag-sm" />
                    <?php endif;
                    echo $result[0]; ?>
                  </span>
                </a>
              </li>
            <?php endforeach;
            $userResultsInGP = getUserResultsInGP($user_id, $gp_id, $connect); ?>
            <li style="margin-top: 5px;">Points<span class="pull-right"><?=$userResultsInGP[0]?></span></li>
            <li>Average per Rider<span class="pull-right"><?=round($userResultsInGP[0]/3, 2)?></span></li>
            <li>Finished Races<span class="pull-right"><?=$userResultsInGP[4]?></span></li>
            <li>Position<span class="pull-right"><?=$userResultsInGP[5]?> / <?=$usersAmountInGP?></span></li>
          <?php endif ?>
        </div>
      </div>
    <?php endforeach;

    if ($loggedInUser == $user_id) :
      include 'inc/picks_backend.php';
      foreach ($gpsUpcoming as $gp) :
        $gp_id = $gp['id'];
        $userPicks = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM users_picks WHERE user_id = $user_id AND gp_id = $gp_id LIMIT 1")); ?>
        <div class="col-sm-4 col-md-3" id="<?=$gp_id?>">
          <div class="well">
            <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $gp['city_id'], $connect)?>.png" class="flag" /> <?=$gp_id?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></h4>
            <?php include 'inc/alerts.php'; ?>
            <form action="" method="post">
              <?php for ($i=1; $i<=3; $i++) : ?>
                <div class="row form-group">
                  <div class="col-xs-12">
                    <div class="input-group">
                      <span class="input-group-addon"><img src="/graphics/<?=($userPicks && $userPicks['pick_'.$i]) ? 'nations/'.getItem('nation_id', 'riders', 'id', $userPicks['pick_'.$i], $connect) : 'sgp_logo' ?>.png" width="16px" /></span>
                      <select name="pick_<?=$i?>" class="form-control input-sm">
                        <option value="0">Pick rider <?=$i?></option>
                        <?php foreach ($riders as $rider) :
                          if ($rider['active'] && (!$rider['wc_city_id'] || ($rider['wc_city_id'] == $gp['city_id']))) : ?>
                            <option value="<?=$rider['id']?>" <?=($userPicks && $userPicks['pick_'.$i] == $rider['id']) ? 'selected="selected"' : null ?>>
                              <?=$rider['name']?>
                            </option>
                          <?php endif;
                        endforeach ?>
                      </select>
                    </div>
                  </div>
                </div>
              <?php endfor ?>
              <div class="row form-group">
                <div class="col-xs-12">
                  <input type="hidden" name="gp_id" value="<?=$gp_id?>">
                  <button type="submit" name="submit_picks" class="btn btn-success btn-sm text-uppercase" style="width: 100%;">
                    <span class="glyphicon glyphicon-check"></span> <?=($userPicks) ? 'Update' : 'Save' ?>
                  </button>
                  <p class="text-right small" style="margin-top: 10px;">
                    <?php if ($userPicks) : ?>Picks updated: <?=$userPicks['date_updated']?><br/><?php endif ?>
                    Open until: <?=$gp['startDate']?> <?=$gp['startTime']?><br/>
                    Current time: <?=dateNow()?> <?=timeNow()?>
                  </p>
                </div>
              </div>
            </form>
          </div>
        </div>
      <?php endforeach;
    endif ?>
  </div>

<?php else:
  $pageTitle = "Participants";
  include 'header.php'; ?>
  <div class="row">
    <div class="col-sm-6 col-sm-offset-3">
      <div class="list-group small">
        <div class="list-group-item list-group-header">
          <h4>Participants</h4>
        </div>
        <?php foreach ($usersAlphabetical as $user) :
          $user_id = $user['id']; ?>
          <a class="list-group-item list-group-item-<?=(isValid($user_id, $connect)) ? 'success' : 'danger'?>" href="users.php?id=<?=$user_id?>">
            <?=$user['first_name']?> <?=$user['last_name']?><span class="pull-right"><?=dateCreated($user_id, $connect)?> <span class="glyphicon glyphicon-chevron-right" style="margin-left: 11px;"></span></span>
          </a>
        <?php endforeach ?>
      </div>
    </div>
  </div>

<?php endif;
include 'footer.php'; ?>
