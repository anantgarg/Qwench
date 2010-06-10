<h1><?php if($nopagination != 1) { echo $questionscount; }?> Questions <i><?php echo $extratitle;?></i></h1>

<div style="clear:both"></div>
<?php
if ($questionscount != 0) {
	if($nopagination != 1) {
?>
<div class="questionsgrid_toppagination">
	<div style="clear:both"></div>
	<?php if($questionscount > QUESTIONS_PER_PAGE  && $nopagination != 1) {?>
		<div class="pagination" style="margin-left:5px;"><?php echo $pagination;?></div>
	<?php } ?>
	<div class="pagination" style="margin-left:5px;float:right;margin-right:5px;"><?php echo $orderOptions;?></div>
	<div style="clear:both"></div>
</div>
<?php
	}

	if ($_SESSION['userid']=='') {
		$mod = 0;
	} else {
		$mod = $_SESSION['moderator'];
	}
?>
<div style="clear:both;height:30px;"></div>
<?php
	foreach ($questions as $question) {
		if ($mod==1) {
?>
	<div id="delquestion" onmouseover="mouseover()" class="questionsview_del">
		<a href="<?php echo basePath(); ?>/questions/del/<?php echo $question['id']; ?>">x</a>
	</div>
<?php
		}
?>
		<div class="questionsview_userbox">
			<?php echo getUser($question['userid']);?>
		</div>

		<div class="questionsview_details">
			<span style="color:#999" title="<?php echo datify($question['created']);?>"><?php echo timeAgo(strtotime($question['created']));?></span>
		</div>

		<?php if ($question['kb']) {?>
		<div class="questionsview_details">
			<?php echo $question['answers'];?> cmts | <?php echo $question['votes'];?> votes
</div>
		<?php } else {?>
<div class="questionsview_details q">
			<?php echo $question['answers'];?> ans | <?php echo $question['votes'];?> votes
</div>
		<?php } ?>



<div class="questionsview_answer" id="a<?php echo $question['id'];?>">
	<div class="questionsgrid_title">
		<a href="<?php echo basePath();?>/questions/view/<?php echo $question['id'];?>/<?php echo $question['slug'];?>"><?php echo $question['title'];?></a>
		<br/><span class="questionsgrid_description"><a href="<?php echo basePath();?>/questions/view/<?php echo $question['id'];?>/<?php echo $question['slug'];?>"><?php echo $question['description'];?></a></span>
		<div style="clear:both"></div>

	</div>

	<ul class="holder noborder">
		<?php foreach ($question['tags'] as $tag) {?>
		<li class="bit-box nopadding"><a href="<?php echo basePath();?>/questions?tag=<?php echo $tag;?>"><?php echo $tag;?></a></li>
		<?php }?>
	</ul>

	<div style="clear:both;"></div>
</div>
	<?php }?>

	<?php if($nopagination != 1) {?>
<div class="questionsgrid_bottompagination">
	<div style="clear:both"></div>
		<?php if($questionscount > QUESTIONS_PER_PAGE) {?>
	<div class="pagination" style="margin-left:5px;"><?php echo $pagination;?></div>
		<?php } ?>
	<div class="pagination" style="margin-left:5px;float:right;margin-right:5px;"><?php echo $orderOptions;?></div>

	<div style="clear:both"></div>
</div> 
	<?php } ?>

<?php } else { ?>
	<h3>Sorry, we could not find what you were looking for. You may want to have a look at <a href="<?php echo basePath();?>/tags">tags</a>.</h3>
<?php } ?>