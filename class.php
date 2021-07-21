<?php

namespace Prominado\Components\Crm\Company;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;

class Kanban extends \CBitrixComponent implements Controllerable
{

    public $queryLimit = 20;

    public function configureActions()
    {
        return [
            'getCompanies' => [
                'prefilters' => [
                    new ActionFilter\Authentication,
                ],
            ],
            'changeColumn' => [
                'prefilters' => [
                    new ActionFilter\Authentication,
                ],
            ],
        ];
    }

    public function getCompaniesAction($post){
        \Bitrix\Main\Loader::includeModule('crm');
        $this->arParams['KANBAN_COLUMNS_UFNAME'] = $post['colUfName'];
        $arCompanies = $this->getCompanies($post['colId'], $post['colPage']);
        return json_encode($arCompanies);
    }

    public function changeColumnAction($post){
        \Bitrix\Main\Loader::includeModule('crm');
        global $USER_FIELD_MANAGER;
        $this->arParams['KANBAN_COLUMNS_UFNAME'] = $post['colUfName'];
        $CompanyTable = new \Bitrix\Crm\CompanyTable();
        $arFields = Array(
            $post['colUfName'] => $post['columnId'],
        );
        $dbRes = $USER_FIELD_MANAGER->Update($CompanyTable::getUFId(), $post['companyId'], $arFields);
        //return 'upd ' . $post['companyId'] . ' to ' . $post['columnId'] . ' in ' . $post['colUfName'];
        return $dbRes;
    }

    private function getKanbanColumns($fieldName){
        $UserFieldEnum = new \CUserFieldEnum;
        $dbRes = $UserFieldEnum->GetList(
            array(),
            array('USER_FIELD_NAME' => $fieldName)
        );
        //$arColumns = Array(0 => 'Список');
        while($item = $dbRes->Fetch()){
            $arColumns[$item['ID']] = $item['VALUE'];
        }
        return $arColumns;
    }

    private function getUf($entity, $entityFields){
        if($entityFields['ID']){
            global $USER_FIELD_MANAGER;
            global $USER_FIELD_MANAGER_ENUM;
            $arUF = $USER_FIELD_MANAGER->GetUserFields($entity, $entityFields['ID']);
            if(!count($USER_FIELD_MANAGER_ENUM)){
                $UFSIDS = [];
                foreach($arUF as $UFS){
                    $UFSIDS[] = $UFS['ID'];
                }
                $CUserFieldEnum = new \CUserFieldEnum;
                $dbRes = $CUserFieldEnum->GetList([], ["USER_FIELD_ID" => $UFSIDS]);
                while($uf = $dbRes->Fetch()) {
                    $USER_FIELD_MANAGER_ENUM[$entity][$uf['USER_FIELD_ID']][$uf['ID']] = $uf['VALUE'];
                }
            }
            foreach($arUF as $key => $data){
                if($data['VALUE']){
                    switch($data['USER_TYPE_ID']){
                        case 'enumeration':
                            $entityFields[$key] = $USER_FIELD_MANAGER_ENUM[$entity][$data['ID']][$data['VALUE']];
                            break;
                        default:
                            $entityFields[$key] = $data['VALUE'];
                            break;
                    }
                }else{
                    $entityFields[$key] = '';
                }
            }
            return $entityFields;
        }
    }

    private function getCompanies($col, $page = 0, $order = ['ID' => 'ASC']){
        global $USER_FIELD_MANAGER;
        $CompanyTable = new \Bitrix\Crm\CompanyTable();
        $dbRes = $CompanyTable::getList([
            'order' => $order,
            'filter' => [$this->arParams['KANBAN_COLUMNS_UFNAME'] => $col],
            'limit' => $this->queryLimit,
            'offset' => $this->queryLimit * $page,
        ]);
        $arCompanies = [];
        while ($item = $dbRes->fetch()) {
            $item = $this->getUf($CompanyTable::getUFId(), $item);
            $item['DATE_MODIFY'] = $item['DATE_MODIFY']->format('Y-m-d H:i:s');
            $arCompanies[$item['ID']] = $item;
        }
        return $arCompanies;
    }

//    private function getCompanies($col, $page = 0, $order = ['ID' => 'ASC']){
//        $arOrder = Array('DATE_CREATE' => 'DESC');
//        $arFilter = Array();
//        $arSelect = Array();
//        $nPageTop = false;
//        $bCheckPermission = false;
//        $CCrmCompany = new \CCrmCompany($bCheckPermission);
//        $dbRes = $CCrmCompany::GetList($arOrder, $arFilter, $arSelect, $nPageTop);
//        while ($item = $dbRes->GetNext()) {
//            echo '<pre>'; var_dump($item); echo '</pre>';
//        }
//    }

    private function getColumns(){
        $arColumns = [];
        foreach($this->arResult['COLUMNS'] as $colNum => $colName) {
            $arCompanies = $this->getCompanies($colNum);
            foreach($arCompanies as $cid => $arCompany){
                $arColumns[$colNum][$cid] = $arCompany;
            }
        }
        return $arColumns;
    }

    private function returnToolbar(){
        return '<div class="crm-kanban-toolbar"><a href="/crm/company/details/0/" class="ui-btn-main ui-btn-primary">Добавить компанию</a></div>';
    }

    public function executeComponent()
    {
        $this->arParams['QUERY_LIMIT'] = $this->queryLimit;
        $this->arResult['COLUMNS'] = $this->getKanbanColumns($this->arParams['KANBAN_COLUMNS_UFNAME']);
        $this->arResult['COMPANIES'] = $this->getColumns();

        global $APPLICATION;
        ob_start();
        echo $this->returnToolbar();
        $content = ob_get_contents();
        ob_end_clean();
        $APPLICATION->AddViewContent('inside_pagetitle', $content, 1);

        $this->includeComponentTemplate();
    }

}