<?php

require('function.php');

debug('===============================================================');
debug('==== レシピ詳細ページ ====');
debug('===============================================================');
debugLogStart();

//ログイン認証
require('auth.php');

//===================================
//レシピ詳細表示 画面処理
//===================================

//画面表示用データ取得
//===================================
//商品IDのGETパラメータを取得
$r_id = (!empty($_GET['r_id'])) ? $_GET['r_id'] : '';
//DBから商品データを取得
$viewData = getRecipeOne($r_id, $_SESSION['user_id']);
//パラメータに不正な値が入っているかチェック
// if(empty($viewData)){
//   error_log('エラー発生：指定ページに不正な値が入りました');
//   header("Location:mypage.php");
// }
debug('取得したデータ：'.print_r($viewData,true));

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!-- head -->
<?php
  $siteTitle = '/ レシピ詳細';
  require('head.php');
?>

  <!-- header -->
  <?php require('header.php'); ?>

  <!-- main -->
  <main id="recipeDetail" class="main site-width">
    <div class="recipeDetail-top flex">
      <div class="recipe-title">
        <p class="badge normal"><?php echo sanitize($viewData['category_name']); ?></p>
        <h2 class="title"><?php echo sanitize($viewData['recipe_name']); ?></h2>
      </div>
      <div class="flex recipe-icon">
        <div class="icon cook">
          <p class="icon-badge">今日のレシピに登録</p>
          <img src="../img/cookicon-before.png" alt="" class="cook-icon before js-click-cook <?php if(isCook($_SESSION['user_id'], $viewData['recipe_id'])){echo 'active';} ?>" data-recipeid="<?php echo sanitize($viewData['recipe_id']); ?>">
          <img src="../img/cookicon-after.png" alt="" class="cook-icon after js-click-cook <?php if(isCook($_SESSION['user_id'], $viewData['recipe_id'])){echo 'active';} ?>" data-recipeid="<?php echo sanitize($viewData['recipe_id']); ?>">
        </div>
        <div class="icon favorite">
          <p class="icon-badge">お気に入りに登録</p>
          <i class="far fa-heart js-click-like <?php if(isLike($_SESSION['user_id'], $viewData['recipe_id'])){echo 'active';} ?>" data-recipeid="<?php echo sanitize($viewData['recipe_id']); ?>"></i>
        </div>
      </div>
    </div>

    <div class="recipeDetail-main">
      <div class="flex main-top">
        <div class="recipe-image">
          <img src="<?php echo sanitize($viewData['image']); ?>" alt="<?php echo sanitize($viewData['recipe_name']); ?>">
        </div>
        <div class="recipe-ingredient border">
          <h3 class="sub-title">材料<span class="n_people">（<?php echo sanitize($viewData['n_people']); ?>人分）</span></h3>
          <p class="text"><?php echo nl2br(sanitize($viewData['ingredient'])); ?></p>
        </div>
      </div>

      <div class="recipe-make border">
        <h3 class="sub-title">作り方</h3>
        <p name="make" class="text make" rows="10">
          <?php echo nl2br(sanitize($viewData['make'])); ?>
        </p>
      </div>
    </div>

    <div class="recipe-comment border">
      <h3 class="sub-title">コメント</h3>
      <p name="comment" class="text comment">
        <?php echo nl2br(sanitize($viewData['comment'])); ?>
      </p>
    </div>

    <div class="item-right">
      <a class="item-right" href="recipeRegister.php?r_id=<?php echo $r_id; ?>"><button type="button" name="button" class="submit button">レシピを編集する</button></a>
    </div>

    <div class="item-left">
      <a href="recipeList.php<?php echo appendGetParam(array('r_id')); ?>">&lt; レシピ一覧へ戻る</a>
    </div>
  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>
