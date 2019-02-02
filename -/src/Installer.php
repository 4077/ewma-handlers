<?php namespace ewma\handlers;

use Illuminate\Database\Schema\Blueprint;

class Installer implements \ewma\Interfaces\ModuleInstallerInterface
{
    public function getSchemas()
    {
        return [
            'abc' => function (Blueprint $table) {
                $table->increments('id');
            }
        ];
    }
}
