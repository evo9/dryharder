<?php


use Dryharder\Components\NotifyOrderComponent;
use Illuminate\Console\Command;

class NotifyOrderCommand extends Command
{
    protected $name = 'dh:notify';
    protected $description = "Notify Orders";

    public function fire()
    {
        $c = new NotifyOrderComponent($this);
        $c->fire();
    }

}