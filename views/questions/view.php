<script type="text/javascript">
	function cform() {
		var description = '';

		description = $("#wmd-input").val();

		if (description.length < 15) {
			$("#wmd-input").addClass('textalert');
			$.fancyalert('Your <?php if ($kb):?>comment<?php else:?>answer<?php endif;?> must be atleast 15 characters in length');
			$("#wmd-input").focus();
			return false;
		} else {
			$("#wmd-input").removeClass('textalert');
		}

		return true;
	}
</script>

<h1><?php echo $title;?></h1>

<div style="clear:both"></div>

<div class="questionsview_userbox">
	<?php echo getUser($userid);?>
</div>

<div class="questionsview_details"><span style="color:#999"><?php echo timeAgo(strtotime($created));?></span></div>
<?php 
if ($_SESSION['userid']=='') {
	$mod = 0;
} else {
	$mod = $_SESSION['moderator'];
}?>

<?php if ($mod==1) {?>
	<div onmouseover="mouseover()" class="questionsview_del"><a href="<?php echo basePath();?>/questions/del/<?php echo $id; ?>">x</a></div>
<?php }?>

<?php if ($userid == $_SESSION['userid'] || $mod==1) {?>
	<div class="questionsview_options"><a href="<?php echo basePath();?>/questions/edit/<?php echo $id;?>">edit</a></div>
<?php } ?>


<div class="questionsview_question" id="q<?php echo $id;?>">
	<div class="questionsview_rating">
		<div class="questionsview_box">
			<div class="questionsview_up<?php if($pvote) { echo " voteselected"; }?>">˄</div>
			<div class="questionsview_vote"><?php echo $votes;?></div><div class="questionsview_down<?php if($nvote) { echo " voteselected"; }?>">˅</div>
			<div class="questionsview_fave<?php if($fave) { echo " voteselected"; }?>">★</div>
		</div>
	</div>
	<div class="questionsview_questiondescription">
		<div class="markdown"><?php echo html_entity_decode($description);?></div>
		<?php if (!empty($link)) {?>
			<p><br/><?php echo $link;?>
			<?php if ($cache) { ?>
				<em><a href="<?php echo basePath();?>/questions/cache/<?php echo $id;?>" target="_blank">(view cache)</a></em>
			<?php } ?>
			</p>
		<?php }?>
		<ul class="holder noborder">
			<?php foreach ($tags as $tag):?>
			<li class="bit-box nopadding"><a href="<?php echo basePath();?>/questions?tag=<?php echo $tag;?>"><?php echo $tag;?></a></li>
			<?php endforeach;?>
		</ul>
		<div class="comments">
			<div id="comments_q<?php echo $id;?>">
			<?php foreach($comments as $comment):?>
				<div class="comment">
					<div class="comment_text"><?php echo $comment['comment'];?> - <a href="<?php echo basePath();?>/users/<?php echo $comment['userid'];?>/<?php echo $comment['username'];?>"><?php echo $comment['username'];?></a></div>

					<div class="commentdel" id="commentdel_<?php echo $comment['id'];?>"><?php if ($comment['userid'] == $_SESSION['userid'] || $mod==1) {
								echo "x";
							}?></div>

					<div class="commentfave <?php if ($comment['voted'] > 0) {
							echo "voteselected";
							 }?>" id="commentfave_<?php echo $comment['id'];?>">♥</div><div class="commentfavevotes"><?php if ($comment['votes'] != 0) {
									 echo $comment['votes'];
							}?></div>
					<div style="clear:both;"></div>
				</div>
			<?php endforeach;?>
			</div>
			<div id="comment_q<?php echo $id;?>" class="commentsadd">
				<span style="float:left"><a href="javascript:void(0)" onclick="javascript:comment('q<?php echo $id;?>')">Add comment</a></span>
				<span style="float:right" style="display:none" class="viewallcomments"><a href="javascript:void(0)" onclick="javascript:viewallcomments('q<?php echo $id;?>')"></a></span>
			</div>
		</div>
		<div style="height:30px;"></div>
	</div>
	<div style="clear:both"></div>
</div>


<?php if ($answerscount != 0):?>

<div class="toppagination">
	<div style="clear:both"></div>
	<?php if($answerscount > ANSWERS_PER_PAGE):?>
	<div class="pagination" style="margin-left:5px;"><?php echo $pagination;?></div>
	<?php endif;?>
	<div class="pagination" style="margin-left:5px;float:right;margin-right:5px;"><?php echo $orderOptions;?></div>
	<div style="clear:both"></div>
</div>
<div>
	<div style="float:left"><h2><?php echo $answerscount;?><?php if ($kb):?> Comments<?php else:?> Answers<?php endif;?></h2></div>
	<div style="clear:both"></div>
