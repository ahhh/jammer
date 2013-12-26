<!-- Begin trap form to snare scanner -->
<html>
<head>
</head>

<body>

<form action="jammer.php" method="post">
Input: <input type="text" name="input1"><br>
<input type="submit">
</form>
<br>
<br>

<!-- Begin response scanner function -->
<?php

//Set fuzzdata length
if(!empty($_POST['fuzzdatalen']))
{
$fuzzdatalen = $_POST['fuzzdatalen'];
}
else
{
$fuzzdatalen = rand(12000000, 12938490);
}
$fuzzpostdatas = false;
if(!empty($_POST['fuzzpostdatas']))
{
$fuzzpostdatas = true;
}

//Start switches for automated scanner responses
//So long as there is input
if(!empty($_POST['input1']))
{

 //This is a Zed Attack Proxy default test injection
 if($_POST['input1'] == "ZAP")
 {
  header("HTTP/1.0 500 Internal Server Error");
  $response = randfuzz($fuzzdatalen);
  trackUser("Tripped Default ZAP Fuzzing");
 }
 else
 { 

   //This is a common SQLInjection test
   if($_POST['input1'] == "'")
    {
     header("HTTP/1.0 500 Internal Server Error");
     $response = randfuzz($fuzzdatalen);
     trackUser("Tripped Common SQLInjection Fuzzing");
    }
    else
    {
     //Base response for unrecognized test
     $response = "testing";
    }
 }
}


//below is the random functions
function randfuzz($len) 
{
    if (is_readable('/dev/urandom')) {
        $f=fopen('/dev/urandom', 'r');
        $urandom=fread($f, $len);
        fclose($f);
    }
    else
    {
    die("either /dev/urandom isnt readable or this isnt a linux 
machine!");
    }
    $return='';
    for ($i=0;$i < $len;++$i) {
        if (!!empty($urandom)) {
            if ($i%2==0) mt_srand(time()%2147 * 1000000 + 
(double)microtime() * 1000000);
            $rand=48+mt_rand()%64;
        } else  $rand=48+ord($urandom[$i])%64;

        if ( 57 < $rand ) $rand+=7; 
        if ( 90 < $rand ) $rand+=6;  

        if ($rand==123) $rand=45;
        if ($rand==124) $rand=46;
        $return.=chr($rand);
    }
    return $return; 
}

//Function below is for logging what triggered fuzzing
function trackUser($message)
{
   $fp = fopen('jammer_log.txt', 'w');
   fwrite($fp, $_SERVER[REMOTE_ADDR]." ". date('Y-m-d H:i:s')." ".$message); 
   fclose($fp);
}


//Echo back fuzz data
if(!empty($response))
{ 
//setcookie($response, $response);
echo "$response";  
}
?>

</body>
</html>

