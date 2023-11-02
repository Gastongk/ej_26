<?php
session_start();
session_destroy();
echo "Sesión cerrada con éxito. Serás redirigido...";
header("Location: ../frontend/index.html"); 
exit;
?>