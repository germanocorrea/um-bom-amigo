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
    public function cadastrar($update = false)
    {
        if (isset($_POST['submit']))
        {
            if ($update)
            {
                $user = $this->model->search('one', [
                    'conditions' => [
                        'username = ?' => $_POST['username']
                    ]
                ]);
                $this->model->set('id', $user->get('id'));
            }

            unset($_POST['submit']);

            $this->model->set('name', $_POST['name']);
            $this->model->set('password', $this->crypto($_POST['password']));

            unset($_POST['name']);
            unset($_POST['password']);

            foreach ($_POST as $key => $value)
            {
                if ($this->verifyInDB($key, $value))
                {
                    $this->variables['alert'] = ['error', 'Nome de Usuário ou Email já cadastrado!'];
                    return false;
                }
                $this->model->set($key, $value);
            }

            if (!$this->verifyImg($_FILES['avatar']['type']))
            {
                $this->variables['alert'] = ['error', 'Imagem inválida!'];
                return false;
            }
            $avatarAddress = $this->fileUpload($_FILES['avatar'], 'avatars');

            if (!$avatarAddress)
            {
                $this->variables['alert'] = ['error', 'Houve um erro ao realizar o upload da imagem!'];
                return false;
            }

            $this->model->set('avatar', $avatarAddress);

            if ($this->model->record())
            {
                $this->variables['alert'] = ['success', 'Dados cadastrados com sucesso!'];
                return true;
            }
            else
            {
                $this->variables['alert'] = ['error', 'Alguma coisa muito errada aconteceu. Chame um encanador.'];
                return false;
            }
        }
    }

    public function login()
    {
        if ($this->verifyLoggedSession()) header('Location: ' . WEB_ROOT);

        if (isset($_POST['submit']))
        {
            $user = $this->model->search('one', [
                'conditions' => [
                    'username = ?' => $_POST['username']
                ]
            ]);
            if (!$user)
            {
                $this->variables['alert'] = ['error', 'Nome de usuário não está cadastrado!'];
                return false;
            }

            if ($user->get('password') != $_POST['password'])
            {
                $this->variables['alert'] = ['error', 'Senha está incorreta!'];
                return false;
            }

            $_SESSION['user'] = $user->get('id');
            $_SESSION['username'] = $user->get('username');
            $_SESSION['user_type'] = $user->get('admin');

            header('Location: ' . WEB_ROOT);
        }
    }

    public function perfil($user_identification, $column = 'username')
    {
        $user = $this->model->search('one', [
            'conditions' => [
               $column . ' = ?' => $user_identification
            ]
        ]);

        if ($user == null) header('Location: ' . WEB_ROOT);

        $this->variables['id'] = $user->get('id');
        $this->variables['name'] = $user->get('name');
        $this->variables['username'] = $user->get('username');
        $this->variables['email'] = $user->get('email');
        $this->variables['avatar'] = $user->get('avatar');
        $this->variables['admin'] = $user->get('admin');
        $this->variables['password'] = $this->decrypt($user->get('password'));
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . WEB_ROOT);
    }

    public function configuracoes()
    {
        $this->cadastrar(true);

        $this->perfil($_POST['user'], 'id');
    }
}