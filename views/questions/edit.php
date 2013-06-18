<script>

function cform() {
	var title = '';
	var description = '';
	title = $("#title").val();
	description = $("#wmd-input").val();
 

	if (title.length < 15) {
		$("#title").addClass('textalert');
		$.fancyalert('Your title must be atleast 15 characters in length');
		$("#title").focus();
		return false;
	} else {
		$("#title").removeClass('textalert');
	}

	if (description.length < 15) {
		$("#wmd-input").addClass('textalert');
		$.fancyalert('Your description must be atleast 15 characters in length');
		$("#wmd-input").focus();
		return false;
	} else {
		$("#wmd-input").removeClass('textalert');
	}

	return true;
}
</script>

<form action="<?php echo generateLink("questions","update");?>" method="post" onsubmit="javascript:return cform();">

<h1>Edit Your Question</h1>
<input type="textbox" class="textbox" name="title" id="title" value="<?php echo $title;?>"/><br/>

<div id="wmd-editor" class="wmd-panel" style="padding-top:20px">
<div id="wmd-button-bar"></div>
<textarea id="wmd-input" name="description" ><?php echo $description;?></textarea>
</div>
<div id="wmd-preview" class="markdown"></div>
 

<h3 style="padding-top:20px">Share a Link</h3>
<input type="textbox" class="textbox" name="link" id="link" value="<?php echo $link;?>"/><br/>

<h3>Tags</h3>
<select class="textbox" name="tags" id="tags"/>
<?php foreach ($tags as $tag):?>
<option value="<?php echo $tag;?>" class="selected"><?php echo $tag;?></option>
<?php endforeach;?>
</select>
<br/>
<input type="checkbox" id="answercheck" name="answercheck" value="1" tabindex="4" <?php if ($kb) { echo "checked"; }?>> Make this a knowledge-base article
<br/><br/>
<input type="hidden" name="id" value="<?php echo $questionid;?>">
<input type="submit" value="Update" class="button">
</form>