<form action="<?php echo generateLink("users","validate");?>" method="post">

<h1>Login</h1>

<h3>E-mail</h3>
<input type="textbox" class="textbox" name="email" id="email" style="width:300px;"/><br/>

<h3>Password</h3>
<input type="password" class="textbox" name="password" id="password" style="width:300px;"/></select>

<br/><br/>
<input type="submit" value="Login" class="button">
<input type="hidden" name="returnurl" value="<?php if (!empty($link)) {echo $link;}?>">
</form>