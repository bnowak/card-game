<?php
declare(strict_types = 1);

namespace Bnowak90\CardGame\Exception;

use Bnowak90\CardGame\Card;
use Bnowak90\CardGame\Player;
use Exception;

/**
 * Exception related to errors of Player object
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class PlayerException extends Exception
{
    /**
     * These same players message
     *
     * @param Player $player
     * @return PlayerException
     */
    public static function theseSamePlayers(Player $player) : self
    {
        return new self("These players are the same '$player'");
    }
    
    /**
     * Player has already card message
     *
     * @param Player $player
     * @param Card $card
     * @return PlayerException
     */
    public static function playerHasAlreadyCard(Player $player, Card $card) : self
    {
        return new self("Player $player has already card $card");
    }
    
    /**
     * Player does not have card message
     *
     * @param Player $player
     * @param Card $card
     * @return PlayerException
     */
    public static function playerDoesNotHaveCard(Player $player, Card $card) : self
    {
        return new self("Player $player does not have card $card");
    }

    /**
     * Game is not set for player message
     *
     * @param Player $player
     * @return PlayerException
     */
    public static function gameIsNotSetForPlayer(Player $player) : self
    {
        return new self("Game is not set for player $player");
    }
    
    /**
     * Game is set for player message
     *
     * @param Player $player
     * @return PlayerException
     */
    public static function gameIsSetForPlayer(Player $player) : self
    {
        return new self("Game is already set for player $player");
    }
}
