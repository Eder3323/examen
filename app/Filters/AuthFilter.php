<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $token = $this->extractTokenFromHeader($request);

        if (empty($token) || !$this->validateToken($token)) {
            return $this->accessDeniedResponse('Access denied');
        }

        $decoded = $this->decodeToken($token);

        if (!$this->checkPermissions($request, $decoded)) {
            return $this->accessDeniedResponse('Access denied by permissions');
        }
    }

    /**
     * Extrae el token del encabezado de autorización de la solicitud.
     *
     * @param RequestInterface $request Objeto de solicitud HTTP.
     * @return string|null Token extraído o null si no se encuentra.
     */
    private function extractTokenFromHeader(RequestInterface $request)
    {
        $header = $request->getHeader("Authorization");
        $token = null;

        if (!empty($header) && preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            $token = $matches[1];
        }

        return $token;
    }

    /**
     * Valida la autenticidad del token utilizando la clave secreta.
     *
     * @param string $token Token a validar.
     * @return bool True si el token es válido, False en caso contrario.
     */
    private function validateToken($token)
    {
        try {
            $key = getenv('JWT_SECRET');
            JWT::decode($token, new Key($key, 'HS256'));
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Decodifica el token utilizando la clave secreta.
     *
     * @param string $token Token a decodificar.
     * @return object Objeto decodificado representando la información del token.
     */
    private function decodeToken($token)
    {
        $key = getenv('JWT_SECRET');
        return JWT::decode($token, new Key($key, 'HS256'));
    }

    /**
     * Verifica los permisos del usuario basándose en la solicitud y el token decodificado.
     *
     * @param RequestInterface $request Objeto de solicitud HTTP.
     * @param object $decoded Objeto decodificado representando la información del token.
     * @return bool True si los permisos son válidos, False en caso contrario.
     */
    private function checkPermissions(RequestInterface $request, $decoded)
    {
        // Obtiene la ruta de la solicitud y los segmentos.
        $permission = $request->getServer('REQUEST_URI');
        $segments = explode('/', trim($permission, '/'));

        // Verifica si la acción está permitida o si el usuario es de tipo 'user' y la actualizacion es sobre su propio recurso.
        return !(!in_array(strtolower($segments[1]), $decoded->permissions) ||
            (isset($segments[2]) && $decoded->id_user_type === 'user' && $decoded->id !== $segments[2])
        );
    }

    /**
     * Devuelve una respuesta de denegación de acceso común.
     *
     * @param string $message Mensaje que contiene el motivo de la denegación de acceso.
     * @return object Objeto de respuesta HTTP.
     */
    private function accessDeniedResponse($message)
    {
        $response = service('response');
        $response->setBody($message);
        $response->setStatusCode(401);
        return $response;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}