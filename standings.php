<?php $pageTitle = 'Standings';
include 'header.php';

if (isset($_GET['sort'])) {
  $sort = $_GET['sort'];
} else {
  $sort = 0;
} ?>

<div class="row">
  <div class="col-md-6">
    <div class="well">
      <h4>Standings</h4>
      <table class="table text-center inline">
        <tr class="small">
          <td colspan="2" class="text-right">Sort by <span class="glyphicon glyphicon-chevron-right"></span></td>
          <td>
            <a href="/standings.php">Points</a>
            <?=(!$sort) ? "<span class='glyphicon glyphicon-chevron-down small'></span>" : null ?>
          </td>
          <?php for ($i=1; $i<=3; $i++) : ?>
            <td>
              <a href="/standings.php?sort=<?=$i?>"><img src="/graphics/medals/<?=$i?>.png" alt="" height="20px"></a>
              <?=($sort == $i) ? "<span class='glyphicon glyphicon-chevron-down small'></span>" : null ?>
            </td>
          <?php endfor ?>
          <td class="hidden-xs">
            <a href="/standings.php?sort=4">Races</a>
            <?=($sort == 4) ? "<span class='glyphicon glyphicon-chevron-down small'></span>" : null ?>
          </td>
        </tr>

        <?php $prevResults = array();
        $pos = 0;
        $sumPoints = 0;
        foreach ($standings[$sort] as $user) :
          $user_id = $user['id'];
          if (isValid($user_id, $connect)) :
            $results = array($user['points'], $user['p_1'], $user['p_2'], $user['p_3'], $user['races']);
            if ($results == $prevResults) { $showPos = false; }
            $sumPoints += $user['points'];
            $pos++; ?>
            <tr class="<?=($user_id == $loggedInUser) ? 'user' : null ?>">
              <td><?=($results != $prevResults) ? $pos : null ?></td>
              <td class="text-left"><a href="users.php?id=<?=$user_id?>"><?=getUserName($user_id, $connect)?></a></td>
              <?php for ($i=0; $i<=4; $i++) : ?>
                <td class="<?=($i>3) ? 'hidden-xs' : null ?>"><?=$results[$i]?></td>
              <?php endfor ?>
            </tr>
            <?php $prevResults = $results;
          endif;
        endforeach ?>
        <tr>
          <td colspan="2" class="text-right small">Average Points:</td>
          <td><?=($pos) ? round($sumPoints/$pos, 2) : null ?></td>
          <td colspan="4"></td>
        </tr>
      </table>
    </div>
  </div>

  <div class="col-md-6">
    <div class="well">
      <h4>Riders</h4>
      <table class="table text-center inline">
        <tr class="small">
          <td colspan="2"></td>
          <td>Points</td>
          <?php for ($i=1; $i<=3; $i++) : ?>
            <td><img src="/graphics/medals/<?=$i?>.png" alt="" height="20px"></td>
          <?php endfor ?>
          <td class="hidden-xs">GP's</td>
          <td class="hidden-xs">Races</td>
          <td class="hidden-xs">Picked</td>
        </tr>
        <?php $riderArray = array();
        foreach ($riders as $rider) :
          $rider_id = $rider['id'];
          $results = getRiderResultsTotal($rider_id, $connect);
          $riderArray[$rider_id] = $results; ?>
        <?php endforeach;
        arsort($riderArray);
        $pos = 0;
        $prevValue = array();
        // $validUsers = validUsers($connect);
        foreach ($riderArray as $rider => $value) :
          $fullName = getItem('name', 'riders', 'id', $rider, $connect);
          $surName = getSurName($fullName);
          $pos++; ?>
          <tr>
            <td><?=($value != $prevValue) ? $pos : null ?></td>
            <td class="text-left">
              <a href="/riders.php?id=<?=$rider?>">
                <img src="/graphics/nations/<?=getItem('nation_id', 'riders', 'id', $rider, $connect)?>.png" alt="" class="flag flag-sm"> <span class="hidden-xs"><?=$fullName?></span><span class="hidden-sm hidden-md hidden-lg"><?=$surName?></span>
              </a>
            </td>
            <?php for ($i=0; $i<=6; $i++) : ?>
              <td class="<?=($i>3) ? 'hidden-xs' : null ?>"><?=$value[$i]?></td>
            <?php endfor ?>
          </tr>
        <?php $prevValue = $value;
        endforeach ?>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>