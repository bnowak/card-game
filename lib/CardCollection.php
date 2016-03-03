<?php
declare(strict_types = 1);

namespace Bnowak90\CardGame;

use ArrayIterator;
use Bnowak90\CardGame\Exception\CardCollectionException;
use Countable;
use IteratorAggregate;

/**
 * CardCollection
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class CardCollection implements IteratorAggregate, Countable
{

    /**
     * Cards array
     *
     * @var Card[]
     */
    private $cards = array();

    /**
     * Constructor of CardCollection
     *
     * @param Card[] $cards
     */
    public function __construct(array $cards = array())
    {
        $this->appendMany($cards);
    }
    
    /**
     * Append card on end of collection
     *
     * @param Card $card
     */
    public function append(Card $card)
    {
        array_push($this->cards, $card);
    }
    
    /**
     * Prepend card on begin of collection
     *
     * @param Card $card
     */
    public function prepend(Card $card)
    {
        array_unshift($this->cards, $card);
    }
    
    /**
     * Append many cards on end of collection
     *
     * @param Card[] $cards
     */
    public function appendMany(array $cards)
    {
        foreach ($cards as $card) {
            $this->append($card);
        }
    }
    
    /**
     * Prepend many cards on begin of collection
     *
     * @param Card[] $cards
     */
    public function prependMany(array $cards)
    {
        foreach ($cards as $card) {
            $this->prepend($card);
        }
    }

    /**
     * Is card in collection
     *
     * @param Card $card
     * @return bool
     */
    public function has(Card $card): bool
    {
        return in_array($card, $this->cards, true);
    }

    /**
     * Collect card and remove it from collection
     *
     * @param Card $card
     * @return Card
     */
    public function collect(Card $card): Card
    {
        $this->checkIsCardInCollection($card);
        $key = array_search($card, $this->cards, true);
        unset($this->cards[$key]);
        $this->cards = array_values($this->cards);
        
        return $card;
    }
    
    /**
     * Collect all cards and remove them from collection
     *
     * @return array
     */
    public function collectAll() : array
    {
        $returnCards = array();
        foreach ($this->cards as $card) {
            $returnCards[] = $this->collect($card);
        }
        
        return $returnCards;
    }
    
    /**
     * Get all cards from collection
     *
     * @return Card[]
     */
    public function getAll() : array
    {
        return $this->cards;
    }
    
    /**
     * Collect first card from collection (at bottom of pile) and remove if from collection
     *
     * @return Card
     */
    public function collectFirst() : Card
    {
        $this->checkIsNotEmptyCollection();
        
        return array_shift($this->cards);
    }
    
    /**
     * Get first card from collection (at bottom of pile)
     *
     * @return Card
     */
    public function getFirst() : Card
    {
        $this->checkIsNotEmptyCollection();
        
        return reset($this->cards);
    }
    
    /**
     * Collect last card from collection (at top of pile) and remove if from collection
     *
     * @return Card
     */
    public function collectLast() : Card
    {
        $this->checkIsNotEmptyCollection();
        
        return array_pop($this->cards);
    }
    
    /**
     * Get last card from collection (at top of pile)
     *
     * @return Card
     */
    public function getLast() : Card
    {
        $this->checkIsNotEmptyCollection();
        
        return end($this->cards);
    }

    /**
     * Is card with $suit in collection
     *
     * @param string $suit
     * @return bool
     */
    public function hasCardWithSuit(string $suit) : bool
    {
        Card::checkSuitIsCorrect($suit);
        
        foreach ($this->cards as $card) {
            if ($card->getSuit() === $suit) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Is card with $figure in collection
     *
     * @param string $figure
     * @return bool
     */
    public function hasCardWithFigure(string $figure) : bool
    {
        Card::checkFigureIsCorrect($figure);
        
        foreach ($this->cards as $card) {
            if ($card->getFigure() === $figure) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Set visibility to other players for all cards in collection
     *
     * @param bool $visible
     */
    public function setAllVisible(bool $visible)
    {
        foreach ($this->cards as $card) {
            $card->setVisible($visible);
        }
    }

    /**
     * Clear card collection
     */
    public function clear()
    {
        $this->cards = array();
    }

    /**
     * Shuffle card collection
     */
    public function shuffle()
    {
        shuffle($this->cards);
    }

    /**
     * Get count of cards in collection
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->cards);
    }

    /**
     * Implementation of IteratorAggregate interface
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->cards);
    }
    
    /**
     * Check if collection is not empty
     *
     * @throws CardCollectionException
     */
    public function checkIsNotEmptyCollection()
    {
        if ($this->count() === 0) {
            throw CardCollectionException::emptyCollection();
        }
    }
    
    /**
     * Check if card is in collection
     *
     * @param Card $card
     * @throws CardCollectionException
     */
    public function checkIsCardInCollection(Card $card)
    {
        if (false === $this->has($card)) {
            throw CardCollectionException::noCardInCollection($card);
        }
    }
}
