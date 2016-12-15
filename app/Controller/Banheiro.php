<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 12/12/16
 * Time: 08:17
 */

namespace Controller;


use AnthonyMartin\GeoLocation\GeoLocation;

class Banheiro extends Controller
{

    private $raio = 6;

    public function visualizar($id)
    {
        if (!$this->verifyLoggedSession()) header('Location: ' . WEB_ROOT . '/usuario/login');
//        $map = new Map();
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

            $this->model->unsetAll();
            $this->model->setTableName('imagens');
            $this->model->set('idBanheiro', $banheiro->get('id'));

            if (!$this->verifyImg($_FILES['image']['type']))
            {
                $this->variables['alert'] = ['error', 'Imagem inválida!'];
                return false;
            }
            else
            {
                $address = $this->fileUpload($_FILES['image'], 'banheiros');
            }

            if (!$address)
            {
                $this->variables['alert'] = ['error', 'Houve um erro ao realizar o upload da imagem!'];
                return false;
            }

            $this->model->set('endereco', $address);

            $this->model->record();
        }

    }

    public function procurar($localization = false)
    {
        if (!$localization): ?>
            <script>
                if (navigator.geolocation)
                {
                    navigator.geolocation.getCurrentPosition(showPosition);
                }
                function showPosition(position)
                {
                    var route = position.coords.latitude + "," + position.coords.longitude;
                    window.location.href = "<?php echo WEB_ROOT ?>/banheiro/procurar/" + route;
                }
            </script>
        <?php endif;

        if (!$this->verifyLoggedSession()) header('Location: ' . WEB_ROOT . '/usuario/login');

        $retorno = [];
        $localization = explode(',', $localization);
        $current_pos = GeoLocation::fromDegrees($localization[0], $localization[1]);

        $locais = $this->model->search('all');

        foreach ($locais as $local) {
            $local_pos = GeoLocation::fromDegrees($local['latitude'],$local['longitude']);
            if ($current_pos->distanceTo($local_pos, 'kilometers') <= $this->raio)
                $retorno[] = $local;
        }

        $this->variables['locais'] = $retorno;
    }

}