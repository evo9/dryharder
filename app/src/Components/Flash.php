<?php


namespace Dryharder\Components;


use Dryharder\Models\CustomerFlash;
use View;

class Flash
{
    private $user;

    private $methodName;

    private $type;

    public function __construct($type)
    {
        $this->type = $type;
        $this->methodName = $this->getMethodName($type);
        $this->user = Customer::instance()->initByKey()->get();
    }

    private function getMethodName($type)
    {
        if ($type > '') {
            $result = '';
            $typeArr = explode('_', $type);
            foreach ($typeArr as $t) {
                $result .= ucfirst($t);
            }

            return $result;
        }
        else {
            return null;
        }
    }

    public function getFlashMessage()
    {
        if (!$this->methodName) {
            return null;
        }
        $methodName = 'get%sMessage';
        $methodName = sprintf($methodName, $this->methodName);

        $params = [];

        if (method_exists($this, $methodName)) {
            $result = $this->$methodName();
            if (!$result['status']) {
                return null;
            }
            if (isset($result['params']))
                $params = $result['params'];
        }

        return $this->viewMessage($params);

    }

    private function getInviteMessage()
    {
        $result = [
            'status' => false
        ];
        $lastView = CustomerFlash::findLast(1, $this->user->id);
        if (!$lastView) {

            $lastView = new CustomerFlash();
            $lastView->flash_id = 1;
            $lastView->customer_id = $this->user->id;
            $lastView->qnt = 1;
            $lastView->save();

            $result['status'] = true;

        }

        // только три раза
        if ($lastView->qnt >= 3) {
            return $result;
        }

        // не чаще, чем раз в час
        if (time() - strtotime($lastView->updated_at) < 60*60) {
            return $result;
        }

        $lastView->qnt++;
        $lastView->save();

        $result['status'] = true;
        $invite = new InviteComponent();
        $result['params'] = ['invite_url' => $invite->url()];

        return $result;

    }

    private function getAddCardMessage()
    {
        $result['status'] = false;

        $lastView = CustomerFlash::findLast(1, $this->user->id);
        if (!$lastView) {

            $lastView = new CustomerFlash();
            $lastView->flash_id = 1;
            $lastView->customer_id = $this->user->id;
            $lastView->qnt = 1;
            $lastView->save();

            $result['status'] = true;

        }

        return $result;

    }

    private function viewMessage($data = [])
    {
        return View::make('flash::' . $this->type, $data);
    }


}