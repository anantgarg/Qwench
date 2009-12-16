
<script>
function cform() {
	var name = '';
	var email = '';
	var password = '';
	name = $("#name").val();
	email = $("#email").val();
	password = $("#password").val();
 

	if (name.length < 1 || name.length > 100) {
		$("#name").addClass('textalert');
		$.fancyalert('Please enter your name');
		$("#name").focus();
		return false;
	} else {
		$("#name").removeClass('textalert');
	}
	
	if (email.length < 1 || email.length > 100) {
		$("#email").addClass('textalert');
		$.fancyalert('Please enter your e-mail');
		$("#email").focus();
		return false;
	} else {
		$("#email").removeClass('textalert');
	}

	
	return true;
}
</script>

<form action="<?php echo generateLink("users","update");?>" method="post" onsubmit="javascript:return cform();">

<h1>Edit Profile</h1>

<h3>Name</h3>
<input type="textbox" class="textbox" name="name" id="name" value="<?php echo $user['name'];?>"/><br/>

<h3>E-mail</h3>
<input type="textbox" class="textbox" name="email" id="email" value="<?php echo $user['email'];?>"/><br/>

<h3>Password <span style="font-size:10px">(Leave blank if you do not want to update)</span></h3> 
<input type="password" class="textbox" name="password" id="password" value=""/></select>

<br/><br/>
<input type="submit" value="Update Profile" class="button">
</form>