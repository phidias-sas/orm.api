<?php
namespace Phidias\Orm\Module;

use Phidias\Orm\Module\Entity as Module;

class Controller
{
    public static function collection()
    {
        return Module::collection()
            ->allAttributes();
    }

    public static function get($moduleId)
    {
        return new Module($moduleId);
    }

    public static function save($input, $moduleId = null)
    {
        $retval = [];
        $modules = is_array($input) ? $input : [$input];

        foreach ($modules as $moduleData) {
            $module = new Module($moduleId);
            $module->setValues($moduleData);
            $module->save();

            $retval[] = $module;
        }

        return is_array($input) ? $retval : $retval[0];
    }

    public static function delete($moduleId)
    {
        $module = new Module($moduleId);
        return $module->delete();
    }
}