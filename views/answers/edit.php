<form action="<?php echo generateLink("answers","update");?>" method="post">

<h1>Edit Your Answer</h1>

<div id="wmd-editor" class="wmd-panel">
<div id="wmd-button-bar"></div>
<textarea id="wmd-input" name="description" ><?php echo $description;?></textarea>
</div>
<div id="wmd-preview" class="wmd-panel"></div>
 
<br/><br/>
<input type="hidden" name="id" value="<?php echo $answerid;?>">
<input type="submit" value="Update Answer" class="button">
</form>