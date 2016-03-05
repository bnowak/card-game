<?php
declare(strict_types = 1);

namespace Bnowak\CardGame;

/**
 * Interface of suits cards
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
interface SuitInterface
{
    const SUIT_HEART = '♥';
    const SUIT_DIAMOND = '♦';
    const SUIT_CLUB = '♣';
    const SUIT_SPADE = '♠';
    
    const SUITS = array(
        self::SUIT_HEART,
        self::SUIT_DIAMOND,
        self::SUIT_CLUB,
        self::SUIT_SPADE,
    );
    
    const SUIT_COLOR = array(
        self::SUIT_HEART => 'red',
        self::SUIT_DIAMOND => 'red',
        self::SUIT_CLUB => 'black',
        self::SUIT_SPADE => 'black',
    );
}
