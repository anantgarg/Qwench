
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
<input type="textbox" class="textbox" style="width:500px" name="name" id="name" value="<?php echo $user['name'];?>"/><br/>

<h3>E-mail</h3>
<input type="textbox" class="textbox" style="width:500px" name="email" id="email" value="<?php echo $user['email'];?>"/><br/>

<h3>Website</h3>
<input type="textbox" class="textbox" style="width:500px" name="website" id="website" value="<?php echo $user['website'];?>"/><br/>

<h3>Real Name</h3> 
<input type="textbox" class="textbox" style="width:500px" name="realname" value="<?php echo $user['realname'];?>" /><br/>

<h3>Location</h3> 
<input type="textbox" class="textbox" style="width:500px" name="location" value="<?php echo $user['location'];?>" /><br/>

<h3>Birthday <span style="font-size:10px">(YYYY/MM/DD never displayed, used to show age)</span></h3> 
<input type="textbox" class="textbox" style="width:500px" name="birthday" value="<?php echo $user['birthday'];?>" /><br/>


<h3>About Me</h3>
<textarea cols="55" class="textbox" style="width:500px" id="AboutMe" name="AboutMe" rows="12"></textarea>
                     
<h3>Password <span style="font-size:10px">(Leave blank if you do not want to update)</span></h3> 
<input type="password" class="textbox" style="width:500px" name="password" id="password" value=""/>

<br/><br/>
<input type="submit" value="Update Profile" class="button">
</form>
