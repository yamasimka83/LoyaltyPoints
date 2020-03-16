<?php

namespace LoyaltyGroup\LoyaltyPoints\Api\Model;

interface UserInterface
{
    /**
     * Get loyalty points.
     *
     * @return integer
     */
    public function getPoints();

    /**
     * Set loyalty points.
     *
     * @param $points
     */
    public function setPoints(int $points);
}
