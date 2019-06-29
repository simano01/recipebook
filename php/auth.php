<?php

//===================================
//ログイン認証
//===================================
//ログインしている場合
if(!empty($_SESSION['login_date'])){
  debug('ログイン済みユーザーです。');

  //現在日時がログイン有効期限を超えていた場合
  if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
    debug('ログイン有効期限オーバーです。');
    session_destroy();//セッション削除
    header("Location:login.php");
  }else{
    debug('ログイン有効期限内です。');
    $_SESSION['login_date'] = time();//最終ログイン日時を現在日時に更新

    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('マイページへ遷移します。');
      header("Location:mypage.php");
    }
  }

}else{
  debug('未ログインユーザーです。');

  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    debug('ログインページへ遷移します。');
    header("Location:login.php");
  }
}
