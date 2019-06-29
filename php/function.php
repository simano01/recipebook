<?php

//===================================
//ログ
//===================================
//ログを取るか
ini_set('log_errors', 'on');
//ログの出力ファイルを指定
ini_set('error_log', '../php.log');

//===================================
//デバック
//===================================
$debug_flg = true;

function debug($str){
  global $debug_flg;
  if($debug_flg){
    error_log('デバック:'.$str);
  }
}


//===================================
//セッション準備と有効期限の延長
//===================================
//セッションファイルの置き場を変更1
session_save_path('../var/tmp');
//ガーベージコレクションが削除するセッションの有効期限を30日に変更
ini_set('session.gc_maxlifetime', 60*60*24*30);
//クッキーの有効期限を30日に延長（ブラウザを閉じてもセッションが削除されないように）
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティー対策）
session_regenerate_id();

//===================================
//画面表示処理開始ログ吐き出し関数
//===================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>画面表示処理開始');
  debug('セッションID:'.session_id());
  debug('セッション変数の中身:'.print_r($_SESSION, true));
  debug('現在日時タイムスタンプ:'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}

//===================================
//エラーメッセージの定数定義
//===================================
define('MSG01','入力必須です');
define('MSG02','半角英数字で入力してください');
define('MSG03','そのユーザーIDは既に登録されています');
define('MSG04','6文字以上で入力してください');
define('MSG05',$max.'文字以内で入力してください');
define('MSG06','パスワード（再入力）が合っていません');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','ユーザーIDまたはパスワードが違います');
define('MSG09','古いパスワードが違います');
define('MSG10','古いパスワードと同じです');
define('MSG11','正しくありません');
define('SUC01','パスワードを変更しました');
define('SUC02','レシピを登録しました');
define('SUC03','レシピを変更しました');

//===================================
//変数定義
//===================================
//エラーメッセージ格納用の配列
$err_msg = array();
$_SESSION['path'] = '';

//===================================
//バリデーションチェック関数
//===================================
//バリデーション関数（未入力チェック）
function validRequired($str, $key){
  global $err_msg;
  if(empty($str)){
    $err_msg[$key] = MSG01;
  }
}
//バリデーション関数（半角英数字の形式チェック）
function validHalf($str, $key){
  global $err_msg;
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    $err_msg[$key] = MSG02;
  }
}
//バリデーション関数（ユーザーID重複チェック）
function validUserIdDup($str, $key){
  global $err_msg;
  //例外処理
  try{
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE loginUser_id = :loginUser_id AND delete_flg = 0';
    $data = array(':loginUser_id' => $str);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($result['count(*)'])){
      $err_msg[$key] = MSG03;
    }
  }catch (Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
  global $err_msg;
  if(mb_strlen($str) < $min){
    $err_msg[$key] = MSG04;
  }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max){
  global $err_msg;
  if(mb_strlen($str) > $max){
    $err_msg[$key] = MSG05;
  }
}
//バリデーション関数（パス同値チェック）
function validMatch($str1, $str2, $key){
  global $err_msg;
  if($str1 !== $str2){
    $err_msg[$key] = MSG06;
  }
}
//バリデーション関数（パスワードチェック）
function validPass($str, $key){
  validHalf($str, $key);
  validMaxLen($str, $key, 255);
  validMinLen($str, $key);
}
function validId($str, $key){
  validHalf($str, $key);
  validUserIdDup($str, $key);
  validMaxLen($str, $key, 255);
}
//selectboxチェック
function validSelect($str, $key){
  global $err_msg;
  if(!preg_match("/^[1-9]+$/", $str)){
    $err_msg[$key] = MSG01;
  }
}
//エラーメッセージ表示
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}

