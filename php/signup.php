<?php

require('function.php');

debug('===============================================================');
debug('==== ユーザー登録ページ ====');
debug('===============================================================');
debugLogStart();

//post送信されていた場合
if(!empty($_POST)){

  //変数にユーザー情報を代入
  $loginUser_id = $_POST['loginUser_id'];
  $pass = $_POST['pass'];
  $re_pass = $_POST['re_pass'];

  //バリデーションチェック（未入力チェック）
  validRequired($loginUser_id, 'loginUser_id');
  validRequired($pass, 'pass');
  validRequired($re_pass, 're_pass');

  if(empty($err_msg)){
    //ユーザーIDのバリデーションチェック
    validId($loginUser_id, 'loginUser_id');

    //パスワードのバリデーションチェック
    validPass($pass, 'pass');
    //パスワード再入力の一致チェック
    validMatch($pass, $re_pass, 're_pass');

    if(empty($err_msg)){
      //例外処理
      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO users (loginUser_id,password,login_time,create_date) VALUES(:loginUser_id,:password,:login_time,:create_date)';
        $data = array(':loginUser_id' => $loginUser_id, ':password' => password_hash($pass, PASSWORD_DEFAULT),
                      ':login_time' => date('Y-m-d H:i:s'),
                      ':create_date' => date('Y-m-d H:i:s'));
        //クエリ実行
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
          $sesLimit = 60*60;
          $_SESSION['login_date'] = time();
          $_SESSION['login_limit'] = $sesLimit;
          $_SESSION['user_id'] = $dbh -> lastInsertID();

          debug('セッションの中身:'.print_r($_SESSION,true));
          header("Location:mypage.php");
        }

      }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}

?>

<!-- head -->
<?php
 $siteTitle = '/ ユーザー登録';
 require('head.php');
?>

  <!-- header -->
  <?php require('header.php');?>

  <!-- main -->
  <main id="signup">
    <div class="bg-mask">
      <form class="form" action="" method="post">
        <h2 class="form-title">ユーザー登録</h2>
        <div class="area-msg">
          <?php echo getErrMsg('common'); ?>
        </div>
        <label class="form-label <?php if(!empty($err_msg['loginUser_id'])) echo 'err'; ?>">ユーザーID(半角英数字) <span class="badge notice">必須</span>
          <input type="text" name="loginUser_id" class="input" value="<?php if(!empty($_POST['loginUser_id'])) echo $_POST['loginUser_id']; ?>">
        </label>
        <div class="area-msg">
          <?php echo getErrMsg('loginUser_id'); ?>
        </div>

        <label class="form-label <?php if(!empty($err_msg['pass'])) echo 'err'; ?>">パスワード(6文字以上) <span class="badge notice">必須</span>
          <input type="password" name="pass" class="input" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
        </label>
        <div class="area-msg">
          <?php echo getErrMsg('pass'); ?>
        </div>

        <label class="form-label <?php if(!empty($err_msg['re_pass'])) echo 'err'; ?>">パスワード再入力 <span class="badge notice">必須</span>
          <input type="password" name="re_pass" class="input" value="<?php if(!empty($_POST['re_pass'])) echo $_POST['re_pass']; ?>">
        </label>
        <div class="area-msg">
          <?php echo getErrMsg('re_pass'); ?>
        </div>

        <input type="submit" name="submit" value="登録" class="submit">
        <a href="login.php" class="link">&lt; ログイン画面に遷移する</a>
      </form>
    </div>
  </main>


  <!-- footer -->
  <?php require('footer.php'); ?>
