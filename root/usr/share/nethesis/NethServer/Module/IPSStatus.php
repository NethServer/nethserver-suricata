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
 * Show IPS alerts
 *
 * @author Giacomo Sanchietti
 *
 */
class IPSStatus extends \Nethgui\Controller\AbstractController
{

    private $report;
    private $logs;
    private $path = '/var/log/snort';

    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return \Nethgui\Module\SimpleModuleAttributesProvider::extendModuleAttributes($base, 'Status');
    }


    private function listLogs()
    {
        $logs = array();

        foreach (scandir($this->path) as $log) {
            if (preg_match('/alert-(\d)+.gz/', $log) ) {
                $logs[] = $log;
            }
        }

        arsort($logs);
        array_unshift($logs,'alert');
        return $logs;
    }

    private function getReport($file = "/var/log/snort/alert")
    {
        return $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/suricata-report '.$file)->getOutput(); 
    }

    public function initialize()
    {
        parent::initialize();
        if (!$this->logs) {
            $this->logs = $this->listLogs();
        }
        $this->declareParameter('logs', $this->createValidator()->memberOf($this->logs), NULL);
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        parent::bind($request);
        if ($request->isMutation()) {
           if ($request->hasParameter('logs')) {
               $this->report = $this->getReport($this->path.'/'.$request->getParameter('logs'));
           }
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        if (!$this->logs) {
            $this->logs = $this->listLogs();
        }

        $view['logsDatasource'] =  array_map(function($fmt) use ($view) {
            if ($fmt == 'alert') {
                $label = $view->translate('log_current');
            } else {
                $tmp = explode('-',$fmt);
                $label = strstr($tmp[1],'.',true);
            }
            return array($fmt, $label);
        }, $this->logs);


        if (!$this->report) {
            $this->report = $this->getReport();
        }
        $view['report'] = $this->report;
    }
}

