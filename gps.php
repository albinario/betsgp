<?php $pageTitle = "GP's";
include 'header.php';

if (isset($_GET['id'])) :
  $gp_id = $_GET['id'];
  $gp = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM gps WHERE id = $gp_id"));
  $hasGPFinished = hasGPFinished($gp_id, $connect); ?>
  <div class="row">
    <div class="col-md-3">
      <div class="well text-center">
        <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $gp['city_id'], $connect)?>.png" class="flag" /> <?=$gp_id?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></h4>
        <p class="small"><?=$gp['startDate']?> <?=$gp['startTime']?></p>
        <img src="/graphics/cities/<?=$gp['city_id']?>.jpg" class="img-rounded shadow" style="max-width: 100%;" />
        <a class="btn btn-success text-uppercase hidden-md hidden-lg" role="button" href="#comments" data-scroll style="margin-top: 10px; width: 100%;">
          Comment this GP <span class="glyphicon glyphicon-chevron-down"></span>
        </a>
      </div>
    </div>
  </div>
  <div class="row">
    <?php if (hasGPStarted($gp_id, $connect)) : ?>
      <div class="col-md-6">
        <div class="well">
          <h4><?=($hasGPFinished) ? 'Final Result' : 'Live Score' ?></h4>
          <table class="table text-center">
            <tr class="small hidden-xs">
              <td colspan="3"></td>
              <td>Points</td>
              <?php if ($hasGPFinished) : ?>
                <td></td>
              <?php endif ?>
              <td>Races</td>
              <td>Tot</td>
            </tr>
            <?php $prevPos = 0;
            $sumPoints = 0;
            $usersAmountInGP = getUsersAmountInGP($gp_id, $connect);
            $users = getUsersResultsInGP($gp_id, $connect);
            $userPickedRiders = array();
            if ($loggedInUser) { $userPickedRiders = getUserPicksInGP($loggedInUser, $gp_id, $connect); }
            $pickedRiders = array();
            foreach ($users as $user) :
              $user_id = $user['user_id'];
              $sumPoints += $user['points'];
              $userPicks = getUserPicksInGP($user_id, $gp_id, $connect); ?>
              <tr class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
                <td class="<?=(!$hasGPFinished) ? 'hidden-xs' : null ?>"><?=($user['position'] != $prevPos) ? $user['position'] : null ?></td>
                <td class="text-left inline">
                  <a href="/users.php?id=<?=$user_id?>">
                    <span class="hidden-xs"><?=getUserName($user_id, $connect)?></span>
                    <span class="hidden-sm hidden-md hidden-lg"><?=getUserNameShort($user_id, $connect)?></span>
                  </a>
                </td>
                <td>
                  <?php foreach ($userPicks as $pick) : ?>
                    <span class="small<?=(in_array($pick, $userPickedRiders)) ? ' user' : null ?>">
                      <a href="/riders.php?id=<?=$pick?>" class="<?=(!in_array($pick, $pickedRiders)) ? 'border' : null ?>" title="<?=getItem('name', 'riders', 'id', $pick, $connect)?>"><img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $pick, $connect)?>.png" alt="" class="flag-sm"> <?=getItem('number', 'riders', 'id', $pick, $connect)?></a>
                      <span class="hidden-xs">&nbsp;</span>
                    </span>
                  <?php if (!in_array($pick, $pickedRiders)) { array_push($pickedRiders, $pick); }
                  endforeach ?>
                </td>
                <td><?=$user['points']?></td>
                <?php if ($hasGPFinished) : ?>
                  <td class="hidden-xs">
                    <?php for ($i=1; $i<=3; $i++) :
                      if ($user['p_'.$i]) : ?>
                        <img src="/graphics/medals/<?=$i?>.png" alt="" class="flag-sm">
                      <?php endif;
                    endfor ?>
                  </td>
                <?php endif ?>
                <td><?=$user['races']?></td>
                <td class="hidden-xs"><span class="small"><?=$user['total']?></span></td>
              </tr>
              <?php $prevPos = $user['position'];
            endforeach ?>
            <tr class="small">
              <td colspan="3" class="text-right">Average Points:</td>
              <td><?=($usersAmountInGP) ? round($sumPoints/$usersAmountInGP, 2) : null ?></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <div class="well">
          <h4>Riders</h4>
            <table class="table text-center inline">
              <tr class="small">
                <td></td>
                <?php if ($hasGPFinished) : ?>
                  <td></td>
                <?php endif ?>
                <td>Points</td>
                <td>Races</td>
                <td>Picked</td>
              </tr>
              <?php $riderResultsInGP = getRidersResultsInGP($gp_id, $connect);
              foreach ($riderResultsInGP as $result) :
                $fullName = getItem('name', 'riders', 'id', $result['rider_id'], $connect);
                $surName = getSurName($fullName); ?>
                <tr class="<?=($loggedInUser && in_array($result['rider_id'], $userPickedRiders)) ? 'user' : null ?>">
                  <td class="text-left">
                    <a href="/riders.php?id=<?=$result['rider_id']?>">
                      <img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $result['rider_id'], $connect)?>.png" alt="" class="flag flag-sm" />
                      <span class="small hidden-xs"><?=getItem('number', 'riders', 'id', $result['rider_id'], $connect)?></span>
                      <span class="hidden-xs"><?=$fullName?></span>
                      <span class="hidden-sm hidden-md hidden-lg"><?=$surName?></span>
                    </a>
                  </td>
                  <?php if ($hasGPFinished) : ?>
                    <td class="text-right">
                      <?php if ($result['podium']) : ?>
                        <img src="/graphics/medals/<?=$result['podium']?>.png" alt="" class="flag flag-sm" />
                      <?php endif ?>
                    </td>
                  <?php endif ?>
                  <td><?=$result['points']?></td>
                  <td><?=$result['races']?></td>
                  <td><?=getRiderTimesPickedInGP($result['rider_id'], $result['gp_id'], $connect)?></td>
                </tr>
                <?php $first = false;
              endforeach ?>
            </table>
        </div>
      </div>
    <?php endif ?>
  </div>
  <?php include 'components/comments.php'; ?>

