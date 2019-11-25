<?php 
function dbConnect(){
  //DB接続準備
  $dsn = 'mysql:dbname=heroku_77fa3de2770433b;host=us-cdbr-iron-east-05.cleardb.net;charset=utf8';
  $user = 'b8a5e8febecf1c';
  $password = 'acf0c311';
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


