
<h1>Administration Panel</h1>

<h2>• Moderators</h2>
<?php foreach( $moderators as $moderator ):?>

<?php echo $moderator['name'];?>
<br>
 <?php endforeach;?><br> 
 
<h2>• Highest voted questions</h2></p>
 <?php foreach( $bestquestions as $bestquestion ):?>
Vote: <?php echo $bestquestion['votes'];?> Title: <?php echo $bestquestion['title'];?> Author:<div class="questionsview_userbox"><?php echo getUser($bestquestion['userid']);?></div>   <br>
 <?php endforeach;?>
 
 <h2>• Lowest voted questions</h2></p>
 <?php foreach( $worstquestions as $worstquestion ):?>
Vote: <?php echo $worstquestion['votes'];?> Title: <?php echo $worstquestion['title'];?>  <a href="<?php echo generateLink("users","view")."/".$worstquestion['userid'];?>"><b>Author</b></a>  <br>
 <?php endforeach;?>
