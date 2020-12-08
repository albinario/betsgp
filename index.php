<?php $pageTitle = 'Home';
include 'header.php'; ?>

<div class="row">
  <div class="col-md-6">
    <div class="well">
      <h4>Top Ten</h4>
      <table class="table text-center">
        <tr>
          <td colspan="2"></td>
          <td class="small"><span class="hidden-xs">Points</span></td>
          <?php for ($i=1; $i<=3; $i++) : ?>
            <td><img src="/graphics/medals/<?=$i?>.png" alt="" class="flag-sm" /></td>
          <?php endfor ?>
          <td class="small hidden-xs">Races</td>
        </tr>
        <?php $showLoggedInUser = true;
        $prevPos = 0;
        foreach ($standingsTopTen as $user) :
          $user_id = $user['user_id'];
          if (isValid($user_id, $connect)) :
            if ($user_id == $loggedInUser) { $showLoggedInUser = false; } ?>
            <tr class="inline<?=($user_id == $loggedInUser) ? ' user' : null ?>">
              <td>
                <?=($user['position'] != $prevPos) ? $user['position'] : null ?>
                <span class="small">
                  <?php if ($user['prev_position'] && $user['position'] < $user['prev_position']) : ?>
                    <span class="glyphicon glyphicon-arrow-up" style="color: #5cb85c;"></span>
                  <?php elseif ($user['prev_position'] && $user['position'] > $user['prev_position']) : ?>
                    <span class="glyphicon glyphicon-arrow-down" style="color: #d9534f;"></span>
                  <?php else : ?>
                    <span class="glyphicon glyphicon-arrow-right" style="color: #1f1f1f;"></span>
                  <?php endif ?>
                </span>
              </td>
              <td class="text-left"><a href="users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
              <td class="bold"><?=$user['points']?></td>
              <td><?=$user['p_1']?></td>
              <td><?=$user['p_2']?></td>
              <td><?=$user['p_3']?></td>
              <td class="hidden-xs"><?=$user['races']?></td>
            </tr>
            <?php $prevPos = $user['position'];
          endif;
        endforeach;
        if ($showLoggedInUser && $loggedInUser) :
          $userResults = getUserResultsTotal($loggedInUser, $connect); ?>
          <tr class="inline user">
            <td><?=($userResults[5]) ? $userResults[5] : null ?>
              <span class="small">
                <?php if ($userResults[6] && $userResults[5] < $userResults[6]) : ?>
                  <span class="glyphicon glyphicon-arrow-up" style="color: #5cb85c;"></span>
                <?php elseif ($userResults[6] && $userResults[5] > $userResults[6]) : ?>
                  <span class="glyphicon glyphicon-arrow-down" style="color: #d9534f;"></span>
                <?php else : ?>
                  <span class="glyphicon glyphicon-arrow-right" style="color: #1f1f1f;"></span>
                <?php endif ?>
              </span>
            </td>
            <td class="text-left user"><a href="users.php?id=<?=$loggedInUser?>"><?=getUserName($loggedInUser, $connect)?></a></td>
            <td><?=$userResults[0]?></td>
            <td><?=$userResults[1]?></td>
            <td><?=$userResults[2]?></td>
            <td><?=$userResults[3]?></td>
            <td class="hidden-xs"><?=$userResults[4]?></td>
          </tr>
        <?php endif ?>
        <tr class="inline">
          <td colspan="7"><a href="/standings.php" class=" text-center">Standings <span class="glyphicon glyphicon-chevron-right"></span></a>
          </td>
        </tr>
      </table>
    </div>
  </div>

  <?php if ($lastGP) :
    $gp_id = $lastGP['id'];
    $hasGPFinished = hasGPFinished($gp_id, $connect);
    $gpCity = getItem('name', 'cities', 'id', $lastGP['city_id'], $connect); ?>
    <div class="col-md-6">
      <div class="well">
        <h4><a href="/gps.php?id=<?=$gp_id?>"><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $lastGP['city_id'], $connect)?>.png" alt="" class="flag"> <?=$gp_id?>. <?=$gpCity?> – <?=($hasGPFinished) ? 'Final Result' : 'Live Score' ?></a></h4>
        <table class="table text-center">
          <tr class="small hidden-xs">
            <td colspan="3"></td>
            <td>Points</td>
            <?php if ($hasGPFinished) : ?>
              <td></td>
            <?php endif ?>
            <td>Races</td>
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
                <td>
                  <?php for ($i=1; $i<=3; $i++) :
                    if ($user['p_'.$i]) : ?>
                      <img src="/graphics/medals/<?=$i?>.png" alt="" class="flag-sm">
                    <?php endif;
                  endfor ?>
                </td>
              <?php endif ?>
              <td><?=$user['races']?></td>
            </tr>
            <?php $prevPos = $user['position'];
          endforeach ?>
          <tr class="inline">
            <td colspan="7"><a href="/gps.php?id=<?=$gp_id?>" class=" text-center">Full table <span class="glyphicon glyphicon-chevron-right"></span></a>
            </td>
          </tr>
        </table>
      </div>
    </div>
  <?php else : ?>
    <div class="col-md-6">
      <div class="well text-center">
        <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $nextGP['city_id'], $connect)?>.png" class="flag" /> <?=$nextGP['id']?>. <?=getItem('name', 'cities', 'id', $nextGP['city_id'], $connect)?></h4>
        <p class="small"><?=$nextGP['startDate']?> <?=$nextGP['startTime']?></p>
        <img src="/graphics/cities/<?=$nextGP['city_id']?>.jpg" class="img-rounded shadow" style="max-width: 100%;" />
        <a href="<?=($loggedInUser) ? '/users.php?id='.$loggedInUser.'#'.$nextGP['id'] : '/account.php' ?>" class="btn btn-success text-uppercase" role="button" style="margin-top: 10px; width: 100%;">
          <?php if ($loggedInUser) : ?>
            Pick Riders <span class="glyphicon glyphicon-chevron-right"></span>
          <?php else : ?>
            <span class="glyphicon glyphicon-log-in"></span> Sign in to Pick Riders
          <?php endif ?>
        </a>
      </div>
    </div>
  <?php endif ?>
</div>

<?php include 'footer.php'; ?>
