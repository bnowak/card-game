<?php
declare(strict_types = 1);

namespace Bnowak90\CardGame\Games\War;

use Bnowak90\CardGame\Exception\AbstractGameException;

/**
 * Exception related to errors of WarGame object
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class WarGameException extends AbstractGameException
{
    /**
     * More than one highest-rank card message
     *
     * @return WarGameException
     */
    public static function moreThanOneHighestRankCard() : self
    {
        return new self("They are placed in more than one highest-rank card");
    }
    
    /**
     * Unsupported move message
     *
     * @param string $moveName
     * @return WarGameException
     */
    public static function unsupportedMove(string $moveName) : self
    {
        return new self("$moveName is unsupported move in War Game");
    }
    
    /**
     * Wrong negative number of movements message
     *
     * @param int $cardCount
     * @return WarGameException
     */
    public static function wrongNegativeNumberOfMovements(int $cardCount) : self
    {
        return new self("Negative number $cardCount is not a valid number of movements");
    }
    
    /**
     * No player is loser message
     *
     * @return WarGameException
     */
    public static function noPlayerIsLoser() : self
    {
        return new self("There is no player who lost the game");
    }
    
    /**
     * Is player who has move message
     *
     * @return WarGameException
     */
    public static function isPlayerWhoHasMove() : self
    {
        return new self("There is player who has the move");
    }
}
