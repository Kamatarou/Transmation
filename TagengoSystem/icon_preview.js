//「画像をアップロード」が選択されたかどうかのフラグ
var flg = 0;
//最後に操作したラジオボタンの番号
var last_no = 0;

function iconChange( no ){
  last_no = no;

  icon = document.getElementById('icon[' + no + ']');
  // console.log(icon);

  if(flg == 0){
    preview = document.getElementById('preview');
    // console.log(preview);

    preview.src = icon.src;
  }

  if(flg == 1){
    button = document.getElementById('changeB');
    console.log(changeB);
    uploadzone = document.getElementById('uploadZone');
    // document.getElementById('uploadZone').style.display = 'none';
    
    button.innerHTML = "<img src=\"" + icon.src +"\" id=\"preview\" width=\"auto\" height=\"48\" style=\"float: left;\">";
    uploadZone.innerHTML = "";
  }

  flg = 0;
  
}

function labelChange(){
  label = document.getElementById('cancelLabel');

  label.htmlFor ="inputIcon[" + last_no + "]";
}

function buttonChange(){
  button = document.getElementById('changeB');
  // console.log(changeB);
  uploadzone = document.getElementById('uploadZone');
  // document.getElementById('uploadZone').style.display = 'block';

  uploadZone.innerHTML = "<button type=\"button\">アップロードする<br>画像を選ぶ(未選択)</button><br>";
  button.innerHTML = "<label class=\"up\" style=\"cursor: pointer;\">画像<br>選択</label>";
  flg = 1;
}

function choicePicture(){
  uplorder = document.getElementById("uploader");

  //アップロードする画像が選択されている場合のみボタンメッセージを変化させる
  //(何も選択されていない場合valueが空の文字列になる)
  if(uplorder.value !== ""){
    uploadzone = document.getElementById('uploadZone');
    uploadZone.innerHTML = "<button type=\"button\">アップロードする<br>画像を選ぶ(選択済み)</button><br>";
  }
}
