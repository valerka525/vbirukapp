<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Osiset\ShopifyApp\Storage\Models\Plan;

class HomeController extends Controller {
    /**
     * Index route which displays the home page of the app.
     *
     * @return View
     */
    public function index()
    {
        $shop = Auth::user();
        $products = $shop->api()->rest('GET', '/admin/products.json')['body']['products'];

        return view('home.index',
            [
                'products' => $products,
                'plans' => Plan::all(),
                'current_plan' => $shop->plan_id
            ]);
    }
}
