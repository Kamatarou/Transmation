// 最後に確定したタイトルを一時的に保持しておくための変数
var temptext;

function getTitle( str ){
  // 最初に初期値として入力されている文字列を代入する
  temptext = str;
  // console.log("temptext = " + temptext);
}

function titlechange(){
  output1 = document.getElementById("output");
  output2 = document.getElementById("formin");
  input = document.forms.inputform.inputtext.value;

  // console.log(output1);
  // console.log(output2);
  // console.log(input);
  output1.innerText = input;
  output2.value = input;
  temptext = input;
}

function reset(){
  // 最後に確定した文字列にテキストボックス内の文字を戻す
  document.forms.inputform.inputtext.value = temptext;
}