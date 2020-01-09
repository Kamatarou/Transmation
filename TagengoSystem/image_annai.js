var src_canvas; 
var src_ctx;
var image;

window.onload = function (){
  src_canvas = document.getElementById("imgCanvas");
  src_ctx = src_canvas.getContext("2d");    
  
  image = document.getElementById("img_source");
  // console.log(image);
  src_canvas.width  = image.width;
  src_canvas.height = image.height;

  console.log(src_canvas);
    
  // キャンバスに画像を描画
  src_ctx.drawImage(image,0,0); 
}

function Draw(right,top,left,bottom){
  // image = document.getElementById("img_source");
  // src_canvas = document.getElementById("imgCanvas");
  // if (src_canvas.getContext){
  // src_ctx = src_canvas.getContext("2d");
  // src_canvas.width  = image.width;
  // src_canvas.height = image.height;
  src_ctx.beginPath();
  // src_ctx.drawImage(image,0,0); 
	src_ctx.moveTo(left,top);
  src_ctx.lineTo(right,top);
  src_ctx.lineTo(right,bottom);
  src_ctx.lineTo(left,bottom);
  src_ctx.closePath();
  src_ctx.globalAlpha = 0.5;
  src_ctx.stroke();
  src_ctx.fill();
  // }else{
  //   console.log("ctxがnullです。");
  // }

}