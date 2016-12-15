<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 12/12/16
 * Time: 08:17
 */

namespace Controller;


class Banheiro extends Controller
{
    public function visualizar($id)
    {
        if (!$this->verifyLoggedSession()) header('Location: ' . WEB_ROOT . '/usuario/login');
        $map = new Map();
    }

    public function classificar($id)
    {
        if (!$this->verifyLoggedSession()) header('Location: ' . WEB_ROOT . '/usuario/login');
        // TODO: inserir classificação no banco
    }

    public function adicionar()
    {
        if (!$this->verifyLoggedSession()) header('Location: ' . WEB_ROOT . '/usuario/login');
        if (isset($_POST['submit']))
        {

            $this->model->set('name', $_POST['name']);
            $this->model->set('longitude', $_POST['longitude']);
            $this->model->set('latitude', $_POST['latitude']);
            $this->model->record();

            $banheiro = $this->model->search('one', [
                'conditions' => [
                    'longitude = ?' => $_POST['longitude'],
                    'latitude = ?' => $_POST['latitude']
                ]
            ]);

            $this->model->setTableName('imagens');
            $this->model->set('idBanheiro', $banheiro->get('id'));

            if (!$this->verifyImg($_FILES['imagem']['type']))
            {
                $this->variables['alert'] = ['error', 'Imagem inválida!'];
                return false;
            }
            $address = $this->fileUpload($_FILES['imagem'], 'banheiros');

            if (!$address)
            {
                $this->variables['alert'] = ['error', 'Houve um erro ao realizar o upload da imagem!'];
                return false;
            }

            $this->model->set('endereco', $address);

            $this->model->record();
        }

    }

    public function procurar()
    {
        if (!$this->verifyLoggedSession()) header('Location: ' . WEB_ROOT . '/usuario/login');
        // eita
    }

}