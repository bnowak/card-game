<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Exception;

use Bnowak\CardGame\FigureInterface;
use Bnowak\CardGame\SuitInterface;
use Exception;

/**
 * Exception related to errors of Card object
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class CardException extends Exception
{
    
    /**
     * Incorrect suit message
     *
     * @param string $suit
     * @return CardException
     */
    public static function incorrectSuit(string $suit) : self
    {
        return new self(
            "Suit $suit is not correct. " .
            "Corrects are: " . implode(', ', SuitInterface::SUITS)
        );
    }
    
    /**
     * Incorrect figure message
     *
     * @param string $figure
     * @return CardException
     */
    public static function incorrectFigure(string $figure) : self
    {
        return new self(
            "Figure $figure is not correct. " .
            "Corrects are: " . implode(', ', FigureInterface::FIGURES)
        );
    }
}
