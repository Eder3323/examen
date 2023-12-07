<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use App\Models\CatUserTypeModel;
use \Firebase\JWT\JWT;


class AuthController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        // Obtener credenciales de la solicitud
        $phone      = $this->request->getVar('phone');
        $password   = $this->request->getVar('password');

        // Validar credenciales y obtener usuario
        $user       = $this->validateCredentialsAndGetUser($phone, $password);
        if (is_null($user)) {
            return $this->respond(['error' => 'Invalid phone or password.'], 401);
        }

        // Actualizar last_login
        $this->updateLastLogin($user['id']);

        // Obtener permisos del usuario
        $permissions    = $this->getUserPermissions($user['id_user_type']);

        // Generar y responder con el token JWT
        $token          = $this->generateTokenJWT($user['id'], $user['id_user_type'], $permissions);
        $response = [
            'message'   => 'Login Successful',
            'token'     => $token
        ];
        return $this->respond($response, 200);
    }

    /**
     * Validar credenciales del usuario y obtener el usuario si las credenciales son válidas.
     *
     * @param string $phone Número de teléfono del usuario.
     * @param string $password Contraseña del usuario.
     * @return array|null Arreglo que representa al usuario o null si las credenciales no son válidas.
     */
    private function validateCredentialsAndGetUser($phone, $password)
    {
        $userModel = new UserModel();
        $user = $userModel->where('phone', $phone)->first();

        if (!is_null($user) && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    /**
     * Actualizar el campo last_login del usuario.
     *
     * @param int $userId ID del usuario.
     */
    private function updateLastLogin($userId)
    {
        $userModel = new UserModel();
        $userModel->set('last_login', date('Y-m-d H:i:s'))->where('id', $userId)->update();
    }

    /**
     * Obtener los permisos del usuario basado en su tipo de usuario.
     *
     * @param string $userType Tipo de usuario.
     * @return array Arreglo de permisos del usuario.
     */
    private function getUserPermissions($userType)
    {
        $catUserTypeModel = new CatUserTypeModel();
        $permissionsData = $catUserTypeModel->select('permissions')->where('user_type', $userType)->first();
        return array_map('trim', explode(',', $permissionsData['permissions']));
    }

    /**
     * Generar un token JWT con la información proporcionada.
     *
     * @param int $userId ID del usuario.
     * @param string $userType Tipo de usuario.
     * @param array $permissions Arreglo de permisos del usuario.
     * @return string Token JWT generado.
     */
    public function generateTokenJWT($userId, $userType, $permissions): string
    {
        $key = getenv('JWT_SECRET');
        $iat = time();
        $exp = $iat + 3600;

        $payload = [
            "iss" => "Issuer of the JWT",
            "aud" => "Audience that the JWT",
            "sub" => "Subject of the JWT",
            "iat" => $iat,
            "exp" => $exp,
            "id" => $userId,
            "id_user_type" => $userType,
            "permissions" => $permissions,
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

}
