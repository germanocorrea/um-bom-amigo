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
                    return ['error', 'Nome de Usuário ou Email já cadastrado!'];
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
                return ['error', 'Imagem inválida!'];
            $avatarAddress = $this->fileUpload($_FILES['avatar'], 'avatar');

            if (!$avatarAddress)
                return ['error', 'Houve um erro ao realizar o upload da imagem!'];
            $this->model->set('avatar', $avatarAddress);

            if ($this->model->record())
                return ['success', 'Usuário Cadastrado com sucesso!'];
            else return ['error', 'Alguma coisa muito errada aconteceu. Chame um encanador.']
        }
    }

    public function login()
    {}

    public function perfil($username)
    {}

    public function configuracoes()
    {}
}