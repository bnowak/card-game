<?php
declare(strict_types = 1);

namespace Bnowak\CardGame;

/**
 * Interface of figure cards
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
interface FigureInterface
{
    const FIGURE_2 = '2';
    const FIGURE_3 = '3';
    const FIGURE_4 = '4';
    const FIGURE_5 = '5';
    const FIGURE_6 = '6';
    const FIGURE_7 = '7';
    const FIGURE_8 = '8';
    const FIGURE_9 = '9';
    const FIGURE_10 = '10';
    const FIGURE_JACK = 'J';
    const FIGURE_QUEEN = 'Q';
    const FIGURE_KING = 'K';
    const FIGURE_ACE = 'A';
    const FIGURE_JOKER = '★';
    
    const FIGURES = array(
        self::FIGURE_2,
        self::FIGURE_3,
        self::FIGURE_4,
        self::FIGURE_5,
        self::FIGURE_6,
        self::FIGURE_7,
        self::FIGURE_8,
        self::FIGURE_9,
        self::FIGURE_10,
        self::FIGURE_JACK,
        self::FIGURE_QUEEN,
        self::FIGURE_KING,
        self::FIGURE_ACE,
        self::FIGURE_JOKER,
    );
}
