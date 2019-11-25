$(function(){
  //メッセージ表示
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();
  if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
    $jsShowMsg.slideToggle('slow');
    setTimeout(function(){ $jsShowMsg.slideToggle('slow'); }, 5000);
  }

  //画像ライブプレビュー
  var $imgDrop = $('.js-imgDrop');
  var $fileInput = $('.js-input-file');
  $imgDrop.on('dragover',function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border', '3px #ccc dashed');
  });

  $fileInput.on('change',function(e){
    $imgDrop.css('border', 'none');
    $imgDrop.toggleClass('active');
    var file = this.files[0],
        $img = $(this).siblings('.js-prev-img'),
        fileReader = new FileReader();

    fileReader.onload = function(event){
      $img.attr('src', event.target.result).show();
    };

    fileReader.readAsDataURL(file);
  });

  //お気に入り/今日のレシピ登録・削除
  var $cook,
      $cookBfore,
      $cookAfter,
      $like,
      cookRecipeId,
      likeRecipeId;
  $cook = $('.js-click-cook') || null;
  $cookBefore = $('.js-click-cook.before');
  $cookAfter = $('.js-click-cook.after');
  $like = $('.js-click-like') || null;
  cookRecipeId = $cook.data('recipeid') || null;
  likeRecipeId = $like.data('recipeid') || null;

  if(cookRecipeId !== null){
    $cook.on('click', function(){
      $.ajax({
        type: "POST",
        url: "../php/ajax.php",
        data: {cookRecipeId : cookRecipeId}
      }).done(function(data){
        $cookBefore.toggleClass('active');
        $cookAfter.toggleClass('active');
      }).fail(function(msg){
        console.log('Ajax Cook Error');
      });
    });
  }

  if(likeRecipeId !== null){
    $like.on('click', function(){
      var $this = $(this);
      $.ajax({
        type: 'POST',
        url: '../php/ajax.php',
        data: {likeRecipeId : likeRecipeId}
      }).done(function(data){
        $this.toggleClass('active');
        console.log(data);
      }).fail(function(msg){
        console.log('Ajax Like Error');
      });
    });
  }

  // spメニュー
  $('.js-sp-menu').on('click', function(){
    $(this).toggleClass('active');
    $('.js-sp-menu-target').toggleClass('active');
  });

  var $ftr = $('#footer');
  if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
    $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
  }

});
