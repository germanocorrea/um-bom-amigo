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
        if (isset($_POST['submit']))
        {
            $this->model->setTableName('avaliacaos');
            $this->model->set('comentario', $_POST['comentario']);
            $this->model->set('estrelas', $_POST['fb']);
            $this->model->set('idBanheiro', (int) $_POST['banheiro']);
            $this->model->set('idUsuario', (int) $_SESSION['user']);

            $this->model->record();

            $this->variables['alert'] = ['success', 'Banheiro avaliado com sucesso!'];
        }

        $this->model->setTableName('banheiros');
        $banheiro = $this->model->search('one', [
                'conditions' => [
                        'id = ?' => $id
                ]
        ]);

        $this->variables['banheiro_nome'] = $banheiro->get('name');
        $this->variables['banheiro_id'] = $banheiro->get('id');
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
        $avaliacoes = [];
        $localization = explode(',', $localization);
        $current_pos = GeoLocation::fromDegrees((float) $localization[0], (float)  $localization[1]);

        $locais = $this->model->search('all');

        $this->model->setTableName('avaliacaos');

        foreach ($locais as $local) {
            $local_pos = GeoLocation::fromDegrees((float) $local['latitude'], (float) $local['longitude']);
            if ($current_pos->distanceTo($local_pos, 'kilometers') <= $this->raio)
                $retorno[$local['id']] = $local;

            $avaliacoes[$local['id']] = $this->model->search('all', [
                    'conditions' => [
                            'idBanheiro = ?' => $local['id']
                    ]
            ]);
        }

        $none = [];

        foreach ($avaliacoes as $key => $avaliacoes_local)
        {
            if ($avaliacoes_local == null) $none[] = $key;
        }

        foreach ($none as $this) unset($avaliacoes[$this]);

        foreach ($avaliacoes as $key => $avaliacoes_local)
        {
            $media = 0;

            $qnt = count($avaliacoes_local);
            foreach ($avaliacoes_local as $avaliacao)
            {
                $media += $avaliacao['estrelas'];
            }
            $avaliacoes[$key] = (float) $media / (float) $qnt;
        }

        $this->model->setTableName('imagens');
        $imagem = [];
        foreach ($retorno as $local)
        {
            $img = $this->model->search('one', [
                'conditions' => [
                    'idBanheiro = ?' => $local['id']
                ]
            ]);

            if ($img != null)
                $imagem[$img->get('idBanheiro')] = $img->get('endereco');

            foreach ($none as $key => $item) {
                if ($local['id'] == $item)
                {
                    $none[$key] = $local;
                }
            }
        }


        $this->variables['imgs'] = $imagem;
        $this->variables['medias'] = $avaliacoes;

        foreach ($retorno as $key => $local)
            if (isset($avaliacoes[$local['id']]))
                $retorno[$key]['media'] = $avaliacoes[$local['id']];

        $retornomesmo = [];

        arsort($avaliacoes);

        foreach ($avaliacoes as $key => $avaliacao)
        {
            foreach ($retorno as $local)
            {
                if ($local['id'] == $key) $retornomesmo[$key] = $local;
            }
        }

        $this->variables['locais'] = $retornomesmo;
    }

}