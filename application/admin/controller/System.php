<?php
namespace app\admin\controller;

class System extends Main
{

    public function siteConfig()
    {
        return $this->fetch();
    }

}
