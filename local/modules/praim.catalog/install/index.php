<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;


/**
 * @internal
 */
class Praim_Catalog extends CModule
{
    public function __construct()
    {
        $this->MODULE_ID = 'praim.catalog';
        $this->MODULE_NAME = 'Прайм Каталог';
        $this->MODULE_DESCRIPTION = 'Модуль для работы с каталогом Rest API. Тестовое задание.';
        $this->MODULE_VERSION = '1.0.0';
        $this->MODULE_VERSION_DATE = '2025-12-24';
        $this->PARTNER_NAME = 'Graff Hause';
        $this->PARTNER_URI = 'https://pc2.graff.keenetic.name/';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->setOptions();
    }

    public function reInstall()
    {
        Praim\Catalor\Service\CacheService::clearCache();
        Option::delete($this->MODULE_ID);
        $this->setOptions();
    }

    public function setOptions(){
        //ID ИБ каталога
        Option::set( $this->MODULE_ID, 'IBLOCK_CATALOG', 2 );

        //время жизни кеша ( 30 мин )
        Option::set( $this->MODULE_ID, 'CACHETIME', 60 * 30 );

        //Адрес для выгрузки товаров
        Option::set( $this->MODULE_ID, 'MAIL_FROM', 'hause1986@mail.ru' );
    }

    public function doUninstall()
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
        Option::delete($this->MODULE_ID);
        Praim\Catalor\Service\CacheService::clearCache();
    }


}
