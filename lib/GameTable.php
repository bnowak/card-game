<?php
declare(strict_types = 1);

namespace Bnowak\CardGame;

use Bnowak\CardGame\Exception\GameTableException;

/**
 * Object of game table. Players can put or get cards on it or in pile.
 * The place where the game takes place.
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class GameTable
{
    /**
     * Players who participate in the game
     *
     * @var Player[]
     */
    private $players = array();
    
    /**
     * Cards placed as own by players
     *
     * @var CardCollection[]
     */
    private $cardPlayers = array();
    
    /**
     * Cards placed by players in pile
     *
     * @var CardCollection
     */
    private $cardsPile;

    /**
     * Constructor of GameTable
     *
     * @param Player[] $players
     */
    public function __construct(array $players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
        $this->cardsPile = new CardCollection();
    }
    
    /**
     * Add player into game
     *
     * @param Player $player
     */
    private function addPlayer(Player $player)
    {
        $this->players[] = $player;
        $this->cardPlayers[] = new CardCollection();
    }
    
    /**
     * Remove player from game (when player loses)
     *
     * @param Player $player
     */
    public function unsetPlayer(Player $player)
    {
        $playerKey = $this->getPlayerKey($player);
        unset($this->players[$playerKey]);
        unset($this->cardPlayers[$playerKey]);
    }
    
    /**
     * Get cards which $player placed as own in GameTable
     *
     * @param Player $player
     * @return CardCollection
     */
    public function getPlayerCards(Player $player) : CardCollection
    {
        return $this->cardPlayers[$this->getPlayerKey($player)];
    }
    
    /**
     * Get cards whick players placed in pile
     *
     * @return CardCollection
     */
    public function getCardsPile() : CardCollection
    {
        return $this->cardsPile;
    }
    
    /**
     * Count cards which player placed on as own GameTable
     *
     * @param Player $player
     * @return int
     */
    public function countSituatedCardPlayer(Player $player) : int
    {
        return $this->getPlayerCards($player)->count();
    }
    
    /**
     * Count cards which all players placed as own on GameTable
     *
     * @return int
     */
    public function countCardsSituatedByAllPlayers() : int
    {
        $cardsSituatedCount = 0;
        foreach ($this->getPlayers() as $player) {
            $cardsSituatedCount += $this->countSituatedCardPlayer($player);
        }
        
        return $cardsSituatedCount;
    }
    
    /**
     * Count cards which all players placed in pile
     *
     * @return int
     */
    public function countCardsSituatedInPile() : int
    {
        return $this->cardsPile->count();
    }
    
    /**
     * Count all cards which are situated on GameTable (as players own and in pile)
     *
     * @return int
     */
    public function countAllCardsSituated() : int
    {
        return $this->countCardsSituatedByAllPlayers() + $this->countCardsSituatedInPile();
    }
    
    /**
     * Is any cards has situated as own by $player
     *
     * @param Player $player
     * @return bool
     */
    public function hasPlayerCardsSituated(Player $player) : bool
    {
        return $this->countSituatedCardPlayer($player) > 0;
    }
    
    /**
     * Is every player has situated any cards as own
     *
     * @return bool
     */
    public function hasEveryPlayerCardsSituated() : bool
    {
        foreach ($this->players as $player) {
            if (false === $this->hasPlayerCardsSituated($player)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Is every player situated these same cards count as own
     *
     * @return bool
     */
    public function hasEveryPlayerSameCardsSituatedCount() : bool
    {
        $cardsSituatedCount = array();
        foreach ($this->players as $player) {
            $cardsSituatedCount[] = $this->countSituatedCardPlayer($player);
        }
        
        return count(array_unique($cardsSituatedCount, SORT_NUMERIC)) === 1;
    }
    
    /**
     * Is $card situated by $player as own
     *
     * @param Card $card
     * @param Player $player
     * @return bool
     */
    public function isCardSituatedByPlayer(Card $card, Player $player) : bool
    {
        return $this->getPlayerCards($player)->has($card);
    }
    
    /**
     * Is $card situated by any player as own
     *
     * @param Card $card
     * @return bool
     */
    public function isCardSituatedByAnyPlayer(Card $card) : bool
    {
        foreach ($this->players as $player) {
            if ($this->isCardSituatedByPlayer($card, $player)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Is $card situated in pile
     *
     * @param Card $card
     * @return bool
     */
    public function isCardSituatedInPile(Card $card) : bool
    {
        return $this->getCardsPile()->has($card);
    }
    
    /**
     * Get player who situated $card as own
     *
     * @param Card $card
     * @return Player
     */
    public function getPlayerWhoSituatedCard(Card $card) : Player
    {
        $this->checkIsSituatedCard($card);
        
        foreach ($this->players as $player) {
            if ($this->isCardSituatedByPlayer($card, $player)) {
                return $player;
            }
        }
    }
    
    /**
     * Check if any player situated $card as own
     *
     * @param Card $card
     * @throws GameTableException
     */
    public function checkIsSituatedCard(Card $card)
    {
        if (false === $this->isCardSituatedByAnyPlayer($card)) {
            throw GameTableException::noPlayerHasPlacedCard($card);
        }
    }
    
    /**
     * Check if every players situated cards as own
     *
     * @throws GameTableException
     */
    public function checkEveryPlayersHasSituatedCards()
    {
        if (false === $this->hasEveryPlayerCardsSituated()) {
            throw GameTableException::notAllPlayersPlacedCards();
        }
    }
    
    /**
     * Get internally player GameTable's key
     *
     * @param Player $player
     * @return int
     */
    private function getPlayerKey(Player $player) : int
    {
        return array_search($player, $this->players, true);
    }
}
