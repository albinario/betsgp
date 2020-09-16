<?php include 'inc/comments_backend.php';

if (isset($_GET['id'])) {
  $gp_id = $_GET['id'];
} else {
  $gp_id = 0;
} ?>

<div class="row">
  <div class="col-xs-12">
    <?php include 'inc/alerts.php'; ?>
  </div>
</div>

<div class="row" id="comments">
  <div class="col-sm-4">
    <div class="well">
      <form action="<?=($gp_id) ? '#comments' : null ?>" method="post">
        <input type="hidden" name="author" value="<?=$loggedInUser?>">
        <input type="hidden" name="gp_id" value="<?=$gp_id?>">
        <div class="form-group">
          <textarea name="comment" placeholder="Your Comment" class="form-control" style="height: 100px;" <?=(!$loggedInUser ? 'disabled' : null )?>></textarea>
        </div>
        <?php if ($loggedInUser) : ?>
          <button type="submit" class="btn btn-success text-uppercase form-control" name="add_comment">
            <span class="glyphicon glyphicon-edit"></span> Comment
          </button>
        <?php else : ?>
          <a href="/account.php" class="btn btn-success text-uppercase form-control" role="button" style="width: 100%;">
            <span class="glyphicon glyphicon-log-in"></span> Sign in to Comment or Reply
          </a>
        <?php endif ?>
      </form>
    </div>
  </div>

  <div class="col-sm-8">
    <?php if ($gp_id) {
      $gp = "AND gp_id = $gp_id";
    } else {
      $gp = '';
    }
    $comments = mysqli_query($connect, "SELECT * FROM comments WHERE reply = 0 ".$gp." ORDER BY id DESC");
    if (mysqli_num_rows($comments) == 0) : ?>
      <div class="list-group">
        <div class="list-group-item list-group-header">
          Comments will appear here
        </div>
      </div>
    <?php endif;
    foreach ($comments as $comment) : ?>
      <div class="list-group">
        <div class="list-group-item list-group-header">
          <span class="small">
            <strong><?=getUserName($comment['user_id'], $connect)?></strong>
            <?php if ($comment['gp_id'] && !$gp_id) :
              $city_id = getItem('city_id', 'gps', 'id', $comment['gp_id'], $connect); ?>
              in <a href="/gps.php?id=<?=$comment['gp_id']?>"><img src="/graphics/nations/<?=getItem('nation_id', 'cities', 'id', $city_id, $connect)?>.png" alt="" class="flag-sm"> GP <?=$comment['gp_id']?>. <?=getItem('name', 'cities', 'id', $city_id, $connect)?></a>
            <?php endif ?>
            <span class="pull-right small"><?=$comment['date_posted']?></span></span><br>
          <?=$comment['comment']?>
        </div>
        <?php $id = $comment['id'];
        $replies = mysqli_query($connect, "SELECT * FROM comments WHERE reply = $id ORDER BY id");
        foreach ($replies as $reply) : ?>
          <div class="list-group-item list-group-header" style="margin-left: 15px;">
            <span class="small"><strong><?=getUserName($reply['user_id'], $connect)?></strong><span class="pull-right small"><?=$reply['date_posted']?></span></span><br>
            <?=$reply['comment']?>
          </div>
        <?php endforeach;
        if ($loggedInUser) : ?>
          <div class="list-group-item list-group-header" style="margin-left: 15px;">
            <form action="<?=($gp_id) ? '#comments' : null ?>" method="post" class="form-inline">
              <input type="hidden" name="author" value="<?=$loggedInUser?>">
              <input type="hidden" name="reply" value="<?=$id?>">
              <div class="input-group col-xs-12">
                <input type="text" class="form-control input-sm" name="comment" placeholder="Your Reply">
                <span class="input-group-btn">
                  <button type="submit" class="btn btn-sm btn-success text-uppercase" name="add_reply"><span class="glyphicon glyphicon-edit"></span> Reply</button>
                </span>
              </div>
            </form>
          </div>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>
</div>
