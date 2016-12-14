<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 04/10/16
 * Time: 10:54
 */

namespace Controller;


abstract class Controller
{
    protected $variables;

    protected $model;

    public function __construct()
    {
        session_start();
        $this->setVariables('webaddress', WEB_ROOT);
//        $logged = ($this->verifyLoggedSession()) ? 'user' : 'guest';
    }

    protected function debug($what)
    {
        echo '<pre>';
        print_r($what);
        echo '</pre><hr><pre>';
        var_dump($what);
        echo '</pre>';
        die;
    }

    protected function verifyPermission()
    {
        if ($_SESSION['user_type'] != 'admin') header('Location: ' . WEB_ROOT);
    }

    public function setVariables($var, $value)
    {
        $this->variables[$var] = $value;
    }


    public function getVariables()
    {
        return (array) $this->variables;
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function verifyLoggedSession()
    {
        if (isset($_SESSION['user'])) return true;
        return false;
    }

    public function defineUserMenu($logged)
    {
        switch ($logged)
        {
            case 'guest':
                $this->variables['have_cart'] = false;
                $this->variables['user_name'] = 'Visitante';
                $this->variables['user_dropdown'] = [
                    ['Fazer Login', WEB_ROOT . '/user/login'],
                    ['Cadastre-se!', WEB_ROOT . '/user/new'],
                ];
                break;
            case 'user':
                $this->variables['have_cart'] = true;
                $this->variables['user_name'] = $_SESSION['user'];
                if ($_SESSION['user_type'] == 'admin') $this->variables['user_dropdown'] = [['Administração', WEB_ROOT . '/administration']];
                $this->variables['user_dropdown'][] = ['Perfil', WEB_ROOT . '/user/profile'];
                $this->variables['user_dropdown'][] = ['Configurações', WEB_ROOT . '/user/configuration'];
                $this->variables['user_dropdown'][] = ['Sair', WEB_ROOT . '/user/logout'];
                break;
        }
        return true;
    }

    protected function fileUpload($file, $subfolder)
    {
        $code = rand(100, 1000000000);
        $uploadFile = 'uploads' . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $code . '-' . $file['name'];
        if (is_dir('uploads' . DIRECTORY_SEPARATOR) && is_dir('uploads' . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR) && !is_file($uploadFile))
            move_uploaded_file($file['tmp_name'], SERVER_DIR . DIRECTORY_SEPARATOR . $uploadFile);
        else return false;
        return $uploadFile;
    }

    protected function verifyImg($file)
    {
        $file_name = explode('.', $file);
        $extension = strtolower($file_name[1]);
        if ($extension != 'png' || $extension != 'jpg' || $extension != 'jpeg')
            return false;
        // TODO: complementar a verificação de imaagem
    }

    protected function verifyInDB($column, $value, $table = false)
    {
        if ($table) $this->model->setTableName($table);

        if ($this->model->search('one', [
            'conditions' => [
                $column . ' = ?' => $value
            ]
        ])) return true;
        else return false;
    }

    protected function crypto($string)
    {
        return $string;
    }

    protected function decrypt($string)
    {
        return $string;
    }
}