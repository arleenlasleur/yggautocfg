<?php
$s = file_get_contents($argv[1]);
$n = strpos($s,"time=");

if($n === false) echo "10000"; else{
    $n += 5;
    $m = strpos($s,"ms",$n);
    echo substr($s, $n, $m-$n);
}
echo PHP_EOL;
?>