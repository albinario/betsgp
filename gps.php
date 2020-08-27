<?php $pageTitle = "GP's";
include 'header.php';

if (isset($_GET['id'])) :
  $gp_id = $_GET['id'];
  $gp = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM gps WHERE id = $gp_id"));
  $hasGPFinished = hasGPFinished($gp_id, $connect); ?>
  <div class="row">
    <div class="col-sm-5 col-lg-4">
      <div class="well">
        <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $gp['city_id'], $connect)?>.png" class="flag" /> <?=$gp_id?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></h4>
        <p class="small text-center"><?=$gp['startDate']?> <?=$gp['startTime']?></p>
        <img src="/graphics/cities/<?=$gp['city_id']?>.jpg" class="img-rounded shadow" style="max-width: 100%;" />
        <?php //if ($hasGPFinished) : ?>
          <div style="margin-top: 10px;">
            <?php $riderResultsInGP = getRidersResultsInGP($gp_id, $connect);
            $first = true;
            foreach ($riderResultsInGP as $result) : ?>
              <li>
                <a href="/riders.php?id=<?=$result['rider_id']?>">
                  <img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $result['rider_id'], $connect)?>.png" alt="" class="flag flag-sm" />
                  <?=getItem('name', 'riders', 'id', $result['rider_id'], $connect)?> <span class="small">(<?=($first) ? 'Picked by ' : null ?><?=getRiderTimesPickedInGP($result['rider_id'], $result['gp_id'], $connect)?>)</span>
                  <span class="pull-right">
                    <?php if ($result['podium']) : ?>
                      <img src="/graphics/medals/<?=$result['podium']?>.png" alt="" class="flag flag-sm" />
                    <?php endif ?>
                    <?=$result['points']?>
                  </span>
                </a>
              </li>
              <?php $first = false;
            endforeach ?>
          </div>
        <?php //endif ?>
      </div>
    </div>
    <?php //if (hasGPStarted($gp_id, $connect)) : ?>
      <div class="col-sm-7">
        <div class="well">
          <h4><?=($hasGPFinished) ? 'Final Results' : 'Live Score' ?></h4>
          <table class="table text-center">
            <?php //if ($hasGPFinished) {
            //   $users = getUsersResultsInGP($gp_id, $connect);
            // } else {
            //   $users = $standings;
            // }
            $prevPos = 0;
            $users = getUsersResultsInGP($gp_id, $connect);
            foreach ($users as $user) :
              $user_id = $user['user_id'];
              $userPicks = getUserPicksInGP($user_id, $gp_id, $connect);
              //if (isValid($user_id, $connect)) : ?>
                <tr class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
                  <td class="<?=(!$hasGPFinished) ? 'hidden-xs' : null ?>"><?=($user['position'] != $prevPos) ? $user['position'] : null ?></td>
                  <td class="text-left inline"><a href="/users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
                  <td class="text-left small">
                    <?php foreach ($userPicks as $pick) : ?>
                      <a href="/riders.php?id=<?=$pick?>"><img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $pick, $connect)?>.png" alt="" class="flag-sm"> <?=getItem('number', 'riders', 'id', $pick, $connect)?></a>
                      <span class="hidden-xs">&nbsp;</span>
                    <?php endforeach ?>
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
                </tr>
                <?php $prevPos = $user['position'];
              //endif;
            endforeach ?>
          </table>
        </div>
      </div>
    <?php //endif ?>

  </div>

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
            <?php //if ($hasGPFinished) :
              $topThree = getUsersResultsInGPTopThree($gp_id, $connect);
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
                  </span>
                </li>
                <?php $prevPos = $pos;
              endforeach ?>
              <a class="btn btn-primary" role="button" data-toggle="collapse" href="#collapse<?=$gp_id?>" aria-expanded="false" aria-controls="collapse<?=$gp_id?>" style="margin-top: 10px; width: 100%;">
                Riders <span class="glyphicon glyphicon-chevron-down"></span>
              </a>
              <div class="collapse" id="collapse<?=$gp_id?>" style="margin-top: 10px;">
                <?php $riderResultsInGP = getRidersResultsInGP($gp_id, $connect);
                foreach ($riderResultsInGP as $result) : ?>
                  <li>
                    <a href="/riders.php?id=<?=$result['rider_id']?>">
                      <img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $result['rider_id'], $connect)?>.png" alt="" class="flag flag-sm" />
                      <?=getItem('name', 'riders', 'id', $result['rider_id'], $connect)?> <span class="small">(<?=getRiderTimesPickedInGP($result['rider_id'], $result['gp_id'], $connect)?>)</span>
                      <span class="pull-right">
                        <?php if ($result['podium']) : ?>
                          <img src="/graphics/medals/<?=$result['podium']?>.png" alt="" class="flag flag-sm" />
                        <?php endif ?>
                        <?=$result['points']?>
                      </span>
                    </a>
                  </li>
                <?php endforeach ?>
              </div>
            <?php //endif ?>
            <a class="btn btn-primary" role="button" href="/gps.php?id=<?=$gp_id?>" style="margin-top: 10px; width: 100%;">
              <?=($hasGPFinished) ? 'Final Results' : 'Live Score' ?> <span class="glyphicon glyphicon-chevron-right"></span>
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
