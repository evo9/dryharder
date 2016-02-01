<?php


use Dryharder\Components\Mailer;
use Dryharder\Models\CustomerInvite;
use Illuminate\Console\Command;

class InviteCommand extends Command
{

    protected $name = 'dh:invite';
    protected $description = "Invite";

    public function fire()
    {

        $list = CustomerInvite::whereBonus(0)->with('customer')->get()->all();

        $this->line('Найдено безбонусных инвайтов: ' . count($list));

        foreach($list as $item){
            if($item->customer && $item->customer->initExistsPaid()){

                $this->line('Отправляем письмо по инвайту: ' . $item->id . ', customer = ' . $item->customer->name);

                Mailer::inviteIsPayment($item->customer, $item->owner);
                $item->bonus = 1;
                $item->save();
            }
        }


    }

}