<?php
declare(strict_types = 1);

namespace Bnowak90\CardGame;

use Bnowak90\CardGame\Exception\PlayerException;
use Bnowak90\CardGame\Games\AbstractGame;

/**
 * Player of game
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class Player
{
    
    /**
     * Player's name
     *
     * @var string
     */
    private $name;
    
    /**
     * Game instance
     *
     * @var AbstractGame
     */
    private $game;
    
    /**
     * Cards which player has
     *
     * @var CardCollection
     */
    private $cards;

    /**
     * Constructor of player
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->cards = new CardCollection();
    }
    
    /**
     * Getter of player's name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * Setter of game instance
     *
     * @param AbstractGame $game
     */
    public function setGame(AbstractGame $game)
    {
        $this->checkIsGameNotSetForPlayer();
        $this->game = $game;
    }
    
    /**
     * Getter of player's cards
     *
     * @return CardCollection
     */
    public function getCards() : CardCollection
    {
        return $this->cards;
    }
    
    /**
     * Put card by player on GameTable
     *
     * @param Card $card
     */
    public function putCard(Card $card)
    {
        $this->checkIsGameSetForPlayer();
        $this->game->putCardByPlayer($card, $this);
    }
    
    /**
     * Put card by player on GameTable in pile
     *
     * @param Card $card
     */
    public function putCardInPile(Card $card)
    {
        $this->checkIsGameSetForPlayer();
        $this->game->putCardInPileByPlayer($card, $this);
    }
    
    /**
     * Give card by player to $player
     *
     * @param Card $card
     * @param Player $player
     */
    public function giveCardToPlayer(Card $card, Player $player)
    {
        $this->checkIsGameSetForPlayer();
        $this->game->giveCardByPlayerToPlayer($card, $this, $player);
    }
    
    /**
     * Which cards player can put on GameTable
     *
     * @return CardCollection
     */
    public function availableCardsToPut() : CardCollection
    {
        $this->checkIsGameSetForPlayer();
        return $this->game->availableCardsToPutByPlayer($this);
    }
    
    /**
     * Which cards player can put on GameTable in pile
     *
     * @return CardCollection
     */
    public function availableCardsToPutInPile() : CardCollection
    {
        $this->checkIsGameSetForPlayer();
        return $this->game->availableCardsToPutInPileByPlayer($this);
    }
    
    /**
     * Which cards player can give to $player
     *
     * @param Player $player
     * @return CardCollection
     */
    public function availableCardsToGiveToPlayer(Player $player) : CardCollection
    {
        $this->checkIsGameSetForPlayer();
        return $this->game->availableCardsToGiveByPlayerToPlayer($this, $player);
    }
    
    /**
     * String representation of player
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->getName();
    }
    
    /**
     * Check if player has not $card
     *
     * @param Card $card
     * @throws PlayerException
     */
    public function checkPlayerHasNotCard(Card $card)
    {
        if ($this->getCards()->has($card)) {
            throw PlayerException::playerHasAlreadyCard($this, $card);
        }
    }
    
    /**
     * Check if player has $card
     *
     * @param Card $card
     * @throws PlayerException
     */
    public function checkPlayerHasCard(Card $card)
    {
        if (false === $this->getCards()->has($card)) {
            throw PlayerException::playerDoesNotHaveCard($this, $card);
        }
    }
    
    /**
     * Check if player has set game instance
     *
     * @throws PlayerException
     */
    public function checkIsGameSetForPlayer()
    {
        if (false === ($this->game instanceof AbstractGame)) {
            throw PlayerException::gameIsNotSetForPlayer($this);
        }
    }
    
    /**
     * Check if player has not set game instance
     *
     * @throws PlayerException
     */
    public function checkIsGameNotSetForPlayer()
    {
        if ($this->game instanceof AbstractGame) {
            throw PlayerException::gameIsSetForPlayer($this);
        }
    }
    
    /**
     * Check if two players are diffrent
     *
     * @param Player $player1
     * @param Player $player2
     * @throws PlayerException
     */
    public static function checkIfAreDiffrentPlayers(Player $player1, Player $player2)
    {
        if ($player1 === $player2) {
            throw PlayerException::theseSamePlayers($player1);
        }
    }
}
