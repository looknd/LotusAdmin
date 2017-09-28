<?php
namespace app\admin\controller;

class Index extends Main
{
    public function index()
    {
        return $this->fetch();
    }

    public function child()
    {
        return $this->fetch();
    }

    public function main()
    {
        return $this->fetch();
    }

    public function welcome()
    {
        return $this->fetch();
    }

}
