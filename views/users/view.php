
<?php 
if ($_SESSION['userid']=='')
$mod = 0;
else
$mod = $_SESSION['moderator'];?>

<h1><?php echo $user['name'];?></h1><?php if ($mod==1):?>
<div onmouseover="mouseover()" class="questionsview_del"><a href="<?php echo BASE_PATH;?>/users/del/<?php echo $user['id']; ?>">
x</a></div>
<?php endif;?>
        <table style="width:960px" class="vcard">
            <tr>
                <!--cell-->
                <td style="vertical-align:top; width:170px">
                    <table>
                        <tr>
                            <td style="padding:20px 20px 8px 20px">
                                <div style="float:left"><img src="http://www.gravatar.com/avatar/<?php echo md5(trim(strtolower($user['email'])));?>?d=identicon&s=128" style="border:1px solid #ccc">
                            </td>

                        </tr>
                        <tr>
                            <td class="summaryinfo">
                                <div class="summarycount"><?php echo $user['points'];?></div>
                                <div style="margin-top:5px; font-weight:bold">reputation</div>
                              
                            </td>
                        </tr>
                        <tr style="height:30px">

                            <td class="summaryinfo" style="vertical-align:bottom">17 views <?php //echo $user['view'];?></td>
                        </tr>
                        
                    </table>
                </td>
                <!--cell-->
                <td style="vertical-align: top; width:500px">
                    <div style="float: right; margin-top: 19px; margin-right: 4px">
                        
                    </div>

                    <h2 style="margin-top:20px">Registered User</h2>
                    <table class="user-details">
                        <tr>
                            <td style="width:150px">name</td>
                            <td style="width:230px"><b><?php echo $user['name'];?></b></td>
                        </tr>
                        <tr>

                            <td>member for</td>
                            <td><span class="cool" title="2008-11-12 19:23:01Z"><?php echo timeAgo(strtotime($user['created']));?></span></td>
                        </tr>
                        <tr>
                            <td>seen</td>
                            <td><span class="cool"><span title="2010-01-15 12:42:32Z" class="relativetime">  
                            <?php echo timeAgo(strtotime($user['lastactivity']));?>
                            </span></span></td>
                        </tr>

                        
                        <tr>
                            <td>website</td>
                            <td>
                                <div class="no-overflow"><a href="<?php echo $user['website'];?>" rel="nofollow me" class="url"><?php echo $user['website'];?></a></div>                                
                            </td>
                        </tr>
                        
                        <tr>
                            <td>location</td>
                            <td style="width:230px" ><?php echo $user['location'];?></td>
                            </td>
                        </tr>
                        <tr>
                            <td>age</td>
                            <td style="width:230px" ><?php echo age($user['birthday']);?></td>
                            </td>
                        </tr>

                        <tr>   <td style="width:100px"> 
                     <tr> 
                            <td style="width:120px">about me</td> 
                            <td style="width:400px"><?php echo str_replace("\n","<br />", $user['aboutme']);?></td> 
                        </tr> 
                    
                </td> 
</tr> 
              
                    </table>
                </td>
                <!--cell-->
                <td style="width:390px"></td>
            </tr>
        </table>

