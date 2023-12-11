<?php

namespace Lightweight\ShippingSetup\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\Framework\DB\Adapter\Pdo\Mysql;

class InstallData implements InstallDataInterface
{

    protected $_websiteFactory;

    /**
     * InstallData constructor.
     *
     * @param WebsiteFactory $websiteFactory
     */
    public function __construct(
        WebsiteFactory $websiteFactory
    )
    {
        $this->_websiteFactory = $websiteFactory;

    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createTableRates($setup);
    }

    public function createTableRates(ModuleDataSetupInterface $setup)
    {
        $tableRateTable = $setup->getTable('shipping_tablerate');
        $fields         = [
            'website_id',
            'dest_country_id',
            'condition_name',
            'condition_value',
            'price',
        ];

        $rate1 = [
            'DE',
        ];

        $rate2 = [
            'AT',
            'BE',
            'CZ',
            'DK',
            'FI',
            'FR',
            'GB',
            'IT',
            'LU',
            'MC',
            'NL',
            'SE',
        ];

        $rate3 = [
            'BG',
            'EE',
            'ES',
            'GR',
            'HR',
            'HU',
            'IE',
            'LT',
            'LV',
            'PL',
            'PT',
            'RO',
            'SI',
            'SK',
        ];

        $rate4 = [
            'AD',
            'AL',
            'BA',
            'CH',
            'CY',
            'GG',
            'GI',
            'JE',
            'LI',
            'ME',
            'MK',
            'MT',
            'NO',
            'RS',
            'SM',
            'VA',
        ];

        /** @var Mysql $connection */
        $connection = $setup->getConnection();

        // empty table rates table
        $connection->truncateTable($tableRateTable);
        $connection->beginTransaction();

        // Add config for EU
        $website = $this->_websiteFactory->create();
        $website->load('base');
        $websiteId = $website->getId();
        $values = [];

        if($websiteId) {
            foreach($rate1 as $countryId) {
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 0, 5.00]);
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 100, 0]);
            }
            foreach($rate2 as $countryId) {
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 0, 14.95]);
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 200, 0]);
            }
            foreach($rate3 as $countryId) {
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 0, 19.95]);
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 200, 0]);
            }
            foreach($rate4 as $countryId) {
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 0, 24.95]);
                array_push($values, [$websiteId, $countryId, 'package_value_with_discount', 250, 0]);
            }

            $connection->insertArray($tableRateTable, $fields, $values, Mysql::INSERT_ON_DUPLICATE);
            $connection->commit();
        }
    }

}
