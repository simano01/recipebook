<?php

require('function.php');

debug('===============================================================');
debug('==== Ajax ====');
debug('===============================================================');
debugLogStart();

//===================================
//Ajax処理
//===================================
if(isset($_POST['cookRecipeId']) && isset($_SESSION['user_id'])){
  debug('POST送信があります');
  $r_id = $_POST['cookRecipeId'];
  debug('レシピID：'.$r_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM todaymenu WHERE recipe_id = :r_id AND user_id = :u_id';
    $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id']);

    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug('レコード数：'.$resultCount);

    if(!empty($resultCount)){
      //レコード削除
      $sql = 'DELETE FROM todaymenu WHERE recipe_id = :r_id AND user_id = :u_id';
      $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id']);

      $stmt = queryPost($dbh, $sql, $data);
    }else{
      //レコードを挿入する
      $sql = 'INSERT INTO todaymenu (recipe_id, user_id, create_date) VALUES (:r_id, :u_id, :date)';
      $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

      $stmt = queryPost($dbh, $sql, $data);
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

if(isset($_POST['likeRecipeId']) && isset($_SESSION['user_id'])){
  debug('POST送信があります');
  $r_id = $_POST['likeRecipeId'];
  debug('レシピID：'.$r_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM favorite WHERE recipe_id = :r_id AND user_id = :u_id';
    $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id']);

    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt->rowCount();
    debug('レコード数：'.$resultCount);

    if(!empty($resultCount)){
      //レコード削除
      $sql = 'DELETE FROM favorite WHERE recipe_id = :r_id AND user_id = :u_id';
      $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id']);

      $stmt = queryPost($dbh, $sql, $data);
    }else{
      //レコードを挿入する
      $sql = 'INSERT INTO favorite (recipe_id, user_id, create_date) VALUES (:r_id, :u_id, :date)';
      $data = array(':r_id' => $r_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

      $stmt = queryPost($dbh, $sql, $data);
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
debug('Ajax処理終了！');

?>
