<?php

namespace Arura\Pages\CMS;

class AddonReader{

    protected $Addon;

    public function __construct(Addon $addon)
    {
        $this->setAddon($addon);
    }

    /**
     * @return mixed
     */
    public function getAddon() : Addon
    {
        return $this->Addon;
    }

    /**
     * @param mixed $Addon
     * @return AddonReader
     */
    public function setAddon(Addon $Addon)
    {
        $this->Addon = $Addon;
        return $this;
    }

}