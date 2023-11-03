<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registro'])) {
        // Registro de usuario
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
    
        // Genera un token (puedes personalizar la generación de tokens)
        $token = bin2hex(random_bytes(16)); // Genera una cadena hexadecimal aleatoria de 32 caracteres
    
        // Obtiene la fecha actual en el formato deseado
        $created_at = date('Y-m-d H:i:s'); // Formato "YYYY-MM-DD HH:MM:SS"
        $updated_at = $created_at; // Actualización inicial igual a la creación
    
        // Llama al método de la clase Usuario para registrar un usuario
        $resultadoRegistro = Auth::registrarUsuario($username, $password, $email, $token, $created_at, $updated_at);
    
        echo $resultadoRegistro;
    } elseif (isset($_POST['login'])) {
        // Inicio de sesión
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Llama al método de la clase Usuario para iniciar sesión y obtener un token
        $usuarioAutenticado = Auth::login($username, $password);

        echo $usuarioAutenticado;
    }
}
?>