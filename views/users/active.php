
<?php 

if (isset($active)==True)
echo "Account activation was successful!";
elseif (isset($active)==False){
echo 'Check now if you successfully receive the activation link
in your mail box to proceed with the registration';
}
?>