<?php

require('function.php');

debug('===============================================================');
debug('==== レシピ登録ページ ====');
debug('===============================================================');
debugLogStart();

//ログイン認証
require('auth.php');

//===================================
//レシピ登録 画面処理
//===================================

//画面表示用データ取得
//===================================
$r_id = (!empty($_GET['r_id'])) ? $_GET['r_id'] : '';
$dbFormData = (!empty($r_id)) ? getRecipe($_SESSION['user_id'], $r_id) : '';
$edit_flg = (empty($dbFormData)) ? false : true;
$dbCategoryData = getCategory();

debug('商品ID：'.$r_id);
debug('フォーム用DBデータ'.print_r($dbFormData,true));
debug('カテゴリデータ'.print_r($dbCategoryData,true));

//パラメータ改ざんチェック
//===================================
if(!empty($r_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。マイページへ遷移します。');
  header("Location:mypae.php");
}

//POST送信時処理
//===================================
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：'.print_r($_POST, true));
  debug('FILE情報：'.print_r($_FILES, true));

  $recipe_name = $_POST['recipe_name'];
  $category = $_POST['category_id'];
  $n_people = $_POST['n_people'];
  $ingredient = $_POST['ingredient'];
  $make = $_POST['make'];
  $image = (!empty($_FILES['image']['name'])) ? uploadImg($_FILES['image'], 'image') : '';
  $image = (empty($image) && !empty($dbFormData['image'])) ? $dbFormData['image'] : $image;
  debug('$image情報：'.$image);
  $comment = $_POST['comment'];

  if($edit_flg){
    if($dbFormData['recipe_name'] !== $recipe_name){
      validRequired($recipe_name, 'recipe_name');
      validMaxLen($recipe_name, 'recipe_name', 255);
    }
    if($dbFormData['category_id'] !== $category){
      validSelect($category, 'category_id');
    }
    if($dbFormData['n_people'] !== $n_people){
      validSelect($n_people, 'n_people');
    }
    if($dbFormData['ingredient'] !== $ingredient){
      validRequired($ingredient, 'ingredient');
      validMaxLen($ingredient, 'ingredient', 500);
    }
    if($dbFormData['make'] !== $make){
      validRequired($make, 'make');
      validMaxLen($make, 'make', 1000);
    }
    if($dbFormData['comment'] !== $comment){
      validMaxLen($comment, 'comment', 1000);
    }
  }else{
    validRequired($recipe_name, 'recipe_name');
    validSelect($category, 'category_id');
    validSelect($n_people, 'n_people');
    validRequired($ingredient, 'ingredient');
    validRequired($make, 'make');
    if(empty($err_msg)){
      validMaxLen($recipe_name, 'recipe_name', 255);
      validMaxLen($ingredient, 'ingredient', 500);
      validMaxLen($make, 'make', 1000);
      validMaxLen($comment, 'comment', 1000);
    }
  }

  if(empty($err_msg)){
    debug('バリデーションOKです');

    try{
      $dbh = dbConnect();
      if($edit_flg){
        debug('DB更新です');
        $sql = 'UPDATE recipe SET recipe_name = :recipe_name, category_id = :category_id, n_people = :n_people, ingredient = :ingredient, make = :make, image = :image, comment = :comment WHERE user_id = :u_id AND recipe_id = :r_id';
        $data = array(':recipe_name' => $recipe_name, ':category_id' => $category, ':n_people' => $n_people, ':ingredient' => $ingredient, ':make' => $make, ':image' => $image, ':comment' => $comment, ':u_id' => $_SESSION['user_id'], ':r_id' => $r_id);
      }else{
        debug('新規登録です');
        $sql = 'INSERT INTO recipe (recipe_name, category_id, n_people, ingredient, make, image, comment, user_id, create_date) VALUES (:recipe_name, :category_id, :n_people, :ingredient, :make, :image, :comment, :u_id, :create_date)';
        $data = array(':recipe_name' => $recipe_name, ':category_id' => $category, ':n_people' => $n_people, ':ingredient' => $ingredient, ':make' => $make, ':image' => $image, ':comment' => $comment, ':u_id' => $_SESSION['user_id'], ':create_date' => date('Y-m-d H:i:s'));
      }

      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_SESSION['msg_success'] = SUC02;
        if($edit_flg){
          debug('レシピ詳細ページへ遷移します');
          header("Location:recipeDetail.php?r_id=$r_id");
        }else{
          debug('マイページへ遷移します');
          header("Location:mypage.php");
        }
      }

    }catch (Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<!-- head -->
<?php
  $siteTitle = (!$edit_flg) ? '/ レシピ登録' : '/ レシピ編集';
  require('head.php');
?>

  <!-- header -->
  <?php require('header.php'); ?>

  <!-- main -->
  <main id="recipeRegister" class="main">
    <h2 class="form-title title-label pc-none"><?php echo (!$edit_flg) ? 'レシピの新規登録' : 'レシピ編集'; ?></h2>
    <form class="form" action="" method="post" enctype="multipart/form-data">
      <h2 class="form-title title-label sp-none"><?php echo (!$edit_flg) ? 'レシピの新規登録' : 'レシピ編集'; ?></h2>
      <div class="area-msg">
        <?php echo getErrMsg('common'); ?>
      </div>
      <label class="form-label <?php if(!empty($err_msg['recipe_name'])) echo 'err'; ?>">レシピ名：<span class="badge notice">必須</span>
        <input type="text" name="recipe_name" class="input" value="<?php echo getFormData('recipe_name'); ?>">
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('recipe_name'); ?>
      </div>

      <div class="flex m-b">
        <label class="form-label <?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">カテゴリ：<span class="badge notice">必須</span><br>
          <select class="select" name="category_id">
            <option value="0" <?php if(getFormData('category_id') == 0){ echo 'selected'; } ?> >選択してください</option>
            <?php
              foreach ($dbCategoryData as $key => $val) {
            ?>
              <option value="<?php echo $val['category_id'] ?>" <?php if(getFormData('category_id') == $val['category_id']) echo 'selected'; ?> >
                <?php echo $val['category_name']; ?>
              </option>
            <?php
              }
            ?>
          </select>
          <div class="area-msg">
            <?php echo getErrMsg('category_id'); ?>
          </div>
        </label>

        <label class="form-label m-l <?php if(!empty($err_msg['n_people'])) echo 'err'; ?>">何人分：<span class="badge notice">必須</span><br>
          <select class="select" name="n_people">
            <option value="0" <?php if(getFormData('n_people') == 0){ echo 'selected'; } ?> >選択してください</option>
            <option value="1" <?php if(getFormData('n_people') == 1){ echo 'selected'; } ?> >1</option>
            <option value="2" <?php if(getFormData('n_people') == 2){ echo 'selected'; } ?> >2</option>
            <option value="3" <?php if(getFormData('n_people') == 3){ echo 'selected'; } ?> >3</option>
            <option value="4" <?php if(getFormData('n_people') == 4){ echo 'selected'; } ?> >4</option>
            <option value="5" <?php if(getFormData('n_people') == 5){ echo 'selected'; } ?> >5</option>
          </select>
          <div class="area-msg">
            <?php echo getErrMsg('n_people'); ?>
          </div>
        </label>
      </div>

      <label class="form-label <?php if(!empty($err_msg['ingredient'])) echo 'err'; ?>">材料：<span class="badge notice">必須</span>
        <textarea name="ingredient" class="textarea input ingredient"><?php echo getFormData('ingredient'); ?></textarea>
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('ingredient'); ?>
      </div>

      <label class="form-label <?php if(!empty($err_msg['make'])) echo 'err'; ?>">作り方：<span class="badge notice">必須</span>
        <textarea name="make" class="textarea input"><?php echo getFormData('make'); ?></textarea>
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('make'); ?>
      </div>

      <p class="form-label image">画像：<span class="badge notice">必須</span></p>
      <label class="form-label js-imgDrop <?php if(!empty($err_msg['image'])) echo 'err'; ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
        <input type="file" name="image" class="input-file js-input-file">
        <img src="<?php echo getFormData('image') ?>" alt="" class="js-prev-img" style="<?php if(empty(getFormData('image'))) echo 'display:none;' ?>">
        <p>ドラッグ＆ドロップ</p>
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('image'); ?>
      </div>

      <label class="form-label <?php if(!empty($err_msg['comment'])) echo 'err'; ?>">コメント：
        <textarea name="comment" class="textarea input"><?php echo getFormData('comment') ?></textarea>
      </label>
      <div class="area-msg">
        <?php echo getErrMsg('comment'); ?>
      </div>

      <input type="submit" name="submit" value="<?php echo (!$edit_flg)? '登録' : '変更'; ?>" class="submit">
    </form>
  </main>

  <!-- footer -->
  <?php require('footer.php'); ?>
