<?php

require('function.php');

debug('===============================================================');
debug('==== レシピ一覧ページ ====');
debug('===============================================================');
debugLogStart();

//ログイン認証
require('auth.php');

//===================================
//レシピ一覧表示 画面処理
//===================================

//画面表示用データ取得
//===================================
//GETパラメータ取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは1ページ目
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
$recipe_name = (!empty($_POST['recipe_name'])) ? $_POST['recipe_name'] : '';
debug('変数categoryの中身：'.$category);
debug('変数recipe_nameの中身：'.$recipe_name);
//1ページあたりの表示件数
$listSpan = 20;
//現在ページに表示するレコードの先頭が何番目かを算出
$currentMinNum = (($currentPageNum - 1) * $listSpan);
//DBから商品、カテゴリデータの取得
$dbRecipeData = getRecipeList($_SESSION['user_id'], $currentMinNum, $listSpan, $category, $recipe_name);
$dbCategoryData = getCategory();

debug('現在のページ：'.$currentPageNum);

//パラメータ改ざんチェック
//===================================
if(!is_int((int)$currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:mypage.php");
}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!-- head -->
<?php
  $siteTitle = '/ レシピ一覧';
  require('head.php');
?>

  <!-- header -->
  <?php require('header.php'); ?>

  <!-- main -->
  <main class="main flex site-width colum-2" id="recipeList">
    <section class="sidebar sp-none">
      <form class="recipe-search d-border" action="" method="get">
        <div class="selectbox">
          <h3 class="search-title">カテゴリー検索</h3>
          <select class="select" name="c_id">
            <option value="0" <?php if(getFormData('c_id',true) == 0){echo 'selected';} ?>>選択してください</option>
            <?php foreach ($dbCategoryData as $key => $val): ?>
            <option value="<?php echo $val['category_id']; ?>" <?php if(getFormData('c_id',true) == $val['category_id']){$getLink = 'c_id='.$val['category_id']; echo 'selected';} ?>><?php echo $val['category_name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <input type="submit" name="submit" value="検索する" class="submit">
      </form>
      <form class="recipe-search d-border" action="" method="post">
        <div class="selectbox">
          <h3 class="search-title">レシピ名で検索</h3>
          <input type="text" name="recipe_name" value="<?php if(!empty($_POST['recipe_name'])) echo $_POST['recipe_name']; ?>" placeholder="レシピ名" class="input">
        </div>
        <input type="submit" name="submit" value="検索する" class="submit">
      </form>
    </section>

    <section class="main">
      <div class="recipeList-title flex">
        <div class="recipeList-title-left">
          <span class="total-num"><?php echo ($dbRecipeData['total']); ?></span>件のレシピが見つかりました
        </div>
        <div class="recipeList-title-right">
          <span class="num"><?php echo $currentMinNum+1; ?></span> - <span class="num"><?php echo ($dbRecipeData['total'] < ($currentMinNum+$listSpan)) ? $dbRecipeData['total'] : $currentMinNum+$listSpan; ?></span>件 / <span class="num"><?php echo ($dbRecipeData['total']); ?></span>件中
        </div>
      </div>
      <section class="sidebar pc-none">
        <form class="recipe-search d-border flex" action="" method="get">
          <div class="selectbox">
            <h3 class="search-title">カテゴリー検索</h3>
            <select class="select" name="c_id">
              <option value="0" <?php if(getFormData('c_id',true) == 0){echo 'selected';} ?>>選択してください</option>
              <?php foreach ($dbCategoryData as $key => $val): ?>
              <option value="<?php echo $val['category_id']; ?>" <?php if(getFormData('c_id',true) == $val['category_id']){$getLink = 'c_id='.$val['category_id']; echo 'selected';} ?>><?php echo $val['category_name']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <input type="submit" name="submit" value="検索する" class="submit">
        </form>
        <form class="recipe-search d-border flex" action="" method="post">
          <div class="selectbox">
            <h3 class="search-title">レシピ名で検索</h3>
            <input type="text" name="recipe_name" value="<?php if(!empty($_POST['recipe_name'])) echo $_POST['recipe_name']; ?>" placeholder="レシピ名" class="input">
          </div>
          <input type="submit" name="submit" value="検索する" class="submit">
        </form>
      </section>
      <div class="recipeList-body">
        <div class="recipeList-body flex">
          <?php
            foreach ($dbRecipeData['data'] as $key => $val):
          ?>
            <a href="recipeDetail.php?r_id=<?php echo (empty($_GET['c_id'])) ? $val['recipe_id'].'&p='.$currentPageNum : $val['recipe_id'].'&p='.$currentPageNum.'&c_id='.$getLink; ?>" class="panel">
              <div class="panel-head">
                <img src="../img/nikujaga.jpg" alt="<?php echo sanitize($val['recipe_name']); ?>">
              </div>
              <div class="panel-body">
                <p class="panel-title"><?php echo sanitize($val['recipe_name']); ?></p>
              </div>
            </a>
          <?php
            endforeach;
          ?>
        </div>

        <div class="pagination">
          <ul class="pagination-list flex">
            <?php
              $pageColNum = 5;
              $totalPageNum = $dbRecipeData['total_page'];
              //現在のページが総ページ数と同じ かつ 総ページ数が表示項目数以上の場合
              if($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
                $minPageNum = $currentPageNum  - 4;
                $maxPageNum = $currentPageNum;
                //現在のページが総ページの１ページ前の場合
              }elseif($currentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum){
                $minPageNum = $currentPageNum - 3;
                $maxPageNum = $currentPageNum + 1;
                //現在のページが2ページの場合
              }elseif($currentPageNum == 2 && $totalPageNum >= $pageColNum){
                $minPageNum = $currentPageNum - 1;
                $maxPageNum = $currentPageNum + 3;
                //現在のページが1の場合
              }elseif($currentPageNum == 1 && $totalPageNum >= $pageColNum){
                $minPageNum = $currentPageNum;
                $maxPageNum = 5;
                //総ページ数が表示項目数よりも少ない場合
              }elseif($totalPageNum < $pageColNum){
                $minPageNum = 1;
                $maxPageNum = $totalPageNum;
              }else{
                $minPageNum = $currentPageNum - 2;
                $maxPageNum = $currentPageNum + 2;
              }
            ?>

            <?php if($currentPageNum != 1): ?>
              <li class="list-item"><a href="?p=1<?php if(!empty($getLink)) echo '&'.$getLink; ?>">&lt;</a></li>
            <?php endif; ?>
            <?php for($i = $minPageNum; $i <= $maxPageNum; $i++): ?>
              <li class="<?php if($currentPageNum == $i) echo 'active'; ?> list-item"><a href="?p=<?php echo (!empty($getLink))? $i.'&'.$getLink : $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <?php if($currentPageNum != $maxPageNum && $maxPageNum > 1): ?>
              <li class="list-item"><a href="?p=<?php echo (!empty($getLink))? $maxPageNum.'&'.$getLink : $maxPageNum ?>">&gt;</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </section>
  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>
