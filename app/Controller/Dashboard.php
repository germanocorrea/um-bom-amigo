<?php
/**
 * Created by PhpStorm.
 * User: ggcorrea
 * Date: 12/12/16
 * Time: 08:05
 */

namespace Controller;


class Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->verifyPermission();
    }

    public function index()
    {}

    public function imagens()
    {}

    public function locais()
    {
        // TODO: excluir locais
    }

    public function usuarios()
    {
        // TODO: tornar admin
        // TODO: suspender conta
        // TODO: excluir conta
    }

    public function imagem($id)
    {
        // TODO: excluir imagem
    }
}