//===================================
//データベース
//===================================
//DB接続関数
function dbConnect(){
  //DB接続準備
  $dsn = 'mysql:dbname=simano01_recipebook;host=mysql1.php.xdomain.ne.jp;charset=utf8';
  $user = 'simano01_111';
  $password = 'fyq42777';
  $options = array(
    //SQL実行失敗時にはエラーコードを吐き出す設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    //バッファードクエリを使う（一度に結果セットをすべて取得し、サーバー負荷を軽減）
    //SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
//SQL実行関数
function queryPost($dbh, $sql, $data){
  //クエリ―作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました');
    debug('失敗したSQL：'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリに成功しました！');
  return $stmt;
}

function getUser($u_id){
  debug('ユーザー情報を取得します。');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM users WHERE user_id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);

    //クエリ結果のデータを１レコード返却
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  }catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getRecipe($u_id, $r_id){
  debug('レシピ情報を取得します');
  debug('ユーザーID：'.$u_id);
  debug('レシピID：'.$r_id);

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM recipe WHERE user_id = :u_id AND recipe_id = :r_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id, ':r_id' => $r_id);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getRecipeList($u_id, $currentMinNum, $listSpan, $category, $recipe_name){
  debug('レシピ情報を取得します');

  try{
    $dbh = dbConnect();
    $sql = 'SELECT recipe_id FROM recipe WHERE user_id = :u_id AND delete_flg = 0';
    if(!empty($category)) $sql .= ' AND category_id ='.$category;
    if(!empty($recipe_name)) $sql .= ' AND recipe_name LIKE \''.$recipe_name.'\'';
    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);
    $rst['total'] = $stmt->rowCount(); //総レコード数
    debug('総レコード数：'.$rst['total']);
    $rst['total_page'] = ceil($rst['total']/$listSpan); //総ページ数

    //ページング用
    $sql = 'SELECT * FROM recipe WHERE user_id = :u_id AND delete_flg = 0';
    if(!empty($category)) $sql .= ' AND category_id ='.$category;
    if(!empty($recipe_name)) $sql .= ' AND recipe_name LIKE \''.$recipe_name.'\'';
    $sql .= ' LIMIT '.$listSpan.' OFFSET '.$currentMinNum;
    $data = array(':u_id' => $u_id);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }

  } catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getLikeList($u_id, $likeCurrentMinNum, $listSpan){
  debug('お気に入りレシピ情報を取得します');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT recipe_id FROM favorite WHERE user_id = :u_id';
    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);
    $rst['total'] = $stmt->rowCount();//総レコード数
    debug('お気に入りレシピの総レコード数：'.$rst['total']);
    $rst['total_page'] = ceil($rst['total']/$listSpan);//総ページ数

    //ページング用
    $sql = 'SELECT * FROM favorite LEFT JOIN recipe ON favorite.recipe_id = recipe.recipe_id WHERE recipe.user_id = :u_id';
    $sql .= ' LIMIT '.$listSpan.' OFFSET '.$likeCurrentMinNum;
    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getCookList($u_id, $cookCurrentMinNum, $listSpan){
  debug('今日のレシピ情報を取得します');

  try {
    $dbh = dbConnect();
    $sql = 'SELECT recipe_id FROM favorite WHERE user_id = :u_id';
    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);
    $rst['total'] = $stmt->rowCount();//総レコード数
    debug('今日のレシピの総レコード数：'.$rst['total']);
    $rst['total_page'] = ceil($rst['total']/$listSpan);//総ページ数

    //ページング用
    $sql = 'SELECT * FROM favorite LEFT JOIN recipe ON favorite.recipe_id = recipe.recipe_id WHERE recipe.user_id = :u_id';
    $sql .= ' LIMIT '.$listSpan.' OFFSET '.$cookCurrentMinNum;
    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getRecipeOne($r_id, $u_id){
  debug('レシピ情報を取得します');
  debug('レシピID：'.$r_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT recipe_id, recipe_name, n_people, ingredient, make, image, comment, user_id, category_name FROM recipe LEFT JOIN category ON recipe.category_id = category.category_id WHERE recipe_id = :r_id AND user_id = :u_id AND recipe.delete_flg = 0 AND category.delete_flg = 0';
    $data = array('r_id' => $r_id, ':u_id' => $u_id);
    debug('$dataの中身：'.$data);

    $stmt = queryPost($dbh, $sql, $data);
    debug('クエリ結果：'.$stmt->fetchAll());

    if($stmt){
      debug('クエリ成功だあぁ！やっほー！！');
      debug('クエリ結果：'.$stmt->fetch(PDO::FETCH_ASSOC));
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      debug('クエリが失敗したぜ！ガハハ');
      debug('クエリ結果：'.$stmt->fetch(PDO::FETCH_ASSOC));
      return false;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function getCategory(){
  debug('カテゴリー情報を取得します。');

  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM category';
    $data = array();

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function isCook($u_id, $r_id){
  debug('今日のレシピに登録されているか確認します！');
  debug('ユーザーID：'.$u_id);
  debug('レシピID：'.$r_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM todaymenu WHERE recipe_id = :r_id AND user_id = :u_id';
    $data = array(':u_id' => $u_id, ':r_id' => $r_id);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt->rowCount()){
      debug('登録済みです');
      return true;
    }else{
      debug('登録されていません');
      return false;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

function isLike($u_id, $r_id){
  debug('お気に入りに登録されているか確認します！');
  debug('ユーザーID：'.$u_id);
  debug('レシピID：'.$r_id);

  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM favorite WHERE recipe_id = :r_id AND user_id = :u_id';
    $data = array(':u_id' => $u_id, ':r_id' => $r_id);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt->rowCount()){
      debug('登録済みです');
      return true;
    }else{
      debug('登録されていません');
      return false;
    }

  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//===================================
//その他
//===================================
//フォーム入力保持
function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;

  if(!empty($dbFormData)){
    if(!empty($err_msg[$str])){
      if(!empty($method[$str])){
        return $method[$str];
      }else{
        return $dbFormData[$str];
      }
    }else{
      if(!empty($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return $method[$str];
      }else{
        return $dbFormData[$str];
      }
    }
  }else{
    if(!empty($method[$str])){
      return $method[$str];
    }
  }
}

//サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

//セッションを1回だけ取得できる
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}

//画像処理
function uploadImg($file, $key){
  debug('画像アップロード開始');
  debug('FILE情報：'.print_r($file, true));

  if(isset($file['error']) && is_int($file['error'])){
    try{
      switch ($file['error']) {
        case UPLOAD_ERR_OK:
          break;
        case UPLOAD_ERR_NO_FILE:
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:
          throw new RuntimeException('ファイルサイズが大きすぎます');
        case UPLOAD_ERR_FORM_SIZE:
          throw new RuntimeException('ファイルサイズが大きすぎます');
        default:
          throw new RuntimeException('その他のエラーが発生しました');
      }

      //exif_imagetype関数は「IMAGETYPE_GIF」「IMAGETYPE_JPEG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']);
      if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
        throw new RuntimeException('画像形式が未対応です');
      }

      $path = '../uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

      //ファイル移動
      if(!move_uploaded_file($file['tmp_name'], $path)){
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }

      //保存したファイルパスのパーミッション（権限）を変更する
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス：'.$path);
      return $path;

    } catch (RuntimeException $e){
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}

//画像処理
function uploadImgSample($file){
  debug('サンプル画像アップロード開始');
  debug('IMG情報：'.print_r($file, true));

  $path = '../uploads/'.sha1_file($file);
  $rst = move_uploaded_file($file, $path);

  //ファイル移動
  if(!$rst){
    debug('ファイル保存時にエラーが発生しました');
  }

  debug('ファイルは正常にアップロードされました');
  debug('ファイルパス：'.$rst);
  return $rst;
}

//画像表示用関数
function showImg($path){
  if(empty($path)){
    return '';
  }else{
    return $path;
  }
}

//GETパラメータ付与
function appendGetParam($arr_del_key = array()){
  if(!empty($_GET)){
    $str = '?';
    foreach($_GET as $key => $val){
      if(!in_array($key,$arr_del_key,true)){
        $str .= $key.'='.$val.'&';
      }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
  }
}
