    </div> <!-- end container started in header.php -->

    <footer class="footer">
      <div class="container">
        <!-- <form class="pull-left" action="#" method="post">
          <select class="form-control" name="division_id" id="division_id" onchange="submit();">
            <option class="form-control" value="">Filter Division</option>
            <?php foreach ($divisions as $division) : ?>
              <option class="form-control" value="<?=$division['id']?>"><?=$division['name']?></option>
            <?php endforeach ?>
          </select>
          <?php if (isset($_SESSION['division_id'])) : ?>
            <br />
            <button type="submit" name="unset_division" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-remove"></span> Clear Filter</button>
          <?php endif ?>
        </form> -->

      <div class="pull-right text-right">
        <?php if ($loggedInUser && isAdmin($loggedInUser, $connect)) : ?>
          <a href="admin.php"><button class="btn btn-primary text-uppercase"><span class="glyphicon glyphicon-wrench"></span> Admin</button></a><br/><br/>
        <?php endif ?>
        Albin Lindeborg <span class="glyphicon glyphicon-copyright-mark"></span> 2020<br/>All rights reserved</div>
      </div>
    </footer>

    <script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
    <script src="js/bootstrap.js"></script>
    <!-- <script>
        $("[data-toggle='tooltip']").tooltip();
        $("[data-toggle='popover']").popover();
    </script> -->
  </body>
</html>
