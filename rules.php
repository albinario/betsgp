<?php $pageTitle = "Rules";
include 'header.php'; ?>

<div class="row">
  <div class="col-sm-6 col-md-4">
    <div class="list-group">
      <div class="list-group-item list-group-header"><h4><img src="<?=$eventLogo?>" alt="" class="flag"> <?=$eventTitle?></h4></div>
      <div class="list-group-item list-group-header">Pick 3 riders in each Grand Prix</div>
      <div class="list-group-item list-group-header">Collect the points that your 3 riders take in each Grand Prix</div>
      <div class="list-group-item list-group-header">You can change your picked riders as many times as you like, up until the start of each Grand Prix</div>
      <div class="list-group-item list-group-header">All rider points are calculated by race points, and NOT following the new Grand Prix point structure</div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="list-group small">
      <div class="list-group-item list-group-header"><h4>Race Points</h4></div>
      <div class="list-group-item list-group-header">Winner<span class="pull-right">3 pts</span></div>
      <div class="list-group-item list-group-header">2nd<span class="pull-right">2 pts</span></div>
      <div class="list-group-item list-group-header">3rd<span class="pull-right">1 pts</span></div>
      <div class="list-group-item list-group-header">4th<span class="pull-right">0 pts</span></div>
      <div class="list-group-item list-group-header">This includes all qualifying races, semifinals and final</div>
    </div>
  </div>
  <div class="col-sm-6 col-md-4">
    <div class="list-group small">
      <div class="list-group-item list-group-header"><h4>Standings sorting order</h4></div>
      <div class="list-group-item list-group-header">1<span class="pull-right">Total Points</span></div>
      <div class="list-group-item list-group-header">2<span class="pull-right">GP Wins <img src="/graphics/medals/1.png" alt="" class="flag-sm"></span></div>
      <div class="list-group-item list-group-header">3<span class="pull-right">2nd places <img src="/graphics/medals/2.png" alt="" class="flag-sm"></span></div>
      <div class="list-group-item list-group-header">4<span class="pull-right">3rd places <img src="/graphics/medals/3.png" alt="" class="flag-sm"></span></div>
      <div class="list-group-item list-group-header">5<span class="pull-right">Finished Races</span></div>
    </div>
  </div>
</div>

<?php include 'footer.php' ?>
