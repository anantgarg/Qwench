
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

	
	if (password.length < 1 || password.length > 100) {
		$("#namepassword").addClass('textalert');
		$.fancyalert('Please enter your password');
		$("#password").focus();
		return false;
	} else {
		$("#password").removeClass('textalert');
	}


	return true;
}
</script>

<form action="<?php echo generateLink("users","create");?>" method="post" onsubmit="javascript:return cform();">

<h1>Register</h1>

<h3>Name</h3>
<input type="textbox" placeholder="Your diplayname" class="textbox" name="name" id="name"/><br/>

<h3>E-mail</h3>
<input type="textbox" placeholder="A valid email address (for Gravatar)" class="textbox" name="email" id="email"/><br/>

<h3>Password</h3>
<input type="password" class="textbox" name="password" id="password"/></select>

<br/><br/>
<input type="submit" value="Register" class="button">
</form>