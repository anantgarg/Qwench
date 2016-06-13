<?php

if ($_SESSION['userid']=='') {
	$mod = 0;
} else {
	$mod = $_SESSION['moderator'];
}

if ($mod==1) {

?>
	<h1>Administration Panel</h1>
	<?php foreach( $moderators as $moderator ):?>
	<a href="<?php echo generateLink("users","view")."/".$moderator['id'];?>"><b><?php echo $moderator['name'];?></b></a>
	<a href="<?php echo generateLink("admin","revoke")."/".$moderator['id'];?>">[revoke]</a> <br>
	<br />
	<?php endforeach; ?>
	
	<h2>• Highest voted questions</h2>
	<?php foreach( $bestquestions as $bestquestion ):?>
	Vote: <?php echo $bestquestion['votes'];?>
	Title: <?php echo $bestquestion['title'];?>
	Author: <div class="questionsview_userbox"><?php echo getUser($bestquestion['userid']);?></div>
	<br />
	<?php endforeach;?>

	<h2>• Lowest voted questions</h2>
	<?php foreach( $worstquestions as $worstquestion ):?>
	Vote: <?php echo $worstquestion['votes'];?>
	Title: <?php echo $worstquestion['title'];?> <a href="<?php echo generateLink("users","view")."/".$worstquestion['userid'];?>"> <b>Author</b></a>
	<br />
	<?php endforeach;?>

<?php
} else {
?>
	<meta http-equiv="refresh" content="0; url=<?php echo BASE_PATH; ?>">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
<?php
}
?>