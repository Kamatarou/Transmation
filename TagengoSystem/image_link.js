// キャンバス
var src_canvas; 
var src_ctx;
 
// イメージ
var image;
 
// 矩形用
var rect_MousedownFlg = false;
var rect_fx = 0;
var rect_fy = 0;
var rect_ex = 0;
var rect_ey = 0;
 
window.onload = function (){
  
  src_canvas = document.getElementById("SrcCanvas");
  src_ctx = src_canvas.getContext("2d");    
  
  image = document.getElementById("img_source");
  // console.log(image);
  src_canvas.width  = image.width;
  src_canvas.height = image.height;

  console.log(src_canvas);
    
  // キャンバスに画像を描画
  src_ctx.drawImage(image,0,0); 
}
 
// 色の反転
function getTurningAround(color) {
 
 // 灰色は白にする 
 if(color >= 88 && color <= 168){
   return 255;
 // 色を反転する  
 }else{
   return 255 - color;
 }
}
 
function OnMousedown(event) {
 
  rect_MousedownFlg = true;
  
  // 座標を求める
  var rect = event.target.getBoundingClientRect();
  rect_fx = event.clientX - rect.left;
  rect_fy = event.clientY - rect.top; 
  document.getElementById("xleft").value = rect_fx;
  document.getElementById("ytop").value = rect_fy;  
  
  // 矩形の枠色を反転させる  
  var imagedata = src_ctx.getImageData(rect_fx, rect_fy, 1, 1);   
  src_ctx.strokeStyle = 'rgb(' + getTurningAround(imagedata.data[0]) +
                           ',' + getTurningAround(imagedata.data[1]) + 
                           ',' + getTurningAround(imagedata.data[2]) + ')';  
  // 線の太さ                         
  src_ctx.lineWidth = 2; 
  
  // 矩形の枠線を点線にする
  src_ctx.setLineDash([2, 3]);                             
}
 
function OnMousemove(event) {
  
  if(rect_MousedownFlg){
    
    // 座標を求める
    var rect = event.target.getBoundingClientRect();
    rect_ex = event.clientX - rect.left;
    rect_ey = event.clientY - rect.top; 
    document.getElementById("right").value = rect_ex;
    document.getElementById("ybotom").value = rect_ey;  
 
    // 元画像の再描画
    src_ctx.drawImage(image,0,0);  
    
    // 矩形の描画
    src_ctx.beginPath();
 
      // // 上
      // src_ctx.moveTo(rect_fx,rect_fy);
      // src_ctx.lineTo(rect_ex,rect_fy);
 
      // // 下
      // src_ctx.moveTo(rect_fx,rect_ey);
      // src_ctx.lineTo(rect_ex,rect_ey);
 
      // // 右
      // src_ctx.moveTo(rect_ex,rect_fy);
      // src_ctx.lineTo(rect_ex,rect_ey);
 
      // // 左
      // src_ctx.moveTo(rect_fx,rect_fy);
      // src_ctx.lineTo(rect_fx,rect_ey);
      src_ctx.moveTo(rect_fx,rect_fy);
      src_ctx.lineTo(rect_fx,rect_ey);
      src_ctx.lineTo(rect_ex,rect_ey);
      src_ctx.lineTo(rect_ex,rect_fy);
      src_ctx.closePath();
      src_ctx.globalAlpha = 0.5;
    src_ctx.stroke();
    src_ctx.fill();
  }
}
 
function OnMouseup(event) {
  rect_MousedownFlg = false;
}
 
function onDragOver(event){ 
  event.preventDefault(); 
} 
  
function onDrop(event){
  onAddFile(event);
  event.preventDefault(); 
}  
 
// ユーザーによりファイルが追加された  
function onAddFile(event) {
  var files;
  var reader = new FileReader();
  
  if(event.target.files){
    files = event.target.files;
  }else{ 
    files = event.dataTransfer.files;   
  }    
 
  // ファイルが読み込まれた
  reader.onload = function (event) {
    
    // イメージが読み込まれた
    image.onload = function (){
      src_canvas.width  = image.width;
      src_canvas.height = image.height;
        
      // キャンバスに画像を描画
      src_ctx.drawImage(image,0,0); 
    };      
       
    // イメージが読み込めない
    image.onerror  = function (){
      alert('このファイルは読み込めません。');  
    };
 
    image.src = reader.result;       
  };
  
  if (files[0]){    
    reader.readAsDataURL(files[0]); 
    document.getElementById("img_source").value = '';
  }
  
}

function sizecheck(){  
  //img_sourceというIDがついたタグを探す
  image = document.getElementById('img_source');
  // console.log(image);
  src_canvas = document.getElementById("SrcCanvas");
  // console.log(src_canvas);
  src_ctx = src_canvas.getContext("2d");

  //変数の値をsrc.canvasのタグに反映
  src_canvas.width  = image.naturalWidth;
  src_canvas.height = image.naturalHeight;

  
  // キャンバスに画像を描画
  src_ctx.drawImage(image,0,0);
}

function rectGenerate(right,top,left,bottom){
  // 高さと幅の取得
  // var height = bottom - top;
  // var width = right - left;

  // 元画像の再描画
  // src_ctx.drawImage(image,0,0);
  
  // 矩形の描画
  // src_ctx.fillstyle = "rgba(255,0,0,0.8)";
  // src_ctx.fillrect(left , top , width , height);
  src_ctx.beginPath();
  src_ctx.moveTo(left,top);
  src_ctx.lineTo(right,top);
  src_ctx.lineTo(right,bottom);
  src_ctx.lineTo(left,bottom);
  src_ctx.closePath();
  src_ctx.globalAlpha = 0.5;
  src_ctx.stroke();
  src_ctx.fill();
}