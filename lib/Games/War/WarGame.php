<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Games\War;

use Bnowak\CardGame\Card;
use Bnowak\CardGame\CardCollection;
use Bnowak\CardGame\FigureInterface;
use Bnowak\CardGame\GameResultInterface;
use Bnowak\CardGame\Games\AbstractGame;
use Bnowak\CardGame\Player;
use Bnowak\CardGame\RoundResultInterface;
use Bnowak\CardGame\SuitInterface;

/**
 * War game implementation
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class WarGame extends AbstractGame
{
    /**
     * Order of cards in WarGame from lowest figure to highest figure rank
     */
    const ORDER_OF_FIGURES = [
        FigureInterface::FIGURE_2,
        FigureInterface::FIGURE_3,
        FigureInterface::FIGURE_4,
        FigureInterface::FIGURE_5,
        FigureInterface::FIGURE_6,
        FigureInterface::FIGURE_7,
        FigureInterface::FIGURE_8,
        FigureInterface::FIGURE_9,
        FigureInterface::FIGURE_10,
        FigureInterface::FIGURE_JACK,
        FigureInterface::FIGURE_QUEEN,
        FigureInterface::FIGURE_KING,
        FigureInterface::FIGURE_ACE,
    ];

    /**
     * Table of player movement count. The keys are players internal keys in game.
     *
     * @var int[]
     */
    private $playersMovementCount = array();
    
    /**
     * Handing out cards to players and start round
     */
    protected function startGame()
    {
        $players = $this->getPlayers();
        $firstPlayer = reset($players);
        $player = reset($players);
        while (count($this->deck) > count($players)
            || (count($this->deck) <= count($players) && $player !== $firstPlayer)
        ) {
            $player->getCards()->append($this->deck->collectFirst());
            $player = next($players) ? current($players) : reset($players);
        }
        
        $this->startRound();
    }
    
    /**
     * Handler of game end
     *
     * @return GameResult
     */
    protected function endGame() : GameResultInterface
    {
        return new GameResult();
    }
    
    /**
     * Setting the initial count of movement of players on round start
     */
    protected function startRound()
    {
        $this->setMovementCountForAllPlayers(1);
    }
    
    /**
     * Handling of round end and losers players
     *
     * @return RoundResult
     */
    protected function endRound() : RoundResultInterface
    {
        if ($this->isOneHighestCardPutted()) {
            $card = $this->getOneHighestCardPutted();
            $winPlayer = $this->gameTable->getPlayerWhoSituatedCard($card);

            $this->collectCardsByWinnerPlayer($winPlayer);
        }
        
        if ($this->isAnyPlayerLoser()) {
            $this->handleLosePlayers();
        }
        
        if ($this->isOnePlayerIsLeft()) {
            $this->endGame();
        }
        
        $this->setMovementCountForAllPlayers(0);
        
        return new RoundResult();
    }
    
    /**
     * Builder of card collection in WarGame
     * Allowed all 4 suits and figures from 2 to A
     *
     * @return CardCollection
     */
    public static function buildDeck(): CardCollection
    {
        $deck = new CardCollection();
        foreach (SuitInterface::SUITS as $suit) {
            foreach (self::ORDER_OF_FIGURES as $figure) {
                $deck->append(new Card($figure, $suit));
            }
        }
        $deck->shuffle();

        return $deck;
    }

    /**
     * Handling of putting $card by $player in WarGame
     *
     * @param Card $card
     * @param Player $player
     */
    public function putCardByPlayer(Card $card, Player $player)
    {
        parent::putCardByPlayer($card, $player);
        
        $playerCardsSituatedCount = $this->gameTable->countSituatedCardPlayer($player);
        if (($playerCardsSituatedCount % 2) === 0) {
            $card->setVisible(false);
        } else {
            $card->setVisible(true);
        }
        
        $cardsCandPutByPlayerCount = $this->getMovementCountForPlayer($player);
        $this->setMovementCountForPlayer($player, --$cardsCandPutByPlayerCount);
        
        if (false === $this->isPlayerWhoHasMovement()) {
            if ($this->isWarFromLastSituatedCards()) {
                $this->addHowManyMovementsCanDoPlayersOnWar();
                if ($this->isAnyPlayerLoser()) {
                    $this->endAndStartRound();
                }
            } elseif ($this->isOneHighestCardPutted()) {
                $this->endAndStartRound();
            }
        }
    }
    
    /**
     * Unsupported move in WarGame
     *
     * @param Card $card
     * @param Player $player
     * @throws WarGameException
     */
    public function putCardInPileByPlayer(Card $card, Player $player)
    {
        throw WarGameException::unsupportedMove(__METHOD__);
    }
    
    /**
     * Unsupported move in WarGame
     *
     * @param Card $card
     * @param Player $byPlayer
     * @param Player $toPlayer
     * @throws WarGameException
     */
    public function giveCardByPlayerToPlayer(Card $card, Player $byPlayer, Player $toPlayer)
    {
        throw WarGameException::unsupportedMove(__METHOD__);
    }
    
    /**
     * In WarGame $player can put only last (at top of player's cards pile) card if has movement
     *
     * @param Player $player
     * @return CardCollection
     */
    public function availableCardsToPutByPlayer(Player $player) : CardCollection
    {
        $availableCards = new CardCollection();
        
        if ($this->canPlayerPutNextCard($player)) {
            $card = $player->getCards()->getLast();
            $availableCards->append($card);
        }

        return $availableCards;
    }

    /**
     * Unsupported move in WarGame
     *
     * @param Player $player
     * @return CardCollection
     */
    public function availableCardsToPutInPileByPlayer(Player $player) : CardCollection
    {
        return new CardCollection();
    }
    
    /**
     * Unsupported move in WarGame
     *
     * @param Player $byPlayer
     * @param Player $toPlayer
     * @return CardCollection
     */
    public function availableCardsToGiveByPlayerToPlayer(Player $byPlayer, Player $toPlayer) : CardCollection
    {
        return new CardCollection();
    }
    
    /**
     * Is $player has movement
     *
     * @param Player $player
     * @return bool
     */
    public function canPlayerPutNextCard(Player $player) : bool
    {
        return $this->getMovementCountForPlayer($player) > 0;
    }
    
    /**
     * Handling of loseinf $player
     *
     * @param Player $player
     */
    protected function losePlayer(Player $player)
    {
        unset($this->playersMovementCount[$this->getPlayerKey($player)]);
        parent::losePlayer($player);
    }
    
    /**
     * Condition when $player is loser
     *
     * @param Player $player
     * @return bool
     */
    protected function isPlayerLoser(Player $player) : bool
    {
        return $this->getMovementCountForPlayer($player) > $player->getCards()->count()
            || $player->getCards()->count() === 0;
    }
    
    /**
     * Is any player loser
     *
     * @return bool
     */
    protected function isAnyPlayerLoser() : bool
    {
        foreach ($this->getPlayers() as $player) {
            if ($this->isPlayerLoser($player)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Handling of losers player in WarGame
     *
     * @throws WarGameException
     */
    protected function handleLosePlayers()
    {
        if (false === $this->isAnyPlayerLoser()) {
            throw WarGameException::noPlayerIsLoser();
        }
        
        foreach ($this->getPlayers() as $player) {
            if ($this->isPlayerLoser($player)) {
                $this->losePlayer($player);
            }
        }
        
        foreach ($this->getPlayers() as $player) {
            if ($this->gameTable->hasPlayerCardsSituated($player)) {
                $this->gameTable->getPlayerCards($player)->setAllVisible(false);
                $player->getCards()->prependMany($this->gameTable->getPlayerCards($player)->collectAll());
            }
        }
    }

    /**
     * When $winPlayer win round, it is collect all cards which are situated on GameTable
     *
     * @param Player $winPlayer
     */
    protected function collectCardsByWinnerPlayer(Player $winPlayer)
    {
        $winCardsCollection = new CardCollection();
        
        foreach ($this->getPlayers() as $player) {
            $winCardsCollection->appendMany($this->gameTable->getPlayerCards($player)->collectAll());
        }
        
        $winCardsCollection->setAllVisible(false);
        $winCardsCollection->shuffle();
        
        $winPlayer->getCards()->prependMany($winCardsCollection->collectAll());
    }


    /**
     * Is only one card in situated by players with highest-rank figure
     *
     * @return bool
     */
    protected function isOneHighestCardPutted() : bool
    {
        $puttedFigures = $this->getLastSituatedFigures();
        $countedPuttedFigures = array_count_values($puttedFigures);
        
        foreach (array_reverse(self::ORDER_OF_FIGURES) as $orderedFigure) {
            $isFigureExist = array_key_exists($orderedFigure, $countedPuttedFigures);
            if ($isFigureExist && $countedPuttedFigures[$orderedFigure] === 1) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get ont highest-rank card which is situated by players
     *
     * @return Card
     */
    protected function getOneHighestCardPutted() : Card
    {
        $this->gameTable->checkEveryPlayersHasSituatedCards();
        $this->checkIsOneHighestCardSituated();
        
        $lastPuttedCardsByPlayers = new CardCollection();
        foreach ($this->getPlayers() as $player) {
            if ($this->gameTable->getPlayerCards($player)->count() > 0) {
                $lastPuttedCardsByPlayers->append($this->gameTable->getPlayerCards($player)->getLast());
            }
        }
        
        foreach (array_reverse(self::ORDER_OF_FIGURES) as $orderedFigure) {
            if ($lastPuttedCardsByPlayers->hasCardWithFigure($orderedFigure)) {
                foreach ($lastPuttedCardsByPlayers as $card) {
                    if ($card->getFigure() === $orderedFigure) {
                        return $card;
                    }
                }
            }
        }
    }
    
    /**
     * Handling of players movements count on war
     *
     * @throws WarGameException
     */
    protected function addHowManyMovementsCanDoPlayersOnWar()
    {
        $this->gameTable->checkEveryPlayersHasSituatedCards();
        if ($this->isPlayerWhoHasMovement()) {
            throw WarGameException::isPlayerWhoHasMove();
        }
        
        $lastPuttedFigures = $this->getLastSituatedFigures();
        $countedPuttedFigures = array_count_values($lastPuttedFigures);
        
        foreach ($this->getPlayers() as $player) {
            $lastPuttedCardByPlayer = $this->gameTable->getPlayerCards($player)->getLast();
            if ($countedPuttedFigures[$lastPuttedCardByPlayer->getFigure()] > 1) {
                $this->setMovementCountForPlayer($player, 2);
            }
        }
    }

    /**
     * Is it war from last situated cards by players
     *
     * @return bool
     */
    protected function isWarFromLastSituatedCards() : bool
    {
        $puttedFigures = $this->getLastSituatedFigures();
        
        return count(array_unique($puttedFigures)) !== count($puttedFigures);
    }
    
    /**
     * Get array of figures from last situated cards by players
     *
     * @return array
     */
    protected function getLastSituatedFigures() : array
    {
        $puttedFigures = array();
        foreach ($this->getPlayers() as $player) {
            $puttedFigures[] = $this->gameTable->getPlayerCards($player)->getLast()->getFigure();
        }
        
        return $puttedFigures;
    }
    
    /**
     * Get movement count for $player
     *
     * @param Player $player
     * @return int
     */
    protected function getMovementCountForPlayer(Player $player) : int
    {
        return $this->playersMovementCount[$this->getPlayerKey($player)];
    }
    
    /**
     * Set movement $count for $player
     *
     * @param Player $player
     * @param int $count
     */
    protected function setMovementCountForPlayer(Player $player, int $count)
    {
        static::checkCountOfMovements($count);
        
        $this->playersMovementCount[$this->getPlayerKey($player)] = $count;
    }
    
    /**
     * Set movement $count for all players
     *
     * @param int $count
     */
    protected function setMovementCountForAllPlayers(int $count)
    {
        static::checkCountOfMovements($count);
        
        $this->playersMovementCount = array();
        foreach (array_keys($this->getPlayers()) as $playerKey) {
            $this->playersMovementCount[$playerKey] = $count;
        }
    }
    
    /**
     * Is player who has movement
     *
     * @return bool
     */
    protected function isPlayerWhoHasMovement() : bool
    {
        foreach ($this->getPlayers() as $player) {
            if ($this->getMovementCountForPlayer($player) > 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if only one highest-rank card is situated
     *
     * @throws WarGameException
     */
    public function checkIsOneHighestCardSituated()
    {
        if (false === $this->isOneHighestCardPutted()) {
            throw WarGameException::moreThanOneHighestRankCard();
        }
    }
    
    /**
     * Check if $count of movements are correct
     *
     * @param int $count
     * @throws WarGameException
     */
    public static function checkCountOfMovements(int $count)
    {
        if ($count < 0) {
            throw WarGameException::wrongNegativeNumberOfMovements($count);
        }
    }
    
    /**
     * In WarGame can play 2 or 3 players
     *
     * @return int[]
     */
    public static function getAllowedPlayersCount() : array
    {
        return [2, 3];
    }
}
