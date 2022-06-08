<?php declare(strict_types = 1);

/*
** Zabbix
** Copyright (C) 2001-2021 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

namespace Modules\GeomapHosts\Actions;

use CController;
use CControllerResponseData; 
use CControllerResponseFatal;
use CRoleHelper;
use API;

class CControllerGeomapHostView extends CController {
    protected function init(): void {
		$this->disableSIDValidation();
	}

	protected function checkInput(): bool {
		return TRUE;
	}

	protected function checkPermissions(): bool {
		return $this->checkAccess(CRoleHelper::UI_MONITORING_HOSTS);
	}

	protected function doAction(): void {
		$hosts = API::Host()->get([
			'output'=> ['name', 'hostid'],
			'selectInventory' => ['location_lat', 'location_lon'],
            'searchWildcardsEnabled' => true,
            'searchInventory' => ['location_lat' => '*'],
		]);

        $final_hosts = array();
        foreach($hosts as $host){
            if ($host['inventory']['location_lat'] != "" && $host['inventory']['location_lon']!=""){
                array_push($final_hosts, $host);
            }
        }

		$index=0;
		foreach($final_hosts as $host){
			$problems = API::Problem()->get([
				'hostids' => $host['hostid'],
				'output' => ['severity']
			]);

			$final_hosts[$index]['problems'] = $problems;
			$index++;
		}

		$data = [
			'hosts' => $final_hosts,			
        ];

		$response = new CControllerResponseData($data);
		$response->setTitle(_('Hosts'));
		$this->setResponse($response);
	}
}
