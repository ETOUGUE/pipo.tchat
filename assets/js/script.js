(function($) {
	$.fn.Tchat=function(){
		var user;
		tchat();
		function createTchat(){
			$('body').append(
				$('<div class="tchat grid"/>').append(
					$('<div class="container"/>').append(
						$('<div class="header"/>').append(
							$('<h3/>').text(user.nom+" "+user.prenom)
						)
					).append(
						$('<div class="body row "/>')
					)
					.append(
						$('<div class="footer row "/>').append(
							$('<form/>').append(
								$('<input name="message" type="text" class="form-control" placeholder="entrer un message ici"/>').on('keyup',function(e){
									if(e. keyCode==13 && $(this).val()!=''){
										sendMessage($(this).val())
										$(this).val('');
										getMessage();
																			}
								})
							).on('submit',function(e){
								e.preventDefault();

							})
						)
					)
				)
			);
		}
		function tchat(){
			var data=new FormData();
			data.append('get_user','user');
			$.ajax({
				url:'controller.php',
				type:'POST',
				data:data,
				dataType:'json',
				processData: false,
				contentType: false,
				success:function(json){
					user=json;
					createTchat();
					getMessage();
					setInterval(getMessage,1000);
				},
				error:function(a,b,c){
					alert(a+b+c);
				}

			});
		}
		function sendMessage(message){
			var data=new FormData();
			data.append('add_message','add');
			data.append('message',message);
			var result=false;
			$.ajax({
				url:'controller.php',
				type:'POST',
				data:data,
				dataType:'json',
				processData: false,
				contentType: false,
				success:function(json){
					result= json;
				},
				error:function(a,b,c){
					result= false;
				}

			});
			return result;
		}
		function getMessage(){
			var data=new FormData();
			data.append('get_message','get');
			$.ajax({
				url:'controller.php',
				type:'POST',
				data:data,
				dataType:'json',
				processData: false,
				contentType: false,
				success:function(json){
					showMessages(json);
				},
				error:function(a,b,c){
					alert(a+b+c);
				}

			});
		}
		function showMessages(json){
			var $tchat=$('body .tchat');
			var $body=$tchat.find('.body');
			$body.html('');
			$.each(json,function($index,val){
				var type="rigth";
				if(val.login==user.login)
					type="left";
				$body.append(
					$('<div class="message '+type+'"/>').append(
						$('<div class="head"/>').append(
							$('<p class="autor"/>').text(val.nom+" "+val.prenom)
						).append(
							$('<p class="date"/>').text(val.date)
						)
					).append(
						$('<p class="message-content"/>').text(val.contenu)
					)
				);
			});
			$body.scrollTop(100000);
		}
	}
})(jQuery);