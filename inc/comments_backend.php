<?php

if (isset($_POST['add_comment'])) {
  $author = $_POST['author'];
  $gp_id = $_POST['gp_id'];
  $comment = mysqli_real_escape_string($connect, $_POST['comment']);
  if (empty($comment)) { array_push($errors, "Comment field is empty"); }
  if (!$errors) {
    mysqli_query($connect, "INSERT INTO comments (user_id, comment, gp_id, reply) VALUES ('$author', '$comment', '$gp_id', 0)");
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
