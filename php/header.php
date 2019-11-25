<header id="header">
 <div class="site-width">
  <div class="header-logo-right">
    <div class="logo-right_img">
      <img src="../img/food.png">
    </div>
    <div class="logo-right_text">
      <span class="sp-none">自分だけのレシピ本</span>
    <h1><a href="<?php echo (empty($_SESSION['user_id'])) ? 'index.php' : 'mypage.php'; ?>">Recipe Book</a></h1>
    </div>
  </div>

  <div class="<?php if(!empty($_SESSION['user_id'])){ echo 'menu-trigger js-sp-menu';} ?>">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <nav class="header-logo-left <?php if(!empty($_SESSION['user_id'])){ echo 'nav-menu js-sp-menu-target';} ?>">
    <ul>
      <?php
        if(empty($_SESSION['user_id']) && $_SESSION['path'] !== 'index.php'){
      ?>
          <li class="sp-none"><a href="login.php">ログイン</a></li>
          <li class="sp-none"><a href="signup.php">ユーザー登録</a></li>
      <?php
        }elseif($_SESSION['path'] !== 'index.php'){
      ?>
          <li><a href="mypage.php">マイページ</a></li>
          <li><a href="recipeRegister.php">レシピ登録</a></li>
          <li><a href="recipeList.php">レシピ一覧</a></li>
      <?php
        }
      ?>
    </ul>
  </nav>
 </div>
</header>
