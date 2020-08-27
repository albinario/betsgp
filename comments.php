<?php $pageTitle = 'Comments';
include "header.php";

if (isset($_POST['add_comment'])) {
  $author = $_POST['author'];
  $comment = mysqli_real_escape_string($connect, $_POST['comment']);
  if (empty($comment)) { array_push($errors, "Comment field is empty"); }
  if (!$errors) {
    mysqli_query($connect, "INSERT INTO comments (user_id, comment, reply) VALUES ('$author', '$comment', 0)");
  }
}

if (isset($_POST['add_reply'])) {
  $author = $_POST['author'];
  $reply = $_POST['reply'];
  $comment = mysqli_real_escape_string($connect, $_POST['comment']);
  if (empty($comment)) { array_push($errors, "Reply field is empty"); }
  if (!$errors) {
    mysqli_query($connect, "INSERT INTO comments (user_id, comment, reply) VALUES ('$author', '$comment', '$reply')");
  }
} ?>

<div class="row">
  <div class="col-xs-12">
    <?php include 'inc/alerts.php'; ?>
  </div>
</div>

<div class="row">
  <div class="col-sm-4">
    <div class="well">
      <form action="" method="post">
        <input type="hidden" name="author" value="<?=$loggedInUser?>">
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
    <?php $comments = mysqli_query($connect, "SELECT * FROM comments WHERE reply = 0 ORDER BY id DESC");
    foreach ($comments as $comment) : ?>
      <div class="list-group">
        <div class="list-group-item list-group-header">
          <span class="small"><strong><?=getUserName($comment['user_id'], $connect)?></strong><span class="pull-right small"><?=$comment['date_posted']?></span></span><br>
          <?=$comment['comment']?>
        </div>
        <?php $id = $comment['id'];
        $replies = mysqli_query($connect, "SELECT * FROM comments WHERE reply = $id ORDER BY id DESC");
        foreach ($replies as $reply) : ?>
          <div class="list-group-item list-group-header" style="margin-left: 15px;">
            <span class="small"><strong><?=getUserName($reply['user_id'], $connect)?></strong><span class="pull-right small"><?=$reply['date_posted']?></span></span><br>
            <?=$reply['comment']?>
          </div>
        <?php endforeach;
        if ($loggedInUser) : ?>
          <div class="list-group-item list-group-header" style="margin-left: 15px;">
            <form action="" method="post" class="form-inline">
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

<?php include "footer.php"; ?>
