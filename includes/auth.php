<?php
require_once 'config.php';

class Auth {
    private $id;
    private $username;
    private $email;

    public function __construct($id, $username, $email) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public static function registrarUsuario($username, $password, $email) {
        $sql = "INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)";
        $parametros = [$username, $password, $email];
        $response = [];

        try {
            ConexionDB::getInstancia()->ejecutarConsulta($sql, $parametros);
            $response = ['success' => true, 'message' => 'Registro exitoso'];
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Error en el registro'];
        }
    
        echo json_encode($response);
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

    public static function obtenerPaginasPorUsuario($usuarioId) {
        $sql = "SELECT id, titulo, contenido, categoria_id, usuario_id FROM paginas WHERE usuario_id = ?";
        $parametros = [$usuarioId];
    
        try {
              $resultados = ConexionDB::getInstancia()->obtenerResultados($sql, $parametros);
            
            $paginas = [];
            foreach($resultados as $r){
                $pagina = new stdClass;
                $pagina->id = $r['id'];
                $pagina->titulo = $r['titulo'];
                $pagina->contenido = $r['contenido'];
                $pagina->categoriaId = $r['categoria_id'];
                $pagina->usuarioId = $r['usuario_id'];
                $paginas[] = $pagina;
            }
            return $paginas;
         
        } catch (Exception $e) {
            throw new Exception("Error  al cargar las  páginas por usuario: " . $e->getMessage());
    }
    
    }
}
?>