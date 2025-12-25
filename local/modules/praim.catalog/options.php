<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

/**
 * @var CMain $APPLICATION
 */
$module_id = 'praim.catalog';

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
Loc::loadMessages(__FILE__);

if ($APPLICATION::GetGroupRight($module_id) < 'S') {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}
/** @noinspection PhpUnhandledExceptionInspection */
Loader::includeModule($module_id);
Extension::load(extNames: "ui.buttons");

$request = HttpApplication::getInstance()->getContext()->getRequest();

//Описание опций
/** @noinspection PhpUnhandledExceptionInspection */
$aTabs = [
    [
        'DIV' => 'main',
        'TAB' => Loc::getMessage('PRAIM_CATALOG_TAB_SETTINGS'),
        'TITLE' => Loc::getMessage('PRAIM_CATALOG_TAB_SETTINGS'),
        'OPTIONS' => [
            ['CACHETIME', Loc::getMessage('PRAIM_CATALOG_CACHETIME'), '', ['text', 100]],
            ['IBLOCK_CATALOG', Loc::getMessage('PRAIM_CATALOG_IBLOCK_ID'), '', ['text', 100]],
            ['MAIL_FROM', Loc::getMessage('PRAIM_CATALOG_MAIL_FROM'), '', ['text', 100]],
        ],
    ],
];

//region Save and Update
if ($request->isPost() && check_bitrix_sessid()) {
    if (isset($request['Update'])) {
        foreach ($aTabs as $aTab) {
            foreach ((array)$aTab['OPTIONS'] as $arOption) {
                if (!is_array($arOption) || $arOption['note']) {
                    continue;
                }
                $optionName = $arOption[0];
                $optionValue = $request->getPost($optionName);
                /** @noinspection PhpUnhandledExceptionInspection */
                Option::set($module_id, $optionName, is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
            }
        }
    } elseif (isset($request['dependency_update'])) {
        try {
            $m = new Praim_Catalog();
            $m->reInstall();

            echo CAdminMessage::ShowMessage([
                "MESSAGE" => Loc::getMessage('UPDATE_MODULE_SUCCESS_MESSAGE'),
                "HTML" => true,
                "TYPE" => "OK",
            ]);
        } catch (Exception $exception) {
            echo CAdminMessage::ShowMessage([
                "MESSAGE" => Loc::getMessage('UPDATE_MODULE_ERROR_MESSAGE'),
                "HTML" => true,
                "TYPE" => "ERROR",
            ]);
        }
    }
}
//endregion

//region Show setting
$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin(); ?>
    <form method='POST'
          action='<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($request['mid']) ?>&amp;lang=<?= $request['lang'] ?>'
          name='hdteam_leads_settings'>
        <?php foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->BeginNextTab();
                __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
            }
        }
        $tabControl->BeginNextTab();
        $tabControl->Buttons(); ?>
        <button
                class="ui-btn ui-btn-success"
                type="submit"
                name="Update"> <?= Loc::getMessage('MAIN_SAVE') ?></button>
        <button
                class="ui-btn ui-btn-secondary"
                type="submit"
                name="dependency_update"> <?= Loc::getMessage('MAIN_UPDATE') ?></button>
        <?= bitrix_sessid_post() ?>
    </form>
<?php $tabControl->End();
//endregion

