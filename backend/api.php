<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';
header('content-Type: application/json');
session_start();

if (isset($_POST['registro'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $resultadoRegistro = Auth::registrarUsuario($username, $password, $email);
    
    echo ($resultadoRegistro);
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    $usuarioAutenticado = Auth::autenticarUsuario($username, $password);

    if ($usuarioAutenticado instanceof Auth) {
       
        $_SESSION['usuario'] = $usuarioAutenticado; 
        echo ($usuarioAutenticado);
       
 
    } else {
        echo ("$usuarioAutenticado");
    }
}

?>