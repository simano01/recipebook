<?php

require('function.php');

debug('===============================================================');
debug('==== ログインページ ====');
debug('===============================================================');
debugLogStart();

//ログイン認証
require('auth.php');

//===================================
//ログイン画面処理
//===================================
if(!empty($_POST)){
  debug('POST送信があります。');

  $loginUser_id = $_POST['loginUser_id'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;

  //バリデーションチェック
  //入力必須
  validRequired($loginUser_id, 'loginUser_id');
  validRequired($pass, 'pass');

    if(empty($err_msg)){
      debug('バリデーションチェックOKです。');

      try{
        $dbh = dbConnect();
        $sql = 'SELECT password,user_id FROM users WHERE loginUser_id = :loginUser_id AND delete_flg = 0';
        $data = array('loginUser_id' => $loginUser_id);

        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        debug('クエリ結果の中身:'.print_r($result,true));

        //パス照合
        if(!empty($result) && password_verify($pass, array_shift($result))){
          debug('パスワードとユーザーIDが合致しました。');

          //ログイン有効期限をデフォルトで1時間に設定
          $sesLimit = 60*60;
          $_SESSION['login_date'] = time();

          //ログイン保持にチェックがある場合
          if($pass_save){
            debug('ログイン保持にチェックがあります。');
            //ログイン有効期限を30日に延長
            $_SESSION['login_limit'] = $sesLimit * 24 * 60;
          }else{
            debug('ログイン保持にチェックがありません。');
            $_SESSION['login_limit'] = $sesLimit;
          }

          $_SESSION['user_id'] = $result['user_id'];
          debug('セッション変数の中身:'.print_r($_SESSION,true));
          debug('マイページへ遷移します。');
          header("Location:mypage.php");
        }else{
          debug('パスワードが合致しませんでした。');
          $err_msg['common'] = MSG08;
        }
      }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!-- head -->
<?php
 $siteTitle = '/ ログインページ';
 require('head.php');
?>

  <!-- header -->
  <?php require('header.php');?>

  <!-- main -->
  <main id="login">
    <div class="bg-mask">
      <form class="form" action="" method="post">
        <h2 class="form-title">ログイン</h2>
        <div class="area-msg">
          <?php echo getErrMsg('common'); ?>
        </div>
        <label class="form-label <?php if(!empty($err_msg['loginUser_id'])) echo 'err'; ?>">ユーザーID（半角英数字）<span class="badge notice">必須</span>
          <input type="text" name="loginUser_id" class="input" value="<?php if(!empty($_POST['loginUser_id'])) echo $_POST['loginUser_id']; ?>">
          <div class="area-msg">
            <?php echo getErrMsg('loginUser_id'); ?>
          </div>
        </label>
        <label class="form-label <?php if(!empty($err_msg['pass'])) echo 'err'; ?>">パスワード（6文字以上）<span class="badge notice">必須</span>
          <input type="password" name="pass" class="input">
          <div class="area-msg">
            <?php echo getErrMsg('pass'); ?>
          </div>
        </label>
        <input type="checkbox" name="pass_save" class="checkbox">次回から自動でログインする
        <input type="submit" name="submit" value="ログイン" class="submit">
        <a href="signup.php" class="link">&lt; ユーザー登録する</a>
      </form>
    </div>
  </main>


  <!-- footer -->
  <?php require('footer.php'); ?>
