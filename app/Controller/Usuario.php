<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 12/12/16
 * Time: 08:16
 */

namespace Controller;


class Usuario extends Controller
{
    public function cadastrar()
    {
        if (isset($_POST['submit']))
        {
            $this->model->set('name', $_POST['name']);
            $this->model->set('password', $this->crypto($_POST['password']));

            unset($_POST['name']);
            unset($_POST['password']);

            foreach ($_POST as $key => $value)
            {
                if ($this->verifyInDB($key, $value))
                {
                    $this->variables('alert', ['error', 'Nome de Usuário ou Email já cadastrado!']);
                    return false;
                }
                $this->model->set($key, $value);
            }

//          TODO: preciso verificar se o foreach ai em cima vai dar certo, se não é necessário descomentar o trecho abaixo
//            if ($this->verifyInDB('username', $_POST['username']))
//                return ['error', 'Nome de Usuário já existe!'];
//            $this->model->set('username', $_POST['username']);
//
//            if ($this->verifyInDB('email', $_POST['email']))
//                return ['error', 'Email já está cadastrado!'];
//            $this->model->set('email', $_POST['email']);

            if (!$this->verifyImg($_FILES['avatar']))
            {
                $this->variables('alert', ['error', 'Imagem inválida!']);
                return false;
            }
            $avatarAddress = $this->fileUpload($_FILES['avatar'], 'avatar');

            if (!$avatarAddress)
            {
                $this->variables('alert', ['error', 'Houve um erro ao realizar o upload da imagem!']);
                return false;
            }

            $this->model->set('avatar', $avatarAddress);

            if ($this->model->record())
            {
                $this->variables('alert', ['success', 'Usuário cadastrado com sucesso!']);
                return true;
            }
            else
            {
                $this->variables('alert', ['error', 'Alguma coisa muito errada aconteceu. Chame um encanador.']);
                return false;
            }
        }
    }

    public function login()
    {}

    public function perfil($username)
    {}

    public function configuracoes()
    {}
}