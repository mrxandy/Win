Mo.ready(function(){
        Mo("#form").bind('submit',function( index, e ){

			Mo.Event( e ).stop();
			var form = this;
			
			var result= Mo.ValidForm( this, function(i){
				/*Î´¼ÓÔØ Effect ×é¼þ*/
				if( Mo.Array(Mo.plugin).indexOf('ui') == -1 ){
					alert(i);
				}else{
					Mo.Message( 'error', i, 3, { "unique" : "error", "center" : true } );
				}
			});
			result && form.submit();
        });
});
