		</div>
		<div id="rightpanel">
		<div style="text-align:right;">
			<a href="<?php echo BASE_PATH;?>/" style="border-bottom:0px;"><img src="<?php echo BASE_DIR;?>/img/logo.gif"></a>
		</div>
		<div style="clear:both"></div>
		<?php
		if (!empty($_SESSION['userid']))
		{
		?>
		<div class="userlogin">
			<div style="float:left">
				<img src="http://www.gravatar.com/avatar/<?php echo md5(trim(strtolower($_SESSION['email'])));?>?d=identicon&s=70" style="border:1px solid #ccc" />
			</div>
			<div style="float:left;padding-left:10px;">
				<h3 style="padding-left:0px"><?php echo $_SESSION['name'];?> | <?php echo $_SESSION['points'];?></h3>
				<a href="<?php echo BASE_PATH;?>/users/edit">Edit Profile</a><br/>
				<?php if($_SESSION['moderator']==1) printf("<a href=\"". BASE_PATH . "/admin\">Admin Panel</a><br>");?>
				<a href="<?php echo BASE_PATH;?>/users/logout">Logout</a>
			</div>
			<div style="clear:both"></div>
		</div>
		<?php
		}
		elseif (empty($loginpage))
		{
		?>
		<div class="userlogin">
			<form action="<?php echo generateLink("users","validate");?>" method="post">
				<h3>E-mail</h3>
				<input type="textbox" class="textbox" name="email" style="width:215px;"/>
				<h3>Password</h3>
				<input type="password" class="textbox" name="password" style="width:215px;"/>
				<input type="hidden" name="returnurl" value="<?php echo getLink();?>">
				<div style="padding-top:10px">
					<input type="submit" value="Login" class="button"> or <i><a href="<?php echo BASE_PATH;?>/users/register">click here to register</a></i>
				</div>
			</form>
		</div>
		<?php
		}
		?>
		<div style="clear:both"></div>
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="footer">
		<!-- Copyright Notice Do Not Remove -->
		Powered by Qwench<br/>Copyright 2009-2010 Inscripts<br />Diablo512
		<!-- Copyright Notice Do Not Remove -->
	</div>
	</body>
</html>
