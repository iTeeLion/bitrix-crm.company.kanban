<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>

<?
$APPLICATION->IncludeComponent(
    'bitrix:crm.control_panel',
    '',
    array(
        'ID' => $cpID,
        'ACTIVE_ITEM_ID' => $cpActiveItemID,
        'PATH_TO_COMPANY_LIST' => (isset($arResult['PATH_TO_COMPANY_LIST']) && !$isMyCompanyMode) ? $arResult['PATH_TO_COMPANY_LIST'] : '',
        'PATH_TO_COMPANY_EDIT' => (isset($arResult['PATH_TO_COMPANY_EDIT']) && !$isMyCompanyMode) ? $arResult['PATH_TO_COMPANY_EDIT'] : '',
        'PATH_TO_CONTACT_LIST' => isset($arResult['PATH_TO_CONTACT_LIST']) ? $arResult['PATH_TO_CONTACT_LIST'] : '',
        'PATH_TO_CONTACT_EDIT' => isset($arResult['PATH_TO_CONTACT_EDIT']) ? $arResult['PATH_TO_CONTACT_EDIT'] : '',
        'PATH_TO_DEAL_LIST' => isset($arResult['PATH_TO_DEAL_LIST']) ? $arResult['PATH_TO_DEAL_LIST'] : '',
        'PATH_TO_DEAL_EDIT' => isset($arResult['PATH_TO_DEAL_EDIT']) ? $arResult['PATH_TO_DEAL_EDIT'] : '',
        'PATH_TO_LEAD_LIST' => isset($arResult['PATH_TO_LEAD_LIST']) ? $arResult['PATH_TO_LEAD_LIST'] : '',
        'PATH_TO_LEAD_EDIT' => isset($arResult['PATH_TO_LEAD_EDIT']) ? $arResult['PATH_TO_LEAD_EDIT'] : '',
        'PATH_TO_QUOTE_LIST' => isset($arResult['PATH_TO_QUOTE_LIST']) ? $arResult['PATH_TO_QUOTE_LIST'] : '',
        'PATH_TO_QUOTE_EDIT' => isset($arResult['PATH_TO_QUOTE_EDIT']) ? $arResult['PATH_TO_QUOTE_EDIT'] : '',
        'PATH_TO_INVOICE_LIST' => isset($arResult['PATH_TO_INVOICE_LIST']) ? $arResult['PATH_TO_INVOICE_LIST'] : '',
        'PATH_TO_INVOICE_EDIT' => isset($arResult['PATH_TO_INVOICE_EDIT']) ? $arResult['PATH_TO_INVOICE_EDIT'] : '',
        'PATH_TO_REPORT_LIST' => isset($arResult['PATH_TO_REPORT_LIST']) ? $arResult['PATH_TO_REPORT_LIST'] : '',
        'PATH_TO_DEAL_FUNNEL' => isset($arResult['PATH_TO_DEAL_FUNNEL']) ? $arResult['PATH_TO_DEAL_FUNNEL'] : '',
        'PATH_TO_EVENT_LIST' => isset($arResult['PATH_TO_EVENT_LIST']) ? $arResult['PATH_TO_EVENT_LIST'] : '',
        'PATH_TO_PRODUCT_LIST' => isset($arResult['PATH_TO_PRODUCT_LIST']) ? $arResult['PATH_TO_PRODUCT_LIST'] : '',
        'MYCOMPANY_MODE' => ($arResult['MYCOMPANY_MODE'] === 'Y' ? 'Y' : 'N'),
        'PATH_TO_COMPANY_WIDGET' => isset($arResult['PATH_TO_COMPANY_WIDGET']) ? $arResult['PATH_TO_COMPANY_WIDGET'] : '',
        'PATH_TO_COMPANY_PORTRAIT' => isset($arResult['PATH_TO_COMPANY_PORTRAIT']) ? $arResult['PATH_TO_COMPANY_PORTRAIT'] : ''
    ),
    $component
);
?>

<div class="crm-kanban-grid" data-columns-ufname="<?=$arParams['KANBAN_COLUMNS_UFNAME']?>">
    <? $columnNum = 0;?>
    <? foreach($arResult['COLUMNS'] as $colId => $colName): ?>
        <?
        $arColCompanies = $arResult['COMPANIES'][$colId];
        if($arParams['KANBAN_COLORS'][$columnNum]){
            $columnBadgeStyle = 'background-color: #' . $arParams['KANBAN_COLORS'][$columnNum] . ';';
            }else{
            $columnBadgeStyle = '';
        }
        ?>
        <div class="crm-kanban-column">
            <div class="crm-kanban-column-badge" style="<?=$columnBadgeStyle?>">
                <?=$colName?>
            </div>
            <div class="crm-kanban-column-items" data-column data-column-id="<?=$colId?>" data-column-page="1">
                <? foreach($arColCompanies as $cid => $company): ?>
                    <div class="crm-kanban-row" data-card data-company-id="<?=$cid?>">
                        <div class="crm-kanban-card">
                            <p><a href="/crm/company/details/<?=$cid?>/"><?=$company['TITLE']?></a></p>
                            <p>Город: <?=$company['UF_CRM_1519584932']?></p>
                            <p>Оборот: <?=$company['REVENUE']?></p>
                            <p>Форма: <?=$company['UF_CRM_1535965570988']?></p>
                        </div>
                    </div>
                <? endforeach; ?>
                <? if(count($arColCompanies) >= $arParams['QUERY_LIMIT']){ echo '<div data-column-loadmore='.$colId.'></div>'; } ?>
            </div>
        </div>
    <? $columnNum++; ?>
    <? endforeach; ?>
</div>