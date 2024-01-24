<?php
$s_offset = "      ";      // spaces to indent in cfg
$n_peers  = 30;            // peers to write

$data_cand = array();
$data_ping = array();
$data = array();
$data_filt = array();
$f_peer = fopen("peer_c.lst", "r");  while($line = fgets($f_peer)) array_push($data_cand, $line);  fclose($f_peer);
$f_resp = fopen("peer_r.lst", "r");  while($line = fgets($f_resp)) array_push($data_ping, $line);  fclose($f_resp);
foreach ($data_cand as $key => $val)
        array_push($data, array( intval($data_ping[$key]), str_replace(PHP_EOL,"",$data_cand[$key]) ));

foreach ($data as $val){
    $x = intval($val[0]);
    if($x == 10000) continue;
    array_push($data_filt,$val);
}
unset($data);
$data = $data_filt;
unset($data_filt);
asort($data);

$i = 0;
foreach ($data as $val){
    if($i > $n_peers) break;
    echo $s_offset.$val[1].PHP_EOL;
    $i++;
}
?>

