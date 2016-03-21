<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Tests;

use Bnowak\CardGame\Card;
use Bnowak\CardGame\FigureInterface;
use Bnowak\CardGame\Player;
use Bnowak\CardGame\SuitInterface;

/**
 * TestsDataProvider
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class TestDataProvider
{
    /**
     * Create cards array for tests
     *
     * @return Card[]
     */
    public static function getCardsArray() : array
    {
        $cardsArray = array();
        foreach (self::getCardsDataArray() as $cardData) {
            $figure = $cardData[0];
            $suit = $cardData[1] ?? null;
            $visible = $cardData[2] ?? null;
            if ($suit !== null) {
                $card = new Card($figure, $suit);
            } else {
                $card = new Card($figure);
            }
            if ($visible !== null) {
                $card->setVisible($visible);
            }
            $cardsArray[] = $card;
        }
        
        return $cardsArray;
    }
    
    /**
     * Get cards data for cards creation
     *
     * @return array
     */
    public static function getCardsDataArray() : array
    {
        return array(
            array(FigureInterface::FIGURE_JACK, SuitInterface::SUIT_HEART, true),
            array(FigureInterface::FIGURE_QUEEN, SuitInterface::SUIT_DIAMOND, true),
            array(FigureInterface::FIGURE_KING, SuitInterface::SUIT_CLUB, false),
            array(FigureInterface::FIGURE_ACE, SuitInterface::SUIT_SPADE, false),
            array(FigureInterface::FIGURE_JOKER),
        );
    }
    
    /**
     * @return Card[]
     */
    public static function get1CardsArray() : array
    {
        return array(
            self::getCard2(),
        );
    }
    
    /**
     * @return Card[]
     */
    public static function get2CardsArray() : array
    {
        return array(
            self::getCard3(),
            self::getCard4(),
        );
    }
    
    /**
     * Get 2♥ card for tests
     *
     * @return Card
     */
    public static function getCard2() : Card
    {
        return new Card(FigureInterface::FIGURE_2, SuitInterface::SUIT_HEART);
    }
    
    /**
     * Get 3♦ card for tests
     *
     * @return Card
     */
    public static function getCard3() : Card
    {
        return new Card(FigureInterface::FIGURE_3, SuitInterface::SUIT_DIAMOND);
    }
    
    /**
     * Get 4♣ card for tests
     *
     * @return Card
     */
    public static function getCard4() : Card
    {
        return new Card(FigureInterface::FIGURE_4, SuitInterface::SUIT_CLUB);
    }
    
    /**
     * Create players array for tests
     *
     * @return Player[]
     */
    public static function getPlayersArray() : array
    {
        return array(
            self::getPlayer1(),
            self::getPlayer2(),
            new Player('player 3'),
        );
    }
    
    /**
     * Get player 1 for tests
     *
     * @return Player
     */
    public static function getPlayer1() : Player
    {
        return new Player('player 1');
    }
    
    /**
     * Get player 2 for tests
     *
     * @return Player
     */
    public static function getPlayer2() : Player
    {
        return new Player('player 2');
    }
}
