<h1><?php echo $count;?> Users</h1>

<div style="clear:both"></div>

<div class="tags_list">
<ul class="holder noborder">
<?php foreach ($users as $user):?>
<li class="bit-box nopadding"><a href="<?php echo basePath();?>/users/view/<?php echo $user['id'];?>/<?php echo createSlug($user['name']);?>"><?php echo $user['name'];?></a> <?php echo $user['points'];?></li>
<?php endforeach;?>
</ul>
</div>