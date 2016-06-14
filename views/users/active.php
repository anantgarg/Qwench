<?php 
if (isset($active) == TRUE) {
	echo "Account activation was successful!";
} elseif (isset($active) == FALSE) {
	echo 'Check now if you successfully receive the activation link
in your mail box to proceed with the registration';
}
?>