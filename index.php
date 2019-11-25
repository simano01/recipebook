
<!-- head -->
<?php
  $siteTitle = '/ トップページ';
  require('php/head.php');
?>

  <!-- header -->
  <?php require('php/header.php'); ?>

  <!-- main -->
  <main id="index" class="bg-main">
    <div class="bg-mask">
      <div class="form-text">
        <p>ウェブ上に自分のレシピ本を作る</p>
        <div class="flex">
          <a href="php/signup.php"><button class="submit">ユーザー登録</button></a>
          <a href="php/login.php"><button class="submit">ログイン</button></a>
        </div>
      </div>
    </div>
  </main>

  <!-- footer -->
  <?php require('php/footer.php'); ?>
