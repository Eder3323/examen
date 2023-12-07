<?php
namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class UserController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        // Obtener todos los usuarios con campos seleccionados
        $users = $this->getAllUsers();

        // Responder con la lista de usuarios
        return $this->respond(['users' => $users], 200);
    }

    /**
     * Obtener todos los usuarios con campos seleccionados.
     *
     * @return array Lista de usuarios con campos seleccionados.
     */
    private function getAllUsers(): array
    {
        $usersModel = new UserModel();
        return $usersModel->select("name,last_name,phone,email,picture,id_user_type,created_at,updated_at,last_login")->findAll();
    }

    public function create()
    {
        // Definir reglas de validación
        $rules = [
            'name'          => 'required|min_length[2]|max_length[255]',
            'last_name'     => 'required|min_length[2]|max_length[255]',
            'phone'         => 'required|min_length[4]|max_length[10]|is_unique[users.phone]',
            'email'         => 'required|min_length[4]|max_length[255]|valid_email',
            'password'      => 'required|min_length[4]|max_length[50]',
            'picture'       => 'uploaded[picture]|max_size[picture,1024]|is_image[picture]',
            'id_user_type'  => 'required',
        ];

        // Validar la entrada
        if ($this->validate($rules)) {
            // Procesar y guardar la información del usuario
            $this->processAndSaveUserData();
            return $this->respond(['message' => 'Registered Successfully'], 200);
        } else {
            // Responder con errores de validación
            $response = [
                'errors'    => $this->validator->getErrors(),
                'message'   => 'Invalid Inputs'
            ];
            return $this->fail($response , 409);
        }
    }

    /**
     * Procesar y guardar la información del usuario.
     */
    private function processAndSaveUserData()
    {
        // Validar y mover la imagen, obtener el nombre del archivo
        $image_name = $this->validateImage($this->request->getFile('picture'));

        // Crear modelo y datos del usuario
        $model = new UserModel();
        $data = [
            'name'          => $this->request->getPost('name'),
            'last_name'     => $this->request->getPost('last_name'),
            'phone'         => $this->request->getPost('phone'),
            'email'         => $this->request->getPost('email'),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'picture'       => $image_name,
            'id_user_type'  => $this->request->getPost('id_user_type'),
        ];

        // Guardar datos del usuario en la base de datos
        $model->save($data);
    }

    /**
     * Validar y mover la imagen, devolver el nombre del archivo.
     *
     * @param \CodeIgniter\HTTP\UploadedFile $file Archivo de imagen.
     * @return string|null Nombre del archivo o null si no es válido.
     */
    public function validateImage($file): ?string
    {
        // Verificar si $file es un string (ruta del archivo) y retornarlo
        if (is_string($file)) {
            return $file;
        }

        // Verificar si $file es un objeto File y continuar con la validación
        if (!$file->isValid()) {
            return null;
        }

        $newFilename = round(microtime(true)) . '.' . $file->getClientExtension();

        // Mover la imagen a la carpeta "images" que se encuentra en /public
        if ($file->move("images", $newFilename)) {
            return "/images/" . $newFilename;
        }

        return null;
    }

    public function update($id = null)
    {
        // Obtener el modelo y los datos del usuario
        $model = new UserModel();
        $data = $this->getUserData($model, $id);

        // Verificar si se encontró el usuario
        if ($data) {
            // Obtener datos del JSON o de la entrada cruda
            $inputData = $this->getInputData();

            // Validar y mover la imagen, si está presente en la entrada
            $image_name = isset($inputData['picture']) ? $this->validateImage($inputData['picture']) : $data['picture'];

            // Actualizar los datos del usuario
            $data = [
                'name'          => $inputData['name'],
                'last_name'     => $inputData['last_name'],
                'phone'         => $inputData['phone'],
                'email'         => $inputData['email'],
                'picture'       => $image_name,
                'id_user_type'  => $inputData['id_user_type'],
            ];

            // Actualizar en la base de datos
            $model->update($id, $data);

            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => ['success' => 'User Updated Successfully']
            ];
        } else {
            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => ['success' => 'No User Found: ' . $id]
            ];
        }

        return $this->respond($response);
    }

    public function delete($id = null)
    {
        // Obtener el modelo y los datos del usuario
        $model = new UserModel();
        $data = $this->getUserData($model, $id);

        // Verificar si se encontró el usuario
        if ($data) {
            // Eliminar el usuario de la base de datos
            $model->delete($id);

            $response = [
                'status'   => 200,
                'error'    => null,
                'messages' => ['success' => 'User Deleted Successfully']
            ];

            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound('No User Found with id ' . $id);
        }
    }

    /**
     * Obtener los datos del usuario.
     *
     * @param UserModel $model Modelo de usuario.
     * @param int|null $id ID del usuario.
     * @return array|null Datos del usuario o null si no se encontró.
     */
    private function getUserData(UserModel $model, $id): ?array
    {
        return $model->find($id);
    }

    /**
     * Obtener datos de entrada, ya sea JSON o entrada cruda.
     *
     * @return array Datos de entrada.
     */
    private function getInputData(): array
    {
        $json = $this->request->getJSON();

        return $json ? (array)$json : $this->request->getRawInput();
    }

    public function downloadPDF()
    {
        // Obtener la información de todos los usuarios
        $users = $this->getAllUsers();

        // Generar el PDF y obtener su URL
        $pdfUrl = $this->generatePDF($users);

        // Retornar la URL del PDF generado
        return $this->respond(['pdf_url' => $pdfUrl]);
    }

    /**
     * Generar un PDF con la información de los usuarios.
     *
     * @param array $users Información de usuarios.
     * @return string URL del PDF generado.
     */
    private function generatePDF(array $users): string
    {
        // Cargar la librería Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        // HTML para el contenido del PDF
        $html = view('pdf/user_list', ['users' => $users]);

        // Cargar el HTML en Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Almacenar el PDF en el directorio writable/pdf (asegúrate de que exista)
        $pdfFilePath = FCPATH . 'pdf/user_list.pdf';
        file_put_contents($pdfFilePath, $dompdf->output());

        // Retornar la URL del PDF generado
        return base_url('/pdf/user_list.pdf');
    }
}
