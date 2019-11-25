<?php
  session_start();
  $_SESSION['path'] = 'index.php';
?>

<!-- head -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>RecipeBook / トップページ</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Kosugi+Maru|Lobster|Sawarabi+Gothic" rel="stylesheet">
  <link rel="stylesheet" href="./scss css/reset.css">
  <meta name="viewport" content="width=device-width">
  <link rel="stylesheet" href="./scss css/style.min.css">
  <script src="./js/vendor/jquery-2.2.2.min.js"></script>
  <script src="./js/app.js"></script>
</head>
<body>

  <!-- header -->
  <?php require('php/header.php'); ?>

  <!-- main -->
  <main id="index" class="bg-main">
    <div class="bg-mask">
      <div class="form-text">
        <p>WEB上に自分のレシピ本を作る</p>
        <div class="flex">
          <a href="php/signup.php"><button class="submit">ユーザー登録</button></a>
          <a href="php/login.php"><button class="submit">ログイン</button></a>
        </div>
      </div>
    </div>
  </main>

  <!-- footer -->
  <?php require('php/footer.php'); ?>
