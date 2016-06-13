function vote(elem,type,voted) {
	$this = $(elem);
	var id = $this.parent().parent().parent().attr('id');

	var add = 1;
	var minus = -1;
	var up = 'up';
	var down = 'down';
	if (voted == 'minus') {
		add = -1;
		minus = 1;
		up = 'down';
		down = 'up';
	}

	$.post(basePath+"/"+type+"/vote", {
		id: id,
		vote: voted
	},
	function(data) {
		var result = data.substr(0,1);
		var message = data.substr(1);

		if (result == 1) {

			if (!$("#"+id+" .questionsview_"+up+"").hasClass("voteselected")) {

				$("#"+id+" .questionsview_vote").html(parseInt($("#"+id+" .questionsview_vote").html())+add);

				if ($("#"+id+" .questionsview_"+down+"").hasClass("voteselected")) {
					$("#"+id+" .questionsview_vote").html(parseInt($("#"+id+" .questionsview_vote").html())+add);
					$("#"+id+" .questionsview_"+down+"").removeClass("voteselected");
				}

			} else {
				$("#"+id+" .questionsview_vote").html(parseInt($("#"+id+" .questionsview_vote").html())+minus);
			}

			$this.toggleClass("voteselected");
		}

		if (message != '') {
			$.fancyalert(message);
		}

	});
}

$(document).ready(function() {

	$(".questionsview_answer .questionsview_up").click(function() {
		vote(this,'answers','plus');
	});

	$(".questionsview_answer .questionsview_down").click(function() {
		vote(this,'answers','minus');
	});

	$(".questionsview_question .questionsview_up").click(function() {
		vote(this,'questions','plus');
	});

	$(".questionsview_question .questionsview_down").click(function() {
		vote(this,'questions','minus');
	});

	$(".questionsview_question .questionsview_fave").click(function() {
		$this = $(this);
		var id = $this.parent().parent().parent().attr('id');
		$.post(basePath+"/questions/fave", {
			id: id
		},
		function(data) {
			var result = data.substr(0,1);
			var message = data.substr(1);

			if (result == 1) {
				$this.toggleClass("voteselected");
			}

			$.fancyalert(message);

		});
	});


	$(".commentfave").click(function() {

		var id = $(this).attr('id');
		$this = $(this);

		$.post(basePath+"/comments/vote", {
			id: id
		},
		function(data) {

			var result = data.substr(0,1);
			var message = data.substr(1);

			if (result == "1") {
				if ($this.next('div').html() == '') {
					$this.next('div').html('0');
				}

				if (!$this.hasClass("voteselected")) {
					$this.next('div').html(parseInt($this.next('div').html())+1);
				} else {
					$this.next('div').html(parseInt($this.next('div').html())-1);
				}

				$this.toggleClass("voteselected");
			}

			if (message != '') {
				$.fancyalert(message);
			}

		});


	});

	$(".commentdel").click(function() {

		var answer = confirm("Delete this comment?")
		if (answer){
			var id = $(this).attr('id');
			$this = $(this);
			$.post(basePath+"/comments/del", {
				id: id
			},
			function(data) {

				var result = data.substr(0,1);
				var message = data.substr(1);

				if (result == "1") {
					$this.parent().fadeOut(2000);
				}

				if (message != '') {
					$.fancyalert(message);
				}

			});

		}


	});

	$(".answerdel").click(function() {

		var answer = confirm("Delete this answer?")
		if (answer){
			var id = $(this).attr('id');
			$this = $(this);
			$.post(basePath+"/answers/del", {
				id: id
			},
			function(data) {

				var result = data.substr(0,1);
				var message = data.substr(1);

				if (result == "1") {
					document.location.reload();
				}

				if (message != '') {
					$.fancyalert(message);
				}

			});

		}


	});


	var comments = $(".comments");
	$.each(comments, function() {
		var elements = ( $('.comment:gt(4)',$(this)).size());

		if (elements > 0) {
			$('.viewallcomments',$(this)).css('display','block');
			$('.viewallcomments a',$(this)).html('View all comments ('+elements+' more)');
		}

		allComments = $(".comment",$(this)).get();
		allComments.sort(function(a,b) {
			a = $(".commentfavevotes",a).html();
			b = $(".commentfavevotes",b).html();

			if (a == '') a = 0;
			if (b == '') b = 0;

			if (a > b) {
				return -1;
			} else if (a < b) {
				return 1;
			} else {
				return 0;
			}
		});
		$(allComments.slice(5)).hide();



	});

});

function comment(id) {
	$("#comment_"+id).html("<textarea class=\"commenttextarea\" id='commenttext_"+id+"'></textarea><input class=\"smallbutton\" type=\"submit\" value=\"Add Comment\" onclick=\"addcomment('"+id+"')\"/>");
}

function addcomment(id) {

	var comment = $("#commenttext_"+id).val();

	if (comment.length < 10) {
		$.fancyalert('Your comment must be atleast 10 characters in length');
		return;
	}

	if (comment.length > 600) {
		$.fancyalert('Your comment is too long, please reduce it to 600 characters');
		return;
	}

	$("#commenttext_"+id).val('');

	$.post(basePath+"/comments/post", {
		id: id,
		comment: comment
	},
	function(data) {
		if (data == 0) {
			$("#commenttext_"+id).val(comment);
			$.fancyalert('Please login to post a comment');
		} else {
			$("#comments_"+id).append(data);
		}
	});
}


function viewallcomments(id){
	$('#comments_'+id+' .comment').fadeIn(1000);
	$('#comment_'+id+' .viewallcomments').css('display','none');
}