</div>

	<?php foreach ($answers as $answer):?>
		<?php
		if ($mod==1) {
			echo "<div  class=\"answerdel\" id=\"answerdel_".$answer['id']."\">x</div>";
		}
		?>

<div class="questionsview_userbox">
	<?php echo getUser($answer['userid']);?>
</div>

<div class="questionsview_details">
	<span style="color:#999"><?php echo timeAgo(strtotime($answer['created']));?></span>
</div>

	<?php if ($answer['userid'] == $_SESSION['userid'] || $mod==1):?>
		<div class="questionsview_options"><a href="<?php echo basePath();?>/answers/edit/<?php echo $answer['id'];?>">edit</a></div>
	<?php endif;?>

	<?php if(!$kb):?>
		<?php if($answer['accepted']):?>
<div class="questionsview_accepted">Accepted Answer</div>
		<?php elseif($userid == $_SESSION['userid'] || $mod==1):?>
<div class="questionsview_accept"><a href="<?php echo basePath();?>/answers/accept?id=<?php echo $answer['id'];?>">Accept this answer</a></div>
		<?php endif;?>
	<?php endif;?>

<div class="questionsview_answer" id="a<?php echo $answer['id'];?>">
	<div class="questionsview_rating">
		<div class="questionsview_box">
			<div class="questionsview_up<?php if($answer['pvote']) { echo " voteselected"; }?>">˄</div>
			<div class="questionsview_vote"><?php echo $answer['votes'];?></div>
			<div class="questionsview_down<?php if($answer['nvote']) { echo " voteselected"; }?>">˅</div>
		</div>
	</div>
	<div class="questionsview_answerdescription">
		<div class="markdown"><?php echo $answer['description'];?></div>
		<div class="comments">
			<div id="comments_a<?php echo $answer['id'];?>">
				<?php foreach($answer['comments'] as $comment):?>
				<div class="comment">
					<div class="comment_text"><?php echo $comment['comment'];?> - <a href="<?php echo basePath();?>/users/view/<?php echo $comment['userid'];?>/<?php echo $comment['username'];?>"><?php echo $comment['username'];?></a></div>
					<div class="commentdel" id="commentdel_<?php echo $comment['id'];?>"><?php if ($comment['userid'] == $_SESSION['userid'] || $mod==1) { echo "x"; }?></div>
					<div class="commentfave <?php if ($comment['voted'] > 0) { echo "voteselected"; }?>" id="commentfave_<?php echo $comment['id'];?>">♥</div>
					<div class="commentfavevotes"><?php if ($comment['votes'] != 0) { echo $comment['votes']; }?></div>
					<div style="clear:both;"></div>
				</div>
				<?php endforeach;?>
			</div>
			<div id="comment_a<?php echo $answer['id'];?>" class="commentsadd">
				<span style="float:left"><a href="javascript:void(0)" onclick="javascript:comment('a<?php echo $answer['id'];?>')">Add comment</a></span>
				<span style="float:right" style="display:none" class="viewallcomments"><a href="javascript:void(0)" onclick="javascript:viewallcomments('a<?php echo $answer['id'];?>')"></a></span>
			</div>
		</div>
		<div style="height:20px;"></div>
	</div>
	<div style="clear:both;"></div>
</div>
	<?php endforeach;?>

<div class="bottompagination">
	<div style="clear:both"></div>
		<?php if($answerscount > ANSWERS_PER_PAGE):?>
	<div class="pagination" style="margin-left:5px;"><?php echo $pagination;?></div>
		<?php endif;?>
	<div class="pagination" style="margin-left:5px;float:right;margin-right:5px;"><?php echo $orderOptions;?></div>
	<div style="clear:both"></div>
</div>

<?php else:?>
<h3><?php if ($kb):?>No comments on this article as yet.<?php else:?>No answers as yet. Be the first to write an answer.<?php endif;?></h3>
<?php endif;?>

<?php if ($_SESSION['userid'] != ''):?>
<div class="questionsview_form">
	<form action="<?php echo generateLink("answers","post");?>" method="post"  onsubmit="javascript:return cform();">

		<h2 style="padding-top:0px;padding-bottom:16px;"><?php if ($kb):?>Add a comment<?php else:?>Answer Question<?php endif;?></h2>

		<div id="wmd-editor" class="wmd-panel">
			<div id="wmd-button-bar"></div>
			<textarea id="wmd-input" name="description" ></textarea>
		</div>
		<div id="wmd-preview" class="markdown"></div>

		<br/><br/>
		<input type="hidden" name="questionid" value="<?php echo $id;?>">
		<input type="submit" value="Answer Question" class="button">
	</form>
</div>
<?php endif;?>
