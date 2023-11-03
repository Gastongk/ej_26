<?php
require_once 'config.php';

class Auth {
    private $id;
    private $username;
    private $password;
    private $email;
    private $token;
    private $created_at;
    private $updated_at;

    public function __construct($id, $username, $email, $password, $token, $created_at, $updated_at) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getToken() {
        return $this->token;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public static function registrarUsuario($username, $password, $email, $token, $created_at, $updated_at) {
         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $sql = "INSERT INTO users (username, password, email, token, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
        $parametros = [$username, $hashedPassword, $email, $token, $created_at, $updated_at];
        $response = [];
    
        try {
            ConexionDB::getInstancia()->ejecutarConsulta($sql, $parametros);
            $response = ['success' => true, 'message' => 'Registro exitoso'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Error en el registro'];
        }
    
        echo json_encode($response);
    }
    

    public static function login($username, $password) {
        $sql = "SELECT id, username, email, password FROM users WHERE username = ?";
        $parametros = [$username];

        try {
            $stmt = ConexionDB::getInstancia()->ejecutarConsulta($sql, $parametros);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                if (password_verify($password, $usuario['password'])) {
                    $usuarioData = new stdClass;
                    $usuarioData->username = $usuario['username'];
                    $usuarioData->email = $usuario['email'];

                    $token = self::generateToken();

                    self::updateToken($usuario['id'], $token);

                    $response = [
                        'success' => true,
                        'message' => 'Inicio de sesión exitoso',
                        'token' => $token,
                        'data' => $usuarioData,
                           ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Error al iniciar sesión. Contraseña incorrecta',
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error al iniciar sesión. Usuario no encontrado',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error en la autenticación',
            ];
        }

        return json_encode($response);
    }

    private static function generateToken() {
        
        return bin2hex(random_bytes(16)); // cadena hexadecimal aleatoria
    }

    public static function updateToken($userId, $newToken){
        $sql = "UPDATE users SET token = ? WHERE id = ?";
        $parametros = [$newToken, $userId]; 
    
        try {
            ConexionDB::getInstancia()->ejecutarConsulta($sql, $parametros);
        } catch (Exception $e) {
        }
    }
 
    public static function autenticarUsuario($username, $password) {
        $sql = "SELECT id, username, password, email FROM users WHERE username = ?";
        $parametros = [$username];
    
        try {
            $stmt = ConexionDB::getInstancia()->ejecutarConsulta($sql, $parametros);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($usuario) {
                if ($password === $usuario['password']) {
                    $usuarioData = new stdClass;
                    $usuarioData->id = $usuario['id'];
                    $usuarioData->username = $usuario['username'];
                    $usuarioData->email = $usuario['email'];
    
                    $response = [
                        'success' => true,
                        'message' => 'Inicio de sesión exitoso',
                        'data' => $usuarioData,
                    ];
           
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Error al iniciar sesión. Contraseña incorrecta',
                    ];
                }
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error al iniciar sesión. Usuario no encontrado',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error en la autenticación',
            ];
        }
    
        echo json_encode($response);
    }
}
?>