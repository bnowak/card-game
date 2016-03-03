<?php
declare(strict_types = 1);

namespace Bnowak90\CardGame\Games;

use Bnowak90\CardGame\Card;
use Bnowak90\CardGame\CardCollection;
use Bnowak90\CardGame\GameTable;
use Bnowak90\CardGame\Exception\AbstractGameException;
use Bnowak90\CardGame\GameResultInterface;
use Bnowak90\CardGame\Player;
use Bnowak90\CardGame\RoundResultInterface;

/**
 * Abstract of Game in cards. Every implemented games must inherit from this class.
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
abstract class AbstractGame
{
    /**
     * Card collection from which game is started
     *
     * @var CardCollection
     */
    protected $deck;

    /**
     * Object where player can put cards
     *
     * @var GameTable
     */
    protected $gameTable;
    
    /**
     * Players participating in game
     *
     * @var Player[]
     */
    private $players = array();
    
    /**
     * Players who lost game
     *
     * @var Player[]
     */
    private $losePlayers = array();
    
    /**
     * Which cards $player can put as own in implemented game
     *
     * @return CardCollection
     */
    abstract public function availableCardsToPutByPlayer(Player $player) : CardCollection;
    
    /**
     * Which cards $player can put in pile in implemented game
     *
     * @return CardCollection
     */
    abstract public function availableCardsToPutInPileByPlayer(Player $player): CardCollection;
    
    /**
     * Which cards $byPlayer can give to $toPlayer in implemented game
     *
     * @return CardCollection
     */
    abstract public function availableCardsToGiveByPlayerToPlayer(Player $byPlayer, Player $toPlayer): CardCollection;
    
    /**
     * Array of players count which implemented game can handle
     *
     * @return int[]
     */
    abstract public static function getAllowedPlayersCount() : array;

    /**
     * Builder of card collection from which implemented game is started
     *
     * @return CardCollection
     */
    abstract public static function buildDeck(): CardCollection;

    /**
     * Handler of game start (like handing out cards to players)
     */
    abstract protected function startGame();
    
    /**
     * Handler of game end
     *
     * @return GameResultInterface
     */
    abstract protected function endGame() : GameResultInterface;
    
    /**
     * Handler of round start
     */
    abstract protected function startRound();
    
    /**
     * Handler of round end
     *
     * @return RoundResultInterface
     */
    abstract protected function endRound() : RoundResultInterface;

    /**
     * Constructor of any implemented games
     *
     * @param Player[] $players
     */
    final public function __construct(array $players)
    {
        foreach ($players as $player) {
            $this->addPlayer($player);
        }
        
        $this->checkPlayerCount();
        $this->gameTable = new GameTable($this->players);
        $this->deck = static::buildDeck();
        $this->startGame();
    }
    
    /**
     * Add player to implemented game
     *
     * @param Player $player
     */
    private function addPlayer(Player $player)
    {
        $this->players[] = $player;
        $player->setGame($this);
        $player->getCards()->clear();
    }

    /**
     * Get players who participating in implemented game
     *
     * @return Player[]
     */
    final public function getPlayers(): array
    {
        return $this->players;
    }
    
    /**
     * Get internal player's key
     *
     * @param Player $player
     * @return int
     */
    final protected function getPlayerKey(Player $player) : int
    {
        return array_search($player, $this->players, true);
    }
    
    /**
     * Get players who lost game
     *
     * @return Player[]
     */
    final public function getLosePlayers() : array
    {
        return $this->losePlayers;
    }
    
    /**
     * Is $player has any available cards to put as own
     *
     * @param Player $player
     * @return bool
     */
    final public function hasAvailableCardsToPutByPlayer(Player $player) : bool
    {
        return $this->availableCardsToPutByPlayer($player)->count() > 0;
    }
    
    /**
     * Is $player has any available cards to put in pile
     *
     * @param Player $player
     * @return bool
     */
    final public function hasAvailableCardsToPutInPileByPlayer(Player $player): bool
    {
        return $this->availableCardsToPutInPileByPlayer($player)->count() > 0;
    }
    
    /**
     * Is $byPlayer has any available cards to give to $toPlayer
     *
     * @param Player $byPlayer
     * @param Player $toPlayer
     * @return bool
     */
    final public function hasAvailableCardsToGiveByPlayerToPlayer(Player $byPlayer, Player $toPlayer): bool
    {
        return $this->availableCardsToGiveByPlayerToPlayer($byPlayer, $toPlayer)->count() > 0;
    }
    
    /**
     * Is $byPlayer has available any movement
     *
     * @param Player $byPlayer
     * @return bool
     */
    final public function hasAvailableMoveByPlayer(Player $byPlayer) : bool
    {
        switch (true) {
            case $this->hasAvailableCardsToPutByPlayer($byPlayer):
            case $this->hasAvailableCardsToPutInPileByPlayer($byPlayer):
                return true;
        }
        
        foreach ($this->players as $toPlayer) {
            if ($byPlayer !== $toPlayer && $this->hasAvailableCardsToGiveByPlayerToPlayer($byPlayer, $toPlayer)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * End and start round in implemented game
     */
    final protected function endAndStartRound()
    {
        $this->endRound();
        $this->startRound();
    }
    
    /**
     * Putting of $card by $player as own
     * Implemented games can override this metod by should call it for this action
     *
     * @param Card $card
     * @param Player $player
     */
    public function putCardByPlayer(Card $card, Player $player)
    {
        $player->checkPlayerHasCard($card);
        $this->checkPlayerCanPutCard($player, $card);
        
        $playerCard = $player->getCards()->collect($card);
        $this->gameTable->getPlayerCards($player)->append($playerCard);
    }
    
    /**
     * Putting of $card by $player in pile
     * Implemented games can override this metod by should call it for this action
     *
     * @param Card $card
     * @param Player $player
     */
    public function putCardInPileByPlayer(Card $card, Player $player)
    {
        $player->checkPlayerHasCard($card);
        $this->checkPlayerCanPutCardInPile($player, $card);
        
        $playerCard = $player->getCards()->collect($card);
        $this->gameTable->getCardsPile()->append($playerCard);
    }
    
    /**
     * Giveing $card from $byPlayer to $toPlayer
     * Implemented games can override this metod by should call it for this action
     *
     * @param Card $card
     * @param Player $byPlayer
     * @param Player $toPlayer
     */
    public function giveCardByPlayerToPlayer(Card $card, Player $byPlayer, Player $toPlayer)
    {
        $byPlayer->checkPlayerHasCard($card);
        $toPlayer->checkPlayerHasNotCard($card);
        Player::checkIfAreDiffrentPlayers($byPlayer, $toPlayer);
        $this->checkPlayerCanGiveCardToPlayer($byPlayer, $toPlayer, $card);
        
        $byPlayerCard = $byPlayer->getCards()->collect($card);
        $toPlayer->getCards()->append($byPlayerCard);
    }
    
    /**
     * Handling of player loses
     *
     * @param Player $player
     */
    protected function losePlayer(Player $player)
    {
        $this->losePlayers[] = $player;
        $playerKey = array_search($player, $this->players, true);
        unset($this->players[$playerKey]);
        $this->gameTable->unsetPlayer($player);
    }
    
    /**
     * Is only one player has left in game
     *
     * @return bool
     */
    protected function isOnePlayerIsLeft() : bool
    {
        return count($this->players) === 1;
    }
    
    /**
     * Check if player count is allowed in implemented game
     *
     * @throws AbstractGameException
     */
    public function checkPlayerCount()
    {
        $currentCount = count($this->players);
        $allowedCount = static::getAllowedPlayersCount();
        if (false === in_array($currentCount, $allowedCount)) {
            throw AbstractGameException::wrongNumberOfPlayers($currentCount, $allowedCount);
        }
    }
    
    /**
     * Check if $player can put $card as own
     *
     * @param Player $player
     * @param Card $card
     * @throws AbstractGameException
     */
    public function checkPlayerCanPutCard(Player $player, Card $card)
    {
        if (false === $this->availableCardsToPutByPlayer($player)->has($card)) {
            throw AbstractGameException::playerCanNotPutCard($player, $card);
        }
    }
    
    /**
     * Check if $player can put $card in pile
     *
     * @param Player $player
     * @param Card $card
     * @throws AbstractGameException
     */
    public function checkPlayerCanPutCardInPile(Player $player, Card $card)
    {
        if (false === $this->availableCardsToPutInPileByPlayer($player)->has($card)) {
            throw AbstractGameException::playerCanNotPutCardInPile($player, $card);
        }
    }
    
    /**
     * Check if $byPlayer can give $card to $toPlayer
     *
     * @param Player $byPlayer
     * @param Player $toPlayer
     * @param Card $card
     * @throws AbstractGameException
     */
    public function checkPlayerCanGiveCardToPlayer(Player $byPlayer, Player $toPlayer, Card $card)
    {
        if (false === $this->availableCardsToGiveByPlayerToPlayer($byPlayer, $toPlayer)->has($card)) {
            throw AbstractGameException::playerCanNotGiveCardToPlayer($byPlayer, $toPlayer, $card);
        }
    }
}
