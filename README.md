# yggautocfg
Yggdrasil network auto config for public peers, under Windows  

How it works:  
1. Attempts to download peers from Neil's json file.
   If can't, attempt it from github zip.
   If can't, attempt txt file maintained by DomesticMoth.
2. Convert peers addr into plain txt from found format and parse it, then pings. Produces list of peers in peer_c.lst and its response times in peer_r.lst. Unavialable ones considered 10000 ping. Only tls:// ipv4 used.
3. Packs them into array and sort it. First N records is written as head+mid+tail file formula. Template pre-configuration required. Modify files cfg_head.txt and cfg_tail.txt to match your yggdrasil.conf

Note: most antivirals does falsepositive aggro at chainspawned downloading processes such as curl. Make sure the thing placed in exclusions.
