<?php $pageTitle = 'Stats';
include "header.php"; ?>

<div class="row">
  <?php if ($lastGP) :
    for ($j=1; $j<=2; $j++) :
      $gp_id = $lastGP['id']; ?>
      <div class="col-sm-6">
        <div class="well">
          <table class="table text-center inline">
            <tr>
              <td colspan="3"></td>
              <?php for ($i=1; $i<=3; $i++) : ?>
                <td>
                  <img src="/graphics/medals/<?=$i?>.png" alt="" height="20px">
                </td>
              <?php endfor ?>
            </tr>
            <h4>Last <?=$j*2?> GP's</h4>
            <?php $users = mysqli_query($connect, "SELECT user_id, SUM(points) AS points, SUM(p_1) AS p_1, SUM(p_2) AS p_2, SUM(p_3) AS p_3, SUM(position) AS position FROM users_results WHERE gp_id BETWEEN $gp_id-($j*2) AND $gp_id GROUP BY user_id ORDER BY points DESC, position, user_id");
            $pos = 0;
            $prevPoints = 0;
            $sumPoints = 0;
            foreach ($users as $user) :
              $user_id = $user['user_id'];
              $points = $user['points'];
              $pos++;
              $sumPoints += $points; ?>
              <tr class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
                <td><?=($points != $prevPoints) ? $pos : null ?></td>
                <td class="text-left"><a href="users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
                <td class="bold"><?=$points?></td>
                <td><?=$user['p_1']?></td>
                <td><?=$user['p_2']?></td>
                <td><?=$user['p_3']?></td>
              </tr>
              <?php $prevPoints = $points;
            endforeach ?>
            <tr>
              <td colspan="2" class="text-right small">Average Points:</td>
              <td><?=round($sumPoints/$pos, 2)?></td>
              <td colspan="3"></td>
            </tr>
          </table>
        </div>
      </div>
    <?php endfor;
  endif ?>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="well">
      <h4>Top Ten GP Results</h4>
      <table class="table">
        <?php $users = mysqli_query($connect, "SELECT * FROM users_results ORDER BY points DESC, position LIMIT 10");
        $pos = 0;
        $prevPoints = 0;
        foreach ($users as $user) :
          $user_id = $user['user_id'];
          $gp_id = $user['gp_id'];
          $city_id = getItem('city_id', 'gps', 'id', $gp_id, $connect);
          $points = $user['points'];
          $pos++; ?>
          <tr class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
            <td class="text-center"><?=($points != $prevPoints) ? $pos : null ?></td>
            <td class="inline"><a href="/users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
            <td class="inline"><a href="/gps.php?id=<?=$gp_id?>"><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $city_id, $connect)?>.png" alt="" class="flag-sm"> <span class="small"><?=$gp_id?>. <?=getItem('name', 'cities', 'id', $city_id, $connect)?></span></a></td>
            <td class="hidden-xs">
              <?php $userPicks = getUserPicksInGP($user_id, $gp_id, $connect);
              foreach ($userPicks as $pick) : ?>
                <a href="/riders.php?id=<?=$pick?>"><img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $pick, $connect)?>.png" alt="" class="flag-sm"> <span class="small"><?=getItem('number', 'riders', 'id', $pick, $connect)?> &nbsp;</span></a>
              <?php endforeach ?>
            </td>
            <td class="text-center"><?=$points?></td>
          </tr>
        <?php
        $prevPoints = $points;
        endforeach ?>
      </table>
    </div>
  </div>

  <div class="col-md-6">
    <div class="well">
      <h4>Top Ten Rider Results</h4>
      <table class="table text-center">
        <?php $ridersTopTen = mysqli_query($connect, "SELECT * FROM riders_results ORDER BY points DESC, podium = 0, podium, races DESC LIMIT 10");
        $pos = 0;
        $prevPoints = 0;
        foreach ($ridersTopTen as $rider) :
          $rider_id = $rider['rider_id'];
          $fullName = getItem('name', 'riders', 'id', $rider_id, $connect);
          $surName = getSurName($fullName);
          $gp_id = $rider['gp_id'];
          $city_id = getItem('city_id', 'gps', 'id', $gp_id, $connect);
          $points = $rider['points'];
          $pos++; ?>
          <tr>
            <td><?=($points != $prevPoints) ? $pos : null ?></td>
            <td class="text-left inline"><a href="/riders.php?id=<?=$rider_id?>"><img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $rider_id, $connect)?>.png" alt="" class="flag flag-sm"> <span class="hidden-xs"><?=$fullName?></span><span class="hidden-sm hidden-md hidden-lg"><?=$surName?></span></a></td>
            <td class="inline"><a href="/gps.php?id=<?=$gp_id?>"><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $city_id, $connect)?>.png" alt="" class="flag-sm"> <span class="small"><?=$gp_id?>. <?=getItem('name', 'cities', 'id', $city_id, $connect)?></span></a></td>
            <td class="text-right">
              <?php if ($rider['podium']) : ?>
                <img src="/graphics/medals/<?=$rider['podium']?>.png" alt="" class="flag flag-sm" />
              <?php endif;
              echo $points; ?>
            </td>
          </tr>
        <?php
        $prevPoints = $points;
        endforeach ?>
      </table>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-sm-4 col-md-3">
    <div class="well">
      <h4>
        <?php for ($i=1; $i<=3; $i++) : ?>
          <img src="/graphics/medals/<?=$i?>.png" alt="" height="20px">
        <?php endfor ?>
      </h4>
      <table class="table">
        <?php $users = mysqli_query($connect, "SELECT user_id, SUM(p_1 + p_2 + p_3) AS amount, SUM(points) AS points FROM users_results GROUP BY user_id ORDER BY amount DESC, points DESC, user_id LIMIT 10");
        $pos = 0;
        $prevAmount = 0;
        foreach ($users as $user) :
          $user_id = $user['user_id'];
          $amount = $user['amount'];
          if ($amount) :
            $pos++; ?>
            <tr class="inline<?=($user_id == $loggedInUser) ? ' user' : null ?>">
              <td class="text-center"><?=($amount != $prevAmount) ? $pos : null ?></td>
              <td><a href="/users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
              <td class="text-center"><?=$amount?></td>
            </tr>
            <?php
            $prevAmount = $amount;
          endif;
        endforeach ?>
      </table>
    </div>
  </div>

  <?php for ($i=1; $i<=3; $i++) : ?>
    <div class="col-sm-4 col-md-3">
      <div class="well">
        <h4><img src="/graphics/medals/<?=$i?>.png" alt="" height="20px"></h4>
        <table class="table inline">
          <?php $pos = 0;
          $prevAmount = 0;
          foreach ($standings[$i] as $user) :
            $user_id = $user['user_id'];
            $amount = $user['p_'.$i];
            if ($amount && isValid($user_id, $connect)) :
              $pos++; ?>
              <tr class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
                <td class="text-center"><?=($amount != $prevAmount) ? $pos : null ?></td>
                <td><a href="/users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
                <td class="text-center"><?=$amount?></td>
              </tr>
              <?php
              $prevAmount = $amount;
            endif;
          endforeach ?>
        </table>
      </div>
    </div>
  <?php endfor ?>
