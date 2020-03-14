<?php

namespace LoyaltyGroup\LoyaltyPoints\Api\Model;

interface UserInterface
{
    /**
     * @return mixed
     */
    public function getPoints();

    /**
     * @param $points
     */
    public function setPoints($points);
}
