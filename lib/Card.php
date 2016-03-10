<?php
declare(strict_types=1);

namespace Bnowak\CardGame;

use Bnowak\CardGame\Exception\CardException;

/**
 * Card
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class Card implements SuitInterface, FigureInterface
{
    /**
     * Card's suit. One of SuitInterface::SUITS
     *
     * @var string
     */
    private $suit;
    
    /**
     * Card's figure. One of FigureInterface::FIGURES
     *
     * @var string
     */
    private $figure;
    
    /**
     * Card is visible to other players
     *
     * @var bool
     */
    private $visible = false;
    
    /**
     * Constructor of card
     *
     * @param string $figure one of FigureInterface::FIGURES
     * @param string $suit one of SuitInterface::SUITS
     */
    public function __construct(string $figure, string $suit = null)
    {
        static::checkFigureIsCorrect($figure);
        if ($figure === self::FIGURE_JOKER) {
            if ($suit !== null) {
                throw CardException::jokerWithSuit($suit);
            }
        } else {
            static::checkSuitIsCorrect($suit);
        }
        
        $this->figure = $figure;
        $this->suit = $suit;
    }
    
    /**
     * Getter for the card's suit
     *
     * @return string
     */
    public function getSuit() : string
    {
        return (string) $this->suit;
    }
    
    /**
     * Getter for the card's figure
     *
     * @return string
     */
    public function getFigure() : string
    {
        return (string) $this->figure;
    }
    
    /**
     * Set if card is visible for other players
     *
     * @param bool $visible
     */
    public function setVisible(bool $visible)
    {
        $this->visible = $visible;
    }
    
    /**
     * Is card visible for all players
     *
     * @return bool
     */
    public function isVisible() : bool
    {
        return $this->visible;
    }
    
    /**
     * String representation of card in HTML-colored
     *
     * @return string
     */
    public function __toString() : string
    {
        $color = array_key_exists($this->getSuit(), SuitInterface::SUIT_COLOR)
                ? SuitInterface::SUIT_COLOR[$this->getSuit()]
                : 'black';
        return "<span style='color: $color'>{$this->figure}{$this->suit}</span>";
    }
    
    /**
     * Check if suit is correct
     *
     * @param string $suit
     * @throws CardException
     */
    public static function checkSuitIsCorrect(string $suit)
    {
        if (false === in_array($suit, SuitInterface::SUITS)) {
            throw CardException::incorrectSuit($suit);
        }
    }
    
    /**
     * Check if figure is correct
     *
     * @param string $figure
     * @throws CardException
     */
    public static function checkFigureIsCorrect(string $figure)
    {
        if (false === in_array($figure, FigureInterface::FIGURES)) {
            throw CardException::incorrectFigure($figure);
        }
    }
}
