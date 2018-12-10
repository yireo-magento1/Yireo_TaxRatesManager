<?php
declare(strict_types=1);

use GuzzleHttp\Client;

/**
 * Class Yireo_TaxRatesManager_Model_System_Config_Source_Feed
 */
class Yireo_TaxRatesManager_Model_System_Config_Source_Feed
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->getSources() as $source) {
            $options[] = [
                'value' => $source,
                'label' => $source,
            ];
        }

        return $options;
    }

    /**
     * @return string[]
     */
    private function getSources(): array
    {
        $client   = new Client(['base_uri' => 'https://raw.githubusercontent.com/']);
        $response = $client->get('/yireo/Magento_EU_Tax_Rates/master/feeds.json');
        $contents = $response->getBody()->getContents();
        $data = json_decode($contents, true);

        return $data['feeds'];
    }
}
