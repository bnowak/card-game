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
        );
    }
}
