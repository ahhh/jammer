<!-- Begin trap form to snare scanner -->
<html>
<head>
</head>

<body>

<form action="jammer.php" method="post">
Input: <input type="text" name="name"><br>
<input type="submit">
</form>
<br>
<br>


<?php
//Begining of Response Fuzzer Section

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

//Start switches for automated scanner responses, makes it easy to add new triggers
//So long as there is input
if(!empty($_POST['name']))
{

 switch ($_POST['name']) {
 
 //This is a ZAP Attack Proxy default test injection
 case "ZAP":
	header("HTTP/1.0 500 Internal Server Error");
	$hits = trackUser("Tripped Default ZAP Fuzzing");
        $response = randfuzz($fuzzdatalen * $hits);
	break;

//This is a SkipFish default test injection
 case "9876sfi":
	header("HTTP/1.0 409 Conflict");
	$hits = trackUser("Tripped Default SkipFish Fuzzing");
        $response = randfuzz($fuzzdatalen * $hits);
	break;

//This is a Burp Spider default test injection
 case "Peter Winter":
	//Triggers Spider adding response to Target page
	header('Location: index.php', true,302);
        $hits = trackUser("Tripped Default Burp Spider Fuzzing");
	$response = randfuzz($fuzzdatalen * $hits);
	break;
 
 //This is a default string to test for SQLInjection
 case "'":
 	header("HTTP/1.0 500 Internal Server Error");
 	$hits = trackUser("Tripped Common SQLInjection Fuzzing");
        $response = randfuzz($fuzzdatalen * $hits);
 	break;
 
 //Base response for unrecognized test
 default:
     $response = "testing";
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
   $fp = fopen('jammer_log.txt', 'a');
   fwrite($fp, $_SERVER[REMOTE_ADDR]." ". date('Y-m-d H:i:s')." ".$message."\n"); 
   fclose($fp);

   $hit = track($_SERVER[REMOTE_ADDR]);
   return $hit;
}

function track($IP)
{

$log = fopen("jammer_log.txt", "r");
$counting = 0;
while ($line = fgets($log)) {
	$line = explode(",", $line);
        if($line[0] = $IP)
         $counting++;
       }

return $counting;

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

