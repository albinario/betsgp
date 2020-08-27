<?php $pageTitle = "Riders";
include 'header.php';

$validUsers = validUsers($connect);
if (isset($_GET['id'])) :
  $rider_id = $_GET['id'];
  $rider = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM riders WHERE id = $rider_id"));
  $results = getRiderResultsTotal($rider_id, $connect);
  $wildCard = $rider['wc_city_id']; ?>
  <div class="row">
    <div class="col-sm-4 col-md-3">
      <div class="well text-center">
        <h4><?=$rider['name']?></h4>
        <img src="/graphics/nations/<?=$rider['nation_id']?>.png" class="flag" /> <?=($wildCard) ? 'Wild Card – '.getItem('name', 'cities', 'id', $rider['wc_city_id'], $connect) : '#'.$rider['number'] ?>
        <br>
        <img src="/graphics/riders/<?=$rider['number']?>.jpg" class="img-rounded shadow" style="max-width: 100%; margin: 10px 0;" />
        <div class="text-left">
          <li><strong>Total Points<span class="pull-right"><?=$results[0]?></span></strong></li>
          <?php for ($i=1; $i<=3; $i++) : ?>
            <li><span class="pull-right"><img src="/graphics/medals/<?=$i?>.png" alt="" class="flag flag-sm" /> <?=$results[$i]?></span><br style="clear: both;" /></li>
          <?php endfor ?>
          <li>GP's<span class="pull-right"><?=$results[4]?></span></li>
          <li>Finished Races<span class="pull-right"><?=$results[5]?></span></li>
          <li>Average Points<span class="pull-right"><?=($results[4]) ? round($results[0]/$results[4], 2) : null ?></span></li>
          <li>Times Picked<span class="pull-right"><?=$results[6]?></span></li>
          <li style="margin-bottom: 10px;">Pick Ratio<span class="pull-right"><?=($results[4]) ? number_format($results[6]/$results[4]/$validUsers * 100).'%' : null ?></span></li>
          <!-- <?php $resultInGPs = mysqli_query($connect, "SELECT * FROM riders_results WHERE rider_id = $rider_id ORDER BY gp_id DESC");
          foreach ($resultInGPs as $result) :
            $gp_id = $result['gp_id'];
            if (hasGPFinished($gp_id, $connect)) :
              $city_id = getitem('city_id', 'gps', 'id', $gp_id, $connect);?>
              <li>
                <a href="/gps.php?id=<?=$result['gp_id']?>">
                  <img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $city_id, $connect)?>.png" alt="" class="flag flag-sm" />
                  <?=$gp_id?>. <?=getItem('name', 'cities', 'id', $city_id, $connect)?> (<?=getRiderTimesPickedInGP($result['rider_id'], $gp_id, $connect)?>)
                  <span class="pull-right">
                    <?php if ($result['podium']) : ?>
                      <img src="/graphics/medals/<?=$result['podium']?>.png" alt="" class="flag flag-sm" />
                    <?php endif ?>
                    <?=$result['points']?>
                  </span>
                </a>
              </li>
          <?php endif; endforeach ?> -->
        </div>
      </div>
    </div>
    <?php foreach ($gpsClosed as $gp) :
      $gp_id = $gp['id'];
      if (!$wildCard || ($wildCard == $gp['city_id'])) : ?>
        <div class="col-sm-4 col-md-3">
          <div class="well">
            <a href="/gps.php?id=<?=$gp_id?>">
              <h4><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $gp['city_id'], $connect)?>.png" class="flag" /> <?=$gp_id?>. <?=getItem('name', 'cities', 'id', $gp['city_id'], $connect)?></h4>
              <img src="/graphics/cities/<?=$gp['city_id']?>.jpg" class="img-rounded shadow" style="max-width: 100%; margin-bottom: 10px;" />
            </a>
            <?php //if (hasGPFinished($gp_id, $connect)) :
              $result = getRiderResultsInGP($rider_id, $gp_id, $connect);?>
              <li>Points<span class="pull-right">
                <?php if ($result[1]) : ?>
                  <img src="/graphics/medals/<?=$result[1]?>.png" alt="" class="flag flag-sm" />
                <?php endif ?>
                <?=$result[0]?>
              </span></li>
              <li style="margin-bottom: 5px;">Finished Races<span class="pull-right"><?=$result[2]?></span></li>
            <?php //endif;
          $timesPickedInGP = getRiderTimesPickedInGP($rider_id, $gp_id, $connect);
            if ($timesPickedInGP) : ?>
              <li class="small">Picked by <?=$timesPickedInGP?>:</li>
              <?php $pickersInGP = getRiderPickersInGP($rider_id, $gp_id, $connect);
              $prevPos = 0;
              foreach ($pickersInGP as $picker) :
                $user_id = $picker['user_id'];
                $pos = $picker['position']; ?>
                <li class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
                  <span class="<?=(!$pos || $pos == $prevPos) ? 'off' : null ?>"><?=$pos?>.</span>
                  <a href="/users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a>
                  <span class="pull-right"><?=$picker['points']?></span>
                </li>
              <?php
              $prevPos = $pos;
              endforeach;
            else : ?>
              <li class="small">Not picked</li>
            <?php endif ?>
          </div>
        </div>
      <?php endif;
    endforeach ?>
  </div>

  <?php else : ?>

  <div class="row">
    <?php foreach ($riders as $rider) :
      $rider_id = $rider['id'];
      $results = getRiderResultsTotal($rider_id, $connect); ?>
      <a href="/riders.php?id=<?=$rider_id?>">
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="well text-center <?=(!$rider['active']) ? 'dim' : null ?>">
            <h4><?=$rider['name']?></h4>
            <img src="/graphics/nations/<?=$rider['nation_id']?>.png" class="flag" /> <?=($rider['wc_city_id']) ?  'Wild Card – '.getItem('name', 'cities', 'id', $rider['wc_city_id'], $connect) : '#'.$rider['number'] ?>
            <br>
            <img src="/graphics/riders/<?=$rider['number']?>.jpg" class="img-rounded shadow" style="max-width: 100%; margin: 10px 0;" />
            <div class="text-left">
              <li><strong>Total Points<span class="pull-right"><?=$results[0]?></span></strong></li>
              <?php for ($i=1; $i<=3; $i++) : ?>
                <li><span class="pull-right"><img src="/graphics/medals/<?=$i?>.png" alt="" class="flag flag-sm" /> <?=$results[$i]?></span><br style="clear: both;" /></li>
              <?php endfor ?>
              <li>Average Points<span class="pull-right"><?=($results[4]) ? round($results[0]/$results[4], 2) : null ?></span></li>
              <li>Times Picked<span class="pull-right"><?=$results[6]?></span></li>
              <li>Pick Ratio<span class="pull-right"><?=($results[4]) ? number_format($results[6]/$results[4]/$validUsers * 100).'%' : null ?></span></li>
            </div>
            <a class="btn btn-primary" role="button" href="/riders.php?id=<?=$rider_id?>" style="margin-top: 10px; width: 100%;">
              Full Stats <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
        </div>
      </a>
    <?php endforeach ?>
  </div>

  <div class="row">
    <?php foreach ($subRiders as $rider) : ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="well text-center">
        <h4><?=$rider['name']?></h4>
        <img src="/graphics/nations/<?=$rider['nation_id']?>.png" class="flag" /> #<?=$rider['number']?>
        <br>
        <img src="/graphics/riders/<?=$rider['number']?>.jpg" class="img-rounded shadow" style="max-width: 100%; margin: 10px 0;" />
        <li>Substitute Rider</li>
      </div>
    </div>
    <?php endforeach ?>
  </div>

<?php endif; include 'footer.php'; ?>
