<?php

namespace ModulesGarden\Servers\EasyDCIMv2\App\UI\admin\productConfig\sections;

use ModulesGarden\Servers\EasyDCIMv2\App\Libs\EasyDCIM\EasyDCIM;

class ClientAreaFeatures
{
    /**
     * @var EasyDCIM
     */
    protected $api;

    public function __construct($api)
    {
        $this->api = $api;
    }

    public function getOsTemplates($locationId = null){
        try {
            $templateList = $this->api->os->getTemplateList();
            if (!empty($locationId)) {
                $provisioningServerId = $this->api->os->getOsTemplateForLocation($locationId)->id;

                $templateList = array_values(array_filter($templateList, function($tpl) use ($provisioningServerId) {
                    // Match direct server_id
                    if (isset($tpl->server_id) && (int)$tpl->server_id === (int)$provisioningServerId) {
                        return true;
                    }
                    // Or any related server in "servers" array (pivot)
                    if (isset($tpl->servers) && is_array($tpl->servers)) {
                        foreach ($tpl->servers as $srv) {
                            if (isset($srv->id) && (int)$srv->id === (int)$provisioningServerId) {
                                return true;
                            }
                        }
                    }
                    return false;
                }));
            }
            return $templateList;
        } catch (\Exception $ex) {
            return [];
        }
    }

    public function getFields(){
        return $this->api->system->getFields();
    }
}
