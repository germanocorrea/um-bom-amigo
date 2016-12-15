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
        $map = new Map();
    }

    public function classificar($id)
    {
        // TODO: inserir classificação no banco
    }

    public function adicionar()
    {
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
        }

    }

    public function procurar()
    {
        // eita
    }

}