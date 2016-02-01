<?php


namespace Dryharder\Components;


use Dryharder\Models\CustomerFlash;
use View;

class Flash
{


    private $user;

    public function getFlashMessage()
    {

        $this->user = Customer::instance()->initByKey()->get();
        $message = $this->getInviteMessage();

        if (!$message) {
            return null;
        }

        return $this->viewMessage($message);

    }

    private function getInviteMessage()
    {
        $lastView = CustomerFlash::findLast(1, $this->user->id);
        if (!$lastView) {

            $lastView = new CustomerFlash();
            $lastView->flash_id = 1;
            $lastView->customer_id = $this->user->id;
            $lastView->qnt = 1;
            $lastView->save();

            return 'add_card';

        }
        return null;

    }

    private function viewMessage($message)
    {

        switch ($message) {

            case 'invite':

                $invite = new InviteComponent();

                return View::make('flash::invite', [
                    'invite_url' => $invite->url(),
                    'title'      => trans('flashes.invite.title'),
                    'content'    => trans('flashes.invite.content'),
                ]);

                break;

            case 'add_card':

                return View::make('flash::add_card', [
                    'title' => trans('flashes.add_card.title'),
                    'content' => ''
                ]);

                break;

            default:

                return null;
                break;

        }

    }


}