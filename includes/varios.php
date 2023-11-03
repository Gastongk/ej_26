<?php
require_once 'config.php';

class Usuario {
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
        $sql = "INSERT INTO usuarios (username, password, email, token, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
        $parametros = [$username, $password, $email, $token, $created_at, $updated_at];
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
        $sql = "SELECT id, username, password FROM usuarios WHERE username = ?";
        $parametros = [$username];

        try {
            $stmt = ConexionDB::getInstancia()->ejecutarConsulta($sql, $parametros);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                if (password_verify($password, $usuario['password'])) {
                    // Generar un token (puedes personalizar la generación de tokens)
                    $token = self::generateToken();

                    // Actualizar el token en la base de datos (opcional)
                    self::updateToken($usuario['id'], $token);

                    $response = [
                        'success' => true,
                        'message' => 'Inicio de sesión exitoso',
                        'token' => $token,
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

    private function generateToken() {
        // Genera un token simple (puedes personalizar esto según tus necesidades)
        return bin2hex(random_bytes(16)); // Genera una cadena hexadecimal aleatoria
    }

    public function updateToken($newToken) {
        $sql = "UPDATE usuarios SET token = ? WHERE id = ?";
        $parametros = [$newToken, $this->id];

        try {
            ConexionDB::getInstancia()->ejecutarConsulta($sql, $parametros);
        } catch (Exception $e) {
            // Manejar errores de actualización del token, si es necesario
        }
    }

    public static function autenticarUsuario($username, $password) {
        $sql = "SELECT id, username, password, email FROM usuarios WHERE username = ?";
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
                    //  para PHP $usuarioAutenticado = new Usuario($usuarioData->id, $usuarioData->username, $usuarioData->email);

               //     return $usuarioAutenticado;

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
<?php
require_once '../includes/usuario.php';
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
        $resultadoRegistro = Usuario::registrarUsuario($username, $password, $email, $token, $created_at, $updated_at);
    
        echo $resultadoRegistro;
    } elseif (isset($_POST['login'])) {
        // Inicio de sesión
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Llama al método de la clase Usuario para iniciar sesión y obtener un token
        $usuarioAutenticado = Usuario::login($username, $password);

        echo $usuarioAutenticado;
    }
}
?>

