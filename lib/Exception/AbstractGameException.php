<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Exception;

use Bnowak\CardGame\Card;
use Bnowak\CardGame\Player;
use Exception;

/**
 * Exception related to errors of AbstractGame object
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class AbstractGameException extends Exception
{

    /**
     * Wrong number of players message
     *
     * @param int $currentNumber
     * @param int[] $allowedNumbers
     * @return AbstractGameException
     */
    public static function wrongNumberOfPlayers(int $currentNumber, array $allowedNumbers) : self
    {
        return new self(
            "Number $currentNumber of players is not correct. " .
            "Corrects numbers are ".  implode(', ', $allowedNumbers)
        );
    }
    
    /**
     * Player can not put card message
     *
     * @param Player $player
     * @param Card $card
     * @return AbstractGameException
     */
    public static function playerCanNotPutCard(Player $player, Card $card) : self
    {
        return new self("Player $player can not put card $card");
    }
    
    /**
     * Player can not put card in pile message
     *
     * @param Player $player
     * @param Card $card
     * @return AbstractGameException
     */
    public static function playerCanNotPutCardInPile(Player $player, Card $card) : self
    {
        return new self("Player $player can not put card $card in pile");
    }
    
    /**
     * Player can not give card to player message
     *
     * @param Player $byPlayer
     * @param Player $toPlayer
     * @param Card $card
     * @return AbstractGameException
     */
    public static function playerCanNotGiveCardToPlayer(Player $byPlayer, Player $toPlayer, Card $card) : self
    {
        return new self("Player $byPlayer can not give card $card to player $toPlayer");
    }
}
