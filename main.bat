@echo off
:: -------------------------------------------------------------------------------
:: Yggdrasil autoconfigger for Windows, v. 0.1-24/01/2024, by Arleen Lasleur
::
:: Configurable: (in sortping.php on lines 2-3)
::   $s_offset -> spaces to indent in cfg file
::   $n_peers  -> number of peers to write (select first N, sorted by min ping)
:: 
:: Split yggdrasil.conf into parts:
::     BEFORE peers -> cfg_head.txt
::     AFTER peers  -> cfg_tail.txt (this file is sensitive due to keys inside)
::
:: System requirements:
::   - 7z.dll, 7za.exe, 7zxa.dll (or other unzipper)
::   - php >= 5.3
::   - LOCAL SYSTEM privileges (for %programdata% write access and ygg svc mgmt)
::
:: Todo:
::   - cfg file replacement, service restart
::   - ygg disease indicator (log file analyzer?)
::   - ygg online indicator (ping to 222:a8e4:50cd:55c:788e:b0a5:4e2f:a92c ?)
::   - duty cycle (exec each hour? day? week?)
::   - install as svc (via nssm)
:: -------------------------------------------------------------------------------
set phppath=c:\tools\php\php.exe
set zippath=c:\tools\7za.exe
:: ---- clean possible shit ------------------------------------------------------
if exist failed.tmp del failed.tmp /q
if exist peer.json del peer.json /q
if exist peer.zip del peer.zip /q
if exist peer_zip.dir del peer_zip.dir /q
if exist peer.txt del peer.txt /q
if exist peer_c.lst del peer_c.lst /q
if exist peer_r.lst del peer_r.lst /q
if exist doping.bat del doping.bat /q
if exist ping*.log  del ping*.log /q
if exist public-peers-master rmdir public-peers-master /s /q
:: ---- attempt Neil json source -------------------------------------------------
curl -L -o peer.json https://publicpeers.neilalexander.dev/publicnodes.json
if not exist peer.json goto gitrepo
:: ---- convert ---------
%phppath% makelist.php /j > peer.txt
del peer.json /q
:: ---- test ------------
%phppath% parselist.php
if not exist failed.tmp goto work
del failed.tmp /q
:: ---- attempt Neil zip source --------------------------------------------------
:gitrepo
curl -L -o peer.zip  https://codeload.github.com/yggdrasil-network/public-peers/zip/refs/heads/master
if not exist peer.zip goto dmoth
:: ---- convert ---------
7za x peer.zip
dir public-peers-master\*.md /s /b > peer_zip.dir
%phppath% makelist.php /z > peer.txt
del peer_zip.dir /q
del peer.zip /q
rmdir public-peers-master /s /q
:: ---- test ------------
%phppath% parselist.php
if not exist failed.tmp goto work
del failed.tmp /q
:: ---- attempt Domesticmoth source ----------------------------------------------
:dmoth
curl -L -o peer.md https://raw.githubusercontent.com/DomesticMoth/MPL/main/yggdrasil.txt
if not exist peer.md goto :eof
:: ---- convert ---------
%phppath% makelist.php /m > peer.txt
:: ---- test ------------
%phppath% parselist.php
if not exist failed.tmp goto work
del failed.tmp /q
goto :eof
:: -------------------------------------------------------------------------------
:work
del peer.txt /q
del failed.tmp /q
rem debug start /wait doping.bat
call doping.bat
ping 127.0.0.1 -n 2 -w 1000 >nul 2>&1
del doping.bat /q
for /f "tokens=* delims=" %%G in ('dir /A-D /B ping*.log') do (
  %phppath% testping.php "%%~G" >> peer_r.lst
)
del ping*.log /q
:: -------------------------------------------------------------------------------
type cfg_head.txt > yggdrasil.conf
%phppath% sortping.php >> yggdrasil.conf
type cfg_tail.txt >> yggdrasil.conf
del peer_c.lst /q
del peer_r.lst /q
