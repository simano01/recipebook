<?php

require('function.php');

debug('===============================================================');
debug('==== パスワード変更ページ ====');
debug('===============================================================');
debugLogStart();

//ログイン認証
require('auth.php');

//===================================
//パスワード変更ページ処理
//===================================

if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST, true));

  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  //バリデーション　未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');

  if(empty($err_msg)){
    debug('未入力チェックOK！');

    validPass($pass_new, 'pass_new');

    //DBからユーザーデータを取得
    $userData = getUser($_SESSION['user_id']);
    debug('取得したユーザー情報：'.print_r($userData, true));

    //古いパスワードとDBパスワードを照合
    if(!password_verify($pass_old, $userData['password'])){
      $err_msg['pass_old'] = MSG09;
    }

    //古いパスワードと新しいパスワードが同じかチェック
    if($pass_old === $pass_new){
      $err_msg['pass_new'] = MSG10;
    }

    validMatch($pass_new, $pass_new_re, 'pass_new_re');

    if(empty($err_msg)){
      debug('バリデーションチェックOK！');

      try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET password = :pass WHERE user_id = :id';
        $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
          $_SESSION['msg_success'] = SUC01;
          header("Location:mypage.php");
        }

      }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }

  }
}
?>

<!-- head -->
<?php
  $siteTitle = '/ パスワード変更';
  require('head.php');
?>

  <!-- header -->
  <?php require('header.php'); ?>

  <!-- main -->
  <main class="main">
    <form class="form" action="" method="post">
      <h2 class="form-title">パスワード変更</h2>
      <div class="area-msg">
        <?php echo getErrMsg('common'); ?>
      </div>
      <label class="form-label <?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>">現在のパスワード（6文字以上）：<span class="badge notice">必須</span>
        <input type="text" name="pass_old" class="input" value="<?php if(!empty($_POST['pass_old'])) echo $_POST['pass_old']; ?>">
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('pass_old'); ?>
      </div>

      <label class="form-label <?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>">新しいパスワード（6文字以上）：<span class="badge notice">必須</span>
        <input type="password" name="pass_new" class="input" value="<?php if(!empty($_POST['pass_new'])) echo $_POST['pass_new']; ?>">
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('pass_new'); ?>
      </div>

      <label class="form-label <?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>">新しいパスワード再入力：<span class="badge notice">必須</span>
        <input type="password" name="pass_new_re" class="input" value="<?php if(!empty($_POST['pass_new_re'])) echo $_POST['pass_new_re']; ?>">
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('pass_new_re'); ?>
      </div>

      <input type="submit" name="submit" value="変更" class="submit">
    </form>
  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>
