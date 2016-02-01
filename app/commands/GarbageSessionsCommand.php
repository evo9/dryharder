<?php


use Illuminate\Console\Command;

class GarbageSessionsCommand extends Command
{

    protected $name = 'dh:garbage-session';
    protected $description = "Garbage session files";

    public function fire()
    {
        $files = glob(storage_path() . '/sessions/*', GLOB_NOSORT);

        echo "\n";

        foreach($files as $file){

            $cTime = filectime($file);
            $aTime = fileatime($file);

            $cTimeDt = date('Y-m-d H:i:s', $cTime);
            $aTimeDt = date('Y-m-d H:i:s', $aTime);
            $lastHours = ceil( (time() - $cTime) / 60 / 60 );

            $this->line($file . ': add: ' . $aTimeDt . ' / change:' . $cTimeDt . ' / elapsed: ' . $lastHours);

            if($cTime == $aTime && $lastHours > 24){
                unlink($file);
                $this->error('unlink dt = ' . $cTimeDt . ' / touch session');
            }elseif($lastHours > 30*24){
                unlink($file);
                $this->error('unlink dt = ' . $cTimeDt . ' / left ' . $lastHours . ' hours total');
            }

        }

        echo "\n";
    }

}