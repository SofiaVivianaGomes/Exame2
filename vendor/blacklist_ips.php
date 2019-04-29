<?php

const BLACKLIST_IPS = ["111.111.111", "222.222.222", "333.333.333"];

// Verificar se o IP está no array de IPs bloqueados
if (in_array ($_SERVER['REMOTE_ADDR'], BLACKLIST_IPS)) {
   header("Location: ../public/404.php"); //mandar para a página de erro
   exit();
} 

?>