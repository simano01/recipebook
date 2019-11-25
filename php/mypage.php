<?php

require('function.php');

debug('===============================================================');
debug('==== マイページ ====');
debug('===============================================================');
debugLogStart();

//ログイン認証
require('auth.php');

//===================================
//マイページ覧表示 画面処理
//===================================

//画面表示用データ取得
//===================================
//1ページあたりの表示件数
$listSpan = 8;
// カレントページ
$likeCurrentPageNum = (!empty($_GET['l_p'])) ? $_GET['l_p'] : 1;
$cookCurrentPageNum = (!empty($_GET['c_p'])) ? $_GET['c_p'] : 1;
// 現在の表示レコード先頭を算出
$likeCurrentMinNum = (($likeCurrentPageNum - 1) * $listSpan);
$cookCurrentMinNum = (($cookCurrentPageNum - 1) * $listSpan);
// DBからレシピデータを取得
$dbLikeData = getLikeList($_SESSION['user_id'], $likeCurrentMinNum, $listSpan);
$dbCookData = getCookList($_SESSION['user_id'], $cookCurrentMinNum, $listSpan);

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!-- head -->
<?php
  $siteTitle = '/ マイページ';
  require('head.php');
?>

  <!-- header -->
  <?php require('header.php'); ?>

  <!-- main -->
  <p id="js-show-msg" style="display:none;" class="msg-slide">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>
  <main class="main flex site-width colum-2" id="mypage">

    <section class="main">
      <div class="border">
        <h2 class="title flex title-label todayRecipe flex"><img src="../img/cookicon-after.png" alt=""><span class="text">今日のレシピ一覧</span></h2>
        <div class="recipeList-body flex cook">
          <?php
            if(!empty($dbCookData['data'])):
              foreach ($dbCookData['data'] as $key => $val):
          ?>
            <a class="panel" href="recipeDetail.php?r_id=<?php echo $val['recipe_id'].'&l_p='.$likeCurrentPageNum.'&c_p='.$cookCurrentPageNum; ?>">
              <div class="panel-head">
                <img src="../img/nikujaga.jpg" alt="<?php echo sanitize($val['recipe_name']); ?>">
              </div>
              <div class="panel-body">
                <p class="panel-title"><?php echo sanitize($val['recipe_name']); ?></p>
              </div>
            </a>
          <?php
              endforeach;
            endif;
          ?>
        </div>
        <div class="pagination">
          <ul class="pagination-list flex">
            <?php
              $pageColNum = 5;
              $totalPageNum = $dbCookData['total_page'];
              //現在のページが総ページ数と同じ かつ 総ページ数が表示項目数以上の場合
              if($cookCurrentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
                $minPageNum = $cookCurrentPageNum  - 4;
                $maxPageNum = $cookCurrentPageNum;
                //現在のページが総ページの１ページ前の場合
              }elseif($cookCurrentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum){
                $minPageNum = $cookCurrentPageNum - 3;
                $maxPageNum = $cookCurrentPageNum + 1;
                //現在のページが2ページの場合
              }elseif($cookCurrentPageNum == 2 && $totalPageNum >= $pageColNum){
                $minPageNum = $cookCurrentPageNum - 1;
                $maxPageNum = $cookCurrentPageNum + 3;
                //現在のページが1の場合
              }elseif($cookCurrentPageNum == 1 && $totalPageNum >= $pageColNum){
                $minPageNum = $cookCurrentPageNum;
                $maxPageNum = 5;
                //総ページ数が表示項目数よりも少ない場合
              }elseif($totalPageNum < $pageColNum){
                $minPageNum = 1;
                $maxPageNum = $totalPageNum;
              }else{
                $minPageNum = $cookCurrentPageNum - 2;
                $maxPageNum = $cookCurrentPageNum + 2;
              }
            ?>

            <?php if($cookCurrentPageNum != 1): ?>
              <li class="list-item"><a href="?c_p=1">&lt;</a></li>
            <?php endif; ?>
            <?php for($i = $minPageNum; $i <= $maxPageNum; $i++): ?>
              <li class="<?php if($cookCurrentPageNum == $i) echo 'active'; ?> list-item"><a href="?c_p=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <?php if($cookCurrentPageNum != $maxPageNum && $maxPageNum > 1): ?>
              <li class="list-item"><a href="?c_p=<?php echo $maxPageNum ?>">&gt;</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <div class="border">
        <h2 class="title title-label flex"><i class="far fa-heart"></i><span class="text">お気に入り一覧</span></h2>
        <div class="recipeList-body flex">
          <?php
            if(!empty($dbLikeData['data'])):
              foreach ($dbLikeData['data'] as $key => $val):
          ?>
            <a class="panel" href="recipeDetail.php?r_id=<?php echo $val['recipe_id'].'&l_P='.$likeCurrentPageNum.'&c_p='.$cookCurrentPageNum; ?>">
              <div class="panel-head">
                <img src="../img/nikujaga.jpg" alt="<?php echo sanitize($val['recipe_name']); ?>">
              </div>
              <div class="panel-body">
                <p class="panel-title"><?php echo sanitize($val['recipe_name']); ?></p>
              </div>
            </a>
          <?php
              endforeach;
            endif;
          ?>
        </div>
        <div class="pagination">
          <ul class="pagination-list flex">
            <?php
              $pageColNum = 5;
              $totalPageNum = $dbLikeData['total_page'];
              //現在のページが総ページ数と同じ かつ 総ページ数が表示項目数以上の場合
              if($likeCurrentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
                $minPageNum = $likeCurrentPageNum  - 4;
                $maxPageNum = $likeCurrentPageNum;
                //現在のページが総ページの１ページ前の場合
              }elseif($likeCurrentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum){
                $minPageNum = $likeCurrentPageNum - 3;
                $maxPageNum = $likeCurrentPageNum + 1;
                //現在のページが2ページの場合
              }elseif($likeCurrentPageNum == 2 && $totalPageNum >= $pageColNum){
                $minPageNum = $likeCurrentPageNum - 1;
                $maxPageNum = $likeCurrentPageNum + 3;
                //現在のページが1の場合
              }elseif($likeCurrentPageNum == 1 && $totalPageNum >= $pageColNum){
                $minPageNum = $likeCurrentPageNum;
                $maxPageNum = 5;
                //総ページ数が表示項目数よりも少ない場合
              }elseif($totalPageNum < $pageColNum){
                $minPageNum = 1;
                $maxPageNum = $totalPageNum;
              }else{
                $minPageNum = $likeCurrentPageNum - 2;
                $maxPageNum = $likeCurrentPageNum + 2;
              }
            ?>

            <?php if($likeCurrentPageNum != 1): ?>
              <li class="list-item"><a href="?l_p=1">&lt;</a></li>
            <?php endif; ?>
            <?php for($i = $minPageNum; $i <= $maxPageNum; $i++): ?>
              <li class="<?php if($likeCurrentPageNum == $i) echo 'active'; ?> list-item"><a href="?l_p=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <?php if($likeCurrentPageNum != $maxPageNum && $maxPageNum > 1): ?>
              <li class="list-item"><a href="?l_p=<?php echo $maxPageNum ?>">&gt;</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </section>

    <section class="sidebar">
      <a href="passEdit.php" style="color: #333;">パスワード変更</a>
      <a href="logout.php" style="color: #333;">ログアウト</a>
      <a href="withdraw.php" style="color: #333;">退会</a>
    </section>

  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>
