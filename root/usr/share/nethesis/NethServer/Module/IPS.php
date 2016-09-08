<?php
namespace NethServer\Module;

/*
 * Copyright (C) 2011 Nethesis S.r.l.
 * 
 * This script is part of NethServer.
 * 
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;

/**
 * Configure Suricata IPS
 *
 * @author Giacomo Sanchietti<giacomo.sanchietti@nethesis.it>
 */
class IPS extends \Nethgui\Controller\AbstractController
{
    private $policies = array('connectivity','balanced','security','expert');

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway');
    }

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('status', Validate::SERVICESTATUS, array(array('configuration', 'suricata', 'status'), array('configuration', 'firewall', 'nfqueue')));
        $this->declareParameter('Policy', $this->createValidator()->memberOf($this->policies), array('configuration', 'pulledpork', 'Policy'));
    }

    public function readStatus($status1, $status2)
    {
        return $status1;
    }

    public function writeStatus($status) {
        return array($status, $status);
    }


    protected function onParametersSaved($changes)
    {
        $this->getPlatform()->signalEvent('nethserver-pulledpork-save');
        $this->getPlatform()->signalEvent('nethserver-suricata-save');
        $this->getPlatform()->signalEvent('firewall-adjust');
    }

    public function prepareView(\Nethgui\View\ViewInterface $view) 
    {
        parent::prepareView($view);
        $view['PolicyDatasource'] =  array_map(function($fmt) use ($view) {
            return array($fmt, $view->translate($fmt . '_label'));
        }, $this->policies);

    }

}

