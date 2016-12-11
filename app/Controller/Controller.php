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
        $logged = ($this->verifyLoggedSession()) ? 'user' : 'guest';
        $this->defineUserMenu($logged);

       if (isset($_SESSION['user_type']))
           if ($_SESSION['user_type'] == 'client')
               if (!isset($_SESSION['carrinho'])) {
                   $_SESSION['carrinho']['produtos'] = [];
                   $_SESSION['carrinho']['total_preco'] = 0;
           }
    }

    protected function debug($what)
    {
        echo '<pre>';
        print_r($what);
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
        // $model_name = get_class($model);
        // NOTE: isso ai em cima depois era utilizado abaixo como $this->{$model_name} = $model
        // entretanto, não é possível manter isso por causa dos namespaces
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

    protected function fileUpload($file)
    {
        $code = rand(100, 1000000000);
        $uploadFile = 'uploads/' . $code . '-' . $file['name'];
        move_uploaded_file($file['tmp_name'], SERVER_DIR . '/' . $uploadFile);
        return $uploadFile;
    }
}