</div>

<div class="row">
  <div class="col-sm-6 col-lg-4">
    <div class="well">
      <h4>Finished Races</h4>
      <table class="table inline">
        <?php $pos = 0;
        $prevAmount = 0;
        foreach ($standings[4] as $user) :
          $user_id = $user['user_id'];
          $amount = $user['races'];
          if ($amount && isValid($user_id, $connect)) :
            $pos++; ?>
            <tr class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
              <td class="text-center"><?=($amount != $prevAmount) ? $pos : null ?></td>
              <td><a href="/users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
              <td class="text-center"><?=$amount?></td>
            </tr>
            <?php
            $prevAmount = $amount;
          endif;
        endforeach ?>
      </table>
    </div>
  </div>

  <div class="col-sm-6 col-lg-5">
    <div class="well">
      <h4>Riders Times Picked</h4>
      <table class="table">
        <?php $riderArray = array();
        $validUsers = validUsers($connect);
        foreach ($riders as $rider) :
          $rider_id = $rider['id'];
          $timesPicked = getRiderTimesPickedTotal($rider_id, $connect);
          $riderArray[$rider_id] = $timesPicked; ?>
        <?php endforeach;
        arsort($riderArray);
        $pos = 0;
        $prevValue = 0;
        foreach ($riderArray as $rider => $value) :
          $gpsRaced = getRiderGPsRaced($rider, $connect);
          $pos++; ?>
          <tr class="<?=(!$value) ? 'dim' : null ?>">
            <td class="text-center"><?=($value != $prevValue) ? $pos : null ?></td>
            <td class="inline"><a href="/riders.php?id=<?=$rider?>"><img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $rider, $connect)?>.png" alt="" class="flag flag-sm"> <?=getItem('name', 'riders', 'id', $rider, $connect)?></a></td>
            <td class="text-center"><?=$value?></td>
            <td class="text-center"><span class="small"><?=($gpsRaced) ? number_format($value/$gpsRaced/$validUsers * 100).'%' : null ?></span></td>
          </tr>
        <?php $prevValue = $value;
        endforeach ?>
      </table>
    </div>
  </div>
</div>


<?php include "footer.php"; ?>
