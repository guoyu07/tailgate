<?php

namespace tailgate\Controller\Admin;

use tailgate\Factory\Lottery as Factory;
use tailgate\Resource\Lottery as Resource;

/**
 * @license http://opensource.org/licenses/lgpl-3.0.html
 * @author Matthew McNaney <mcnaney at gmail dot com>
 */
class Lottery extends Base
{

    protected function getJsonView($data, \Canopy\Request $request)
    {
        $command = $request->getVar('command');
        $factory = new Factory;
        $json = array('success' => true);

        switch ($command) {
            case 'getAvailableSpots':
                $json = array('available_spots' => $factory->totalAvailableSpots());
                break;

            case 'submissionCount':
                $json = array('submissions' => $this->getTotalSubmissions());
                break;

            case 'getUnclaimedSpots':
                $json = Factory::getAvailableSpots(null, null, false, true);
                break;

            default:
                throw new \Exception('Bad command:' . $request->getVar('command'));
        }

        $view = new \phpws2\View\JsonView($json);
        return $view;
    }

    public function post(\Canopy\Request $request)
    {
        $factory = new Factory;
        $view = new \phpws2\View\JsonView(array('success' => true));

        if (!$request->isVar('command')) {
            throw new \Exception('Bad command');
        }
        switch ($request->getVar('command')) {
            case 'chooseWinners':
                $view = $this->chooseWinners();
                break;

            case 'complete':
                $factory->completeGame();
                break;

            case 'notify':
                $view = $view = new \phpws2\View\JsonView(array('sent' => $factory->notify()));
                break;

            case 'completeLottery':
                $factory->completeLottery();
                break;

            case 'pickup':
                $this->postPickUp();
                break;

            default:
                throw new \Exception('Bad command:' . $request->getVar('command'));
        }
        $response = new \Canopy\Response($view);
        return $response;
    }

    private function postPickUp()
    {
        $lottery_id = filter_input(INPUT_POST, 'lotteryId',
                FILTER_SANITIZE_NUMBER_INT);
        $factory = new Factory;
        $factory->pickedUp($lottery_id);
    }

    private function chooseWinners()
    {
        try {
            $factory = new Factory;
            $winners = $factory->chooseWinners();
            $spots_left = $factory->totalAvailableSpots() - $winners;
            $data = array('spots_filled' => $winners, 'spots_left' => $spots_left);
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }
        $view = new \phpws2\View\JsonView($data);
        return $view;
    }

    /**
     * Number of student submissions to this lottery
     * @return integer
     */
    private function getTotalSubmissions()
    {
        $factory = new Factory;
        $current_game = \tailgate\Factory\Game::getCurrentId();
        if (empty($current_game)) {
            $submissions = 0;
        } else {
            $submissions = $factory->getTotalSubmissions($current_game);
        }
        return (int) $submissions;
    }

}
