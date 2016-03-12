<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Tests;

use Bnowak\CardGame\Card;
use Bnowak\CardGame\Exception\CardException;
use Bnowak\CardGame\FigureInterface;
use Bnowak\CardGame\SuitInterface;
use PHPUnit_Framework_TestCase;

/**
 * CardTest
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class CardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider \Bnowak\CardGame\Tests\TestDataProvider::getCardsDataArray
     */
    public function testConstructSuccess(string $figure, string $suit = null)
    {
        $this->assertInstanceOf(Card::class, new Card($figure, $suit));
    }
    
    /**
     * @dataProvider constructFailProvider
     */
    public function testConstructFail(CardException $expectedException, string $figure = null, string $suit = null)
    {
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        new Card($figure, $suit);
    }
    
    /**
     * @return array
     */
    public static function constructFailProvider() : array
    {
        return array(
            array(CardException::incorrectFigure('notExistedFigure'), 'notExistedFigure', null),
            array(CardException::incorrectFigure('notExistedFigure'), 'notExistedFigure', 'notExistedSuit'),
            array(CardException::incorrectSuit('notExistedSuit'), FigureInterface::FIGURE_ACE, 'notExistedSuit'),
            array(CardException::incorrectFigure('notExistedFigure'), 'notExistedFigure', SuitInterface::SUIT_HEART),
            array(CardException::jokerWithSuit('notExistedSuit'), FigureInterface::FIGURE_JOKER, 'notExistedSuit'),
            array(
                CardException::jokerWithSuit(SuitInterface::SUIT_HEART),
                FigureInterface::FIGURE_JOKER,
                SuitInterface::SUIT_HEART,
            ),
        );
    }
    
    /**
     * @dataProvider \Bnowak\CardGame\Tests\TestDataProvider::getCardsDataArray
     */
    public function testGetSuit(string $figure, string $suit = null)
    {
        $card = new Card($figure, $suit);
        $this->assertSame((string) $suit, $card->getSuit());
    }
    
    /**
     * @dataProvider \Bnowak\CardGame\Tests\TestDataProvider::getCardsDataArray
     */
    public function testGetFigure(string $figure, string $suit = null)
    {
        $card = new Card($figure, $suit);
        $this->assertSame((string) $figure, $card->getFigure());
    }
    
    /**
     * @dataProvider \Bnowak\CardGame\Tests\TestDataProvider::getCardsDataArray
     */
    public function testVisible(string $figure, string $suit = null, bool $isVisible = false)
    {
        $card = new Card($figure, $suit);
        $card->setVisible($isVisible);
        $this->assertSame($isVisible, $card->isVisible());
    }
    
    /**
     * @dataProvider \Bnowak\CardGame\Tests\TestDataProvider::getCardsDataArray
     */
    public function testToString(string $figure, string $suit = null)
    {
        $color = array_key_exists($suit, SuitInterface::SUIT_COLOR) ? SuitInterface::SUIT_COLOR[$suit] : 'black';
        $card = new Card($figure, $suit);
        $this->assertContains($figure.$suit, $card->__toString());
        $this->assertContains("color: $color", $card->__toString());
    }
    
    public function testCheckSuitIsCorrect()
    {
        foreach (SuitInterface::SUITS as $suit) {
            Card::checkSuitIsCorrect($suit);
            $this->addToAssertionCount(1);
        }
        
        $expectedException = CardException::incorrectSuit('notExistedSuit');
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        Card::checkSuitIsCorrect('notExistedSuit');
    }
    
    public function testCheckFigureIsCorrect()
    {
        foreach (FigureInterface::FIGURES as $figure) {
            Card::checkFigureIsCorrect($figure);
            $this->addToAssertionCount(1);
        }
        
        $expectedException = CardException::incorrectFigure('notExistedFigure');
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        Card::checkFigureIsCorrect('notExistedFigure');
    }
}
