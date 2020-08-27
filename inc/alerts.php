<?php
if ($successes) :
  foreach ($successes as $success) : ?>
    <div class="alert alert-success"><span class="glyphicon glyphicon-thumbs-up"></span> <?=$success?></div>
  <?php endforeach;
endif;

if ($errors) :
  foreach ($errors as $error) : ?>
    <div class="alert alert-danger"><span class="glyphicon glyphicon-thumbs-down"></span> <?=$error?></div>
  <?php endforeach;
endif;

if ($infos) :
  foreach ($infos as $info) : ?>
    <div class="alert alert-info"><span class="glyphicon glyphicon-hand-right"></span> <?=$info?></div>
  <?php endforeach;
endif;

if (isset($_POST['insert_picks']) || isset($_POST['update_picks'])) :
  if ($gpSuccesses[$gp_id]) :
    foreach ($gpSuccesses[$gp_id] as $success) : ?>
      <div class="alert alert-success"><span class="glyphicon glyphicon-thumbs-up"></span> <?=$success?></div>
    <?php endforeach;
  endif;

  if ($gpErrors[$gp_id]) :
    foreach ($gpErrors[$gp_id] as $error) : ?>
      <div class="alert alert-danger"><span class="glyphicon glyphicon-thumbs-down"></span> <?=$error?></div>
    <?php endforeach;
  endif;
endif
?>
