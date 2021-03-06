<?php

namespace tailgate\Controller;

define('TAILGATE_DEFAULT_ADMIN_COMMAND', 'Visitor');

/**
 * @license http://opensource.org/licenses/lgpl-3.0.html
 * @author Matthew McNaney <mcnaney at gmail dot com>
 */
class Admin extends \phpws2\Http\Controller
{

    public function get(\Canopy\Request $request)
    {
        $command = $this->routeCommand($request);
        return $command->get($request);
    }

    public function post(\Canopy\Request $request)
    {
        $command = $this->routeCommand($request);
        return $command->post($request);
    }

    private function routeCommand($request)
    {
        $command = $request->shiftCommand();

        if (empty($command)) {
            $command = TAILGATE_DEFAULT_ADMIN_COMMAND;
        }

        $className = 'tailgate\Controller\Admin\\' . $command;
        if (!class_exists($className)) {
            throw new \phpws2\Http\NotAcceptableException($request);
        }
        $commandObject = new $className($this->getModule());
        return $commandObject;
    }

}