<?php else: ?>

  <div class="row equal">
    <?php foreach ($gpsClosed as $gp) :
      $gp_id = $gp['id'];
      $hasGPFinished = hasGPFinished($gp_id, $connect); ?>
      <a href="/gps.php?id=<?=$gp_id?>">
        <div class="col-sm-6 col-lg-3">
          <div class="well">
            <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $gp['city_id'], $connect)?>.png" class="flag" /> <?=$gp_id?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></h4>
            <p class="small text-center"><?=$gp['startDate']?> <?=$gp['startTime']?></p>
            <img src="/graphics/cities/<?=$gp['city_id']?>.jpg" class="img-rounded shadow" style="max-width: 100%; margin-bottom: 10px;" />
            <?php $topThree = getUsersResultsInGPTopThree($gp_id, $connect);
            $prevPos = 0;
            foreach ($topThree as $user) :
              $user_id = $user['user_id'];
              $pos = $user['position']; ?>
              <li class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
                <span class="<?=(!$pos || $pos == $prevPos) ? 'off' : null ?>"><?=$pos?>.</span>
                <a href="/users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a>
                <span class="pull-right">
                  <?php for ($i=1; $i<=3; $i++) :
                    if ($user['p_'.$i]) : ?>
                      <img src="/graphics/medals/<?=$i?>.png" alt="" class="flag flag-sm">
                    <?php endif;
                  endfor ?>
                  <?=$user['points']?>
                </span><br style="clear: both;" />
              </li>
              <?php $prevPos = $pos;
            endforeach ?>
            <a class="btn btn-primary text-uppercase" role="button" href="/gps.php?id=<?=$gp_id?>" style="margin-top: 10px; width: 100%;">
              <?=($hasGPFinished) ? 'Final Result' : 'Live Score' ?> <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
        </div>
      </a>
    <?php endforeach ?>
  </div>

  <div class="row">
    <?php foreach ($gpsUpcoming as $gp) : ?>
      <div class="col-sm-6 col-lg-3">
        <div class="well text-center">
          <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $gp['city_id'], $connect)?>.png" class="flag" /> <?=$gp['id']?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></h4>
          <p class="small"><?=$gp['startDate']?> <?=$gp['startTime']?></p>
          <img src="/graphics/cities/<?=$gp['city_id']?>.jpg" class="img-rounded shadow" style="max-width: 100%;" />
          <a href="<?=($loggedInUser) ? '/users.php?id='.$loggedInUser.'#'.$gp['id'] : '/account.php' ?>" class="btn btn-success text-uppercase" role="button" style="margin-top: 10px; width: 100%;">
            <?php if ($loggedInUser) : ?>
              Pick Riders <span class="glyphicon glyphicon-chevron-right"></span>
            <?php else : ?>
              <span class="glyphicon glyphicon-log-in"></span> Sign in to Pick Riders
            <?php endif ?>
          </a>
        </div>
      </div>
    <?php endforeach ?>
  </div>

<?php endif;
include 'footer.php'; ?>
