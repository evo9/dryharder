<?php


namespace Dryharder\Manage\Controllers;

use Dryharder\Models\OrderRequest;

class OrderRequestController extends BaseController
{

    public function index()
    {
        if (!$this->isAcceptedJson()) {
            return \View::make('man::request.index');
        }

        $list = OrderRequest::whereRaw('`created_at` >= DATE_SUB(NOW(), INTERVAL 1 MONTH)')
            ->orderBy('id', 'desc')->get();

        return $list;

    }

}