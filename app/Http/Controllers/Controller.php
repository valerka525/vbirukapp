<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Storage\Queries\Shop as IShopQuery;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Get Shop
     *
     * @param $domain
     * @return mixed
     */
    protected function getShop(string $domain)
    {
        try {
            if(empty($domain)) {
                throw new \Exception("Shop is empty");
            }

            $shopDomain = ShopDomain::fromNative($domain);

            $iShopQuery = new IShopQuery;
            $shop = $iShopQuery->getByDomain($shopDomain);

            if(!$shop->getId()) {
                throw new \Exception("Shop not found");
            }
        } catch (\Exception $e) {
            return false;
        }

        return $shop;
    }
}
