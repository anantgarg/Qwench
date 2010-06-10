<?php 
if ($_SESSION['userid']=='') {
	$mod = 0;
} else {
	$mod = $_SESSION['moderator'];
}
?>

<h1><?php echo $count;?> Tags</h1><?php //if($mod==1){echo "[Edit]";}?>

<div style="clear:both"></div>

<div class="tags_list">
	<ul class="holder noborder">
		<?php foreach ($tags as $tag):?>
		<li class="bit-box nopadding">
				<a href="<?php echo basePath();?>/questions?tag=<?php echo $tag['tag'];?>"><?php echo $tag['tag'];?></a> x <?php echo $tag['count'];?><?php if($mod==1) { echo "<a class=\"tag_del\" href=\"".basePath()."/tags/del?tag=".$tag['tag']."\">x</a>"; }?>
		</li>
		<?php endforeach;?>
	</ul>
</div>