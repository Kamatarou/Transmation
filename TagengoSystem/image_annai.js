function Draw(right,top,left,bottom){
  var src_canvas; 
  var src_ctx;
  var image;
  image = document.getElementById("img_source");
  src_canvas = document.getElementById("imgCanvas");
  if (src_canvas.getContext){
  src_ctx = src_canvas.getContext("2d");
  src_canvas.width  = image.width;
  src_canvas.height = image.height;
  src_ctx.drawImage(image,0,0); 
	src_ctx.moveTo(left,top);
  src_ctx.lineTo(left,bottom);
  src_ctx.lineTo(right,bottom);
  src_ctx.lineTo(right,top);
  src_ctx.globalAlpha = 0.5;
  src_ctx.stroke();
  src_ctx.fill();
  src_ctx.closePath();
  }else{
    console.log("ctxがnullです。");
  }

}