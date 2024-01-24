<?php
if($argv[1] == "/j"){
    $f_data = file_get_contents("peer.json");
    $data = json_decode($f_data);
    foreach ($data as $line){
        foreach ($line as $addr => $val){
            if(strpos($addr,"[") !== false) continue;        // ipv6 unreachable, skip
            if(strpos($addr,"tls://") === false) continue;
            echo $addr.PHP_EOL;
        }
    }
}

if($argv[1] == "/z"){
    $f_index = fopen("peer_zip.dir", "r");
    while($line = fgets($f_index)){
        $line = str_replace(PHP_EOL,"",$line);
        $f_md = fopen($line, "r");
        while($line_md = fgets($f_md)){
            if(strpos($line_md,"[") !== false) continue;
            $line_md = str_replace(chr(0x0d),"",$line_md);   // mbshit
            $line_md = str_replace(chr(0x0a),"",$line_md);   // strip unix EOLs
            $n = strpos($line_md,"tls://");
            if($n === false) continue;
            $line_md = substr($line_md, $n, -1);
            $n = strpos($line_md,"`");
            if($n !== false) $line_md = substr($line_md, 0, $n);
            if(strlen($line_md) < 10) continue;              // strip comment about tls
            echo $line_md.PHP_EOL;
        }
        fclose($f_md);
    }
    fclose($f_index);
}

if($argv[1] == "/m"){
    $f_data = fopen("peer.md", "r");
    while($line = fgets($f_data)){
        if(strpos($line,"[") !== false) continue;
        $line = str_replace(chr(0x0d),"",$line);
        $line = str_replace(chr(0x0a),"",$line);
        $n = strpos($line,"tls://");
        if($n === false) continue;
        echo $line.PHP_EOL;
    }
    fclose($f_data);
}
?>
