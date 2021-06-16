<?php

namespace App\Http\Controllers\Script;

use Illuminate\Http\Request;

class TestController extends Controller {
    /**
     *  @param  Request  $request
     *  @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        try {
            $shop = $this->getShop($request->get('shop'));

            if(!$shop) {
                throw new \Exception("Shop not found");
            }

            $settings = $shop->api()->rest('GET', '/admin/shop.json')['body']['shop'];

            $file_path = public_path(config('shopify-app.scripts.test'));
            $content   = $this->getContent($this->getFile($file_path), [
                'settings' => json_encode($settings)
            ]);

        } catch (\Exception $e) {
            abort(403);
        }

        return response()
            ->make($content, 200)
            ->header('Content-Type', 'application/javascript; charset=utf-8');
    }
}
