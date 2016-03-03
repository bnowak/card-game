<?php
declare(strict_types = 1);

namespace Bnowak90\CardGame\Exception;

use Bnowak90\CardGame\Card;
use Exception;

/**
 * Exception related to errors of CardCollection object
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class CardCollectionException extends Exception
{
    
    /**
     * Empty collection exception message
     *
     * @return CardCollectionException
     */
    public static function emptyCollection() : self
    {
        return new self("Card collection is empty");
    }
    
    /**
     * No card in collection message
     *
     * @param Card $card
     * @return CardCollectionException
     */
    public static function noCardInCollection(Card $card) : self
    {
        return new self("$card card is not in the collection");
    }
}
