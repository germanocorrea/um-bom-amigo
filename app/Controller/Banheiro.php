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

//            TODO: implementar o que segue em outro momento
//            if (isset($_POST['address']))
//            {
//                $geocoder = new Geocoder();
//                $geocoder->registerProviders(array(
//                    new GeocoderProvider(new CurlHttpAdapter()),
//                ));
//                $request = $geocoder->geocode($_POST['address']);
//            }

            $this->model->set('name', $_POST['name']);
            $this->model->set('longitude', $longitude);
            $this->model->set('latitude', $latitude);
        }

    }

    public function procurar()
    {
        if (!$this->verifyLoggedSession()) header('Location: ' . WEB_ROOT . '/usuario/login');
        // eita
    }

}