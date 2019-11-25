<?php

require('function.php');

debug('===============================================================');
debug('==== 退会ページ ====');
debug('===============================================================');
debugLogStart();

//ログイン認証
require('auth.php');

//===================================
//退会画面処理
//===================================
if(!empty($_POST)){
  debug('POST送信があります。');

  try{
    $dbh = dbConnect();
    $sql = 'UPDATE users SET delete_flg = 1 WHERE user_id = :user_id';
    $data = array(':user_id' => $_SESSION['user_id']);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      session_destroy();
      debug('セッション変数の中身:'.print_r($_SESSION,true));
      debug('トップページへ遷移します。');
      header("Location:../index.php");
    }

  }catch (Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!-- head -->
<?php
  $siteTitle = '/ 退会';
  require('head.php');
?>

  <!-- header -->
  <?php require('header.php'); ?>

  <!-- main -->
  <main class="main">
    <h2>退会</h2>
    <p>退会をご希望の場合は、以下の「退会する」ボタンを押して下さい。</p>
    <form action="" method="post">
      <input type="submit" name="submit" value="退会する" class="submit withdraw-submit">
      <div class="area-msg">
        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
      </div>
    </form>
  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>
