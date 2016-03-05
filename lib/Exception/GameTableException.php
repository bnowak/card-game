<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Exception;

use Bnowak\CardGame\Card;
use Exception;

/**
 * Exception related to errors of GameTable object
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class GameTableException extends Exception
{
    /**
     * No player has placed card message
     *
     * @param Card $card
     * @return GameTableException
     */
    public static function noPlayerHasPlacedCard(Card $card) : self
    {
        return new self("No player has placed card $card");
    }
    
    /**
     * Not all players placed cards message
     *
     * @return GameTableException
     */
    public static function notAllPlayersPlacedCards() : self
    {
        return new self("Not all players placed cards");
    }
}
