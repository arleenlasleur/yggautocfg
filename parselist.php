<?php
$f_cmds = fopen("doping.bat","w");
$f_peer = fopen("peer_c.lst","w");
$f_data = fopen("peer.txt", "r");
$i = 1;
while($line = fgets($f_data)){
    $line = str_replace(PHP_EOL,"",$line);
    $line_cut = $line;
    $n = strrpos($line,":");
    if($n !== false) $line_cut = substr($line_cut, 6, $n-strlen($line_cut));
    $ping_cmd = "ping ".$line_cut." -n 1 | find \"TTL=\" /i > ping".lz($i).".log 2>&1";
    $i++;
    fwrite($f_cmds, $ping_cmd.PHP_EOL);
    fwrite($f_peer, $line.PHP_EOL);
//    echo $line.PHP_EOL;
}
fclose($f_data);
fclose($f_cmds);
fclose($f_peer);

$fail_a = filesize("peer_c.lst");
$fail_b = filesize("doping.bat");
$fail = false;
if(!$fail_a || $fail_a <= 2) $fail=true;
if(!$fail_b || $fail_b <= 2) $fail=true;
if($fail) file_put_contents("failed.tmp"," ",LOCK_EX);

function lz($i){
    $s = "";
    if($i<100) $s .= "0";
    if($i<10)  $s .= "0";
    $s .= $i;
    return $s;
}
?>
