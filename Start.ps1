﻿param (
	[switch]$Loop = $true
)

if(Test-Path "bin\php\php.exe"){
	$env:PHPRC = ""
	$binary = "bin\php\php.exe"
}else{
	$binary = "php"
}

if(Test-Path "DarkSystem.phar"){
	$file = "DarkSystem.phar"
}elseif(Test-Path "DarkSystem*.phar"){
	$file = "DarkSystem*.phar"
}elseif(Test-Path "PocketMine-MP.phar"){
	$file = "PocketMine-MP.phar"
}elseif(Test-Path ".\src\pocketmine\PocketMine.php"){
	$file = ".\src\pocketmine\PocketMine.php"
}else{
	echo "Couldn't find a valid DarkSystem installation"
	pause
	exit 1
}

function StartServer{
	$command = "powershell " + $binary + " " + $file + " --enable-ansi"
	iex $command
}

$loops = 0

StartServer

while($Loop){
	if($loops -ne 0){
		echo ("Restarted " + $loops + " times")
	}
	
	$loops++
	echo "To escape the loop, press CTRL+C now."
	StartServer
}
