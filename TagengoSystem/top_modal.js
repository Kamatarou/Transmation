var sX_syncerModal = 0 ;
var sY_syncerModal = 0 ;

$(function(){

    //グローバル変数
    var nowModalSyncer = null ;		//現在開かれているモーダルコンテンツ
    var modalClassSyncer = "modal-syncer" ;		//モーダルを開くリンクに付けるクラス名
    
    //モーダルのリンクを取得する
    var modals = document.getElementsByClassName( modalClassSyncer ) ;
    
    //モーダルウィンドウを出現させるクリックイベント
    for(var i=0,l=modals.length; l>i; i++){
    
        //全てのリンクにタッチイベントを設定する
        modals[i].onclick = function(){

            //すでにモーダルが表示されていないかチェック(id="modal-overlay"というIDが付いている要素を探す)
            var check = document.getElementById("modal-overlay");
            // console.log(check);
            //nullになっていなければ現在何らかのモーダルが開かれている
            if( check !== null ){
              //開いてるモーダルのコンテンツをフェードアウト
              $( nowModalSyncer ).fadeOut( "fast" ) ;
      
                    //[#modal-overlay]を削除する
                    //$( '#modal-overlay' ).remove() ;

            }
    
            //ボタンからフォーカスを外す
            this.blur() ;
    
            //ターゲットとなるコンテンツを確認
            var target = this.getAttribute( "data-target" ) ;
    
            //ターゲットが存在しなければ終了
            if( typeof( target )=="undefined" || !target || target==null ){
                return false ;
            }
    
            //コンテンツとなる要素を取得
            nowModalSyncer = document.getElementById( target ) ;
    
            //ターゲットが存在しなければ終了
            if( nowModalSyncer == null ){
                return false ;
            }
    
            //キーボード操作などにより、オーバーレイが多重起動するのを防止する
            // if( $( "#modal-overlay" )[0] ) return false ;		//新しくモーダルウィンドウを起動しない
            if($("#modal-overlay")[0]) $("#modal-overlay").remove() ;		//現在のモーダルウィンドウを削除して新しく起動する
    
            //スクロール位置を記録する
            var dElm = document.documentElement , dBody = document.body;
            sX_syncerModal = dElm.scrollLeft || dBody.scrollLeft;	//現在位置のX座標
            sY_syncerModal = dElm.scrollTop || dBody.scrollTop;		//現在位置のY座標

            //オーバーレイを出現させる
            $( "body" ).append( '<div id="modal-overlay"></div>' ) ;
            $( "#modal-overlay" ).fadeIn( "fast" ) ;
    
            //コンテンツをセンタリングする
            centeringModalSyncer() ;
    
            //コンテンツをフェードインする
            $( nowModalSyncer ).fadeIn( "fast" ) ;
    
            //[#modal-overlay]、または[.modal-close]をクリックしたら…
            $( /*"#modal-overlay,*/".modal-close" ).unbind().click( function() {
    
                //スクロール位置を戻す
                window.scrollTo( sX_syncerModal , sY_syncerModal );
                    
                //[#modal-content]と[#modal-overlay]をフェードアウトした後に…
                $( "#" + target + ",#modal-overlay" ).fadeOut( "fast" , function() {
    
                    //[#modal-overlay]を削除する
                    $( '#modal-overlay' ).remove() ;
    
                } ) ;
    
                //現在のコンテンツ情報を削除
                nowModalSyncer = null ;
    
            } ) ;
    
        }
    
    }

    //リサイズされたら、センタリングをする関数[centeringModalSyncer()]を実行する
    $( window ).resize( centeringModalSyncer ) ;
    
    //センタリングを実行する関数
    function centeringModalSyncer() {

        //モーダルウィンドウが開いてなければ終了
        if( nowModalSyncer == null ) return false ;

        //画面(ウィンドウ)の幅、高さを取得
        var w = $( window ).width() ;
        var h = $( window ).height() ;

        //コンテンツ(#modal-content)の幅、高さを取得
        // jQueryのバージョンによっては、引数[{margin:true}]を指定した時、不具合を起こします。
        var cw = $( nowModalSyncer ).outerWidth() ;
        var ch = $( nowModalSyncer ).outerHeight() ;

        //センタリングを実行する
        $( nowModalSyncer ).css( {"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"} ) ;

    }
    
} ) ;