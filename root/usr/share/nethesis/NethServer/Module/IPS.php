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
    public $categories = array();
    public $selectableCategories = array();

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Gateway');
    }

    private function readCategories()
    {
       if(!$this->categories) {
           $this->categories = json_decode($this->getPlatform()->exec('sudo /usr/libexec/nethserver/suricata-list-categories')->getOutput(), TRUE);
           // hide all non-ET categories
           foreach ($this->categories as $category) {
               if (strpos($category,'ET') === 0) {
                   $this->selectableCategories[] = $category;
               }
           }
       }
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        $this->readCategories();
        if (!$this->getRequest()->isMutation()) {
            $categories = array();
            $categoryValidator = $this->createValidator()->memberOf(array('alert','block','disable'));
            $db = $this->getPlatform()->getDatabase('configuration');
            $block = explode(',',$db->getProp('suricata','BlockCategories'));
            $alert =  explode(',',$db->getProp('suricata','AlertCategories'));
            foreach ($block as $bc) {
                $this->declareParameter('Categories_'.$bc, $categoryValidator); 
                $this->parameters['Categories_'.$bc] = 'block';
            }
            foreach ($alert as $ac) {
                $this->declareParameter('Categories_'.$ac, $categoryValidator);
                $this->parameters['Categories_'.$ac] = 'alert';
            }
            foreach ($this->selectableCategories as $category) {
                if (!in_array($category,$block) && !in_array($category,$alert)) {
                    $this->declareParameter('Categories_'.$category, $categoryValidator);
                    $this->parameters['Categories_'.$category] = 'disable';
                }
            }
        }
    }

    public function process()
    {
        parent::process();

        if ($this->getRequest()->isMutation()) {
            $block = array();
            $alert = array();
            $db = $this->getPlatform()->getDatabase('configuration');
            foreach ($this->getRequest()->getParameterNames() as $param ) {
                if (strpos($param,'Categories_') === 0) {
                    $category = str_replace('Categories_','',$param);
                    if (!$category) {
                        continue;
                    }
                    $value = $this->getRequest()->getParameter($param);
                    if ($value == 'alert') {
                        $alert[] = $category;
                    }
                    if ($value == 'block') {
                        $block[] = $category;
                    }
                }
            }
            $db->setProp('suricata', array('BlockCategories' => implode(',',$block)));
            $db->setProp('suricata', array('AlertCategories' => implode(',',$alert)));

            $this->getPlatform()->signalEvent('firewall-adjust');
            $this->getPlatform()->signalEvent('nethserver-suricata-save');
            $this->getPlatform()->signalEvent('nethserver-pulledpork-save &');
        }
    }


    public function initialize()
    {
        $this->readCategories();
        $this->declareParameter('status', Validate::SERVICESTATUS, array(array('configuration', 'suricata', 'status'), array('configuration', 'firewall', 'nfqueue')));
        $this->declareParameter('Categories', Validate::ANYTHING);
        parent::initialize();
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
        $this->getPlatform()->signalEvent('firewall-adjust');
        $this->getPlatform()->signalEvent('nethserver-suricata-save');
        $this->getPlatform()->signalEvent('nethserver-pulledpork-save &');
    }

    public function prepareView(\Nethgui\View\ViewInterface $view) 
    {
        parent::prepareView($view);
        $this->readCategories();

        $view['actions'] = array('disable' => $view->translate('disable_label'), 'alert' => $view->translate('alert_label'), 'block' => $view->translate('block_label'));
        $view['categories'] = $this->selectableCategories;
    }

}

