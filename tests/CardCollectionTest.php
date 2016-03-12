<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Tests;

use Bnowak\CardGame\Card;
use Bnowak\CardGame\CardCollection;
use Bnowak\CardGame\FigureInterface;
use Bnowak\CardGame\SuitInterface;
use PHPUnit_Framework_TestCase;
use stdClass;
use TypeError;

/**
 * CardCollectionTest
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class CardCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider constructSuccessProvider
     * @param mixed $cardsArray
     */
    public function testConstructSuccess($cardsArray = null)
    {
        if ($cardsArray !== null) {
            $cardCollection = new CardCollection($cardsArray);
        } else {
            $cardCollection = new CardCollection();
        }
        $this->assertInstanceOf(CardCollection::class, $cardCollection);
    }
    
    /**
     * @return array
     */
    public function constructSuccessProvider() : array
    {
        return array(
            'none param' => array(),
            'empty array param' => array(array()),
            'cards array param' => array(TestDataProvider::getCardsArray()),
        );
    }
    
    /**
     * @expectedException TypeError
     */
    public function testConstructFail()
    {
        new CardCollection(array(new stdClass()));
    }
    
    /**
     * @return array
     */
    public function emptyAndFilledCardCollectionProvider() : array
    {
        return array(
            array(new CardCollection()),
            array(new CardCollection(TestDataProvider::getCardsArray())),
        );
    }
    
    /**
     * @dataProvider emptyAndFilledCardCollectionProvider
     */
    public function testAppend(CardCollection $cardCollection)
    {
        $count = $cardCollection->count();
        $appendCard = TestDataProvider::getCard2();
        $this->assertFalse($cardCollection->has($appendCard));
        $cardCollection->append($appendCard);
        $this->assertSame(++$count, $cardCollection->count());
        $this->assertSame($appendCard, $cardCollection->getLast());
        $this->assertTrue($cardCollection->has($appendCard));
    }
    
    /**
     * @dataProvider emptyAndFilledCardCollectionProvider
     */
    public function testPrepend(CardCollection $cardCollection)
    {
        $count = $cardCollection->count();
        $prependCard = TestDataProvider::getCard2();
        $this->assertFalse($cardCollection->has($prependCard));
        $cardCollection->prepend($prependCard);
        $this->assertSame(++$count, $cardCollection->count());
        $this->assertSame($prependCard, $cardCollection->getFirst());
        $this->assertTrue($cardCollection->has($prependCard));
    }
    
    /**
     * @dataProvider emptyAndFilledCardCollectionProvider
     */
    public function testAppendMany(CardCollection $cardCollection)
    {
        $count = $cardCollection->count();
        $appendCardsArray = array(
            array(),
            TestDataProvider::get1CardsArray(),
            TestDataProvider::get2CardsArray(),
        );
        foreach ($appendCardsArray as $appendCards) {
            foreach ($appendCards as $card) {
                $this->assertFalse($cardCollection->has($card));
            }
            $cardCollection->appendMany($appendCards);
            $count += count($appendCards);
            $this->assertSame($count, $cardCollection->count());
            if (count($appendCards) > 0) {
                $this->assertSame(end($appendCards), $cardCollection->getLast());
            }
            foreach ($appendCards as $card) {
                $this->assertTrue($cardCollection->has($card));
            }
        }
    }
    
    /**
     * @dataProvider emptyAndFilledCardCollectionProvider
     */
    public function testPrependMany(CardCollection $cardCollection)
    {
        $count = $cardCollection->count();
        $prependCardsArray = array(
            array(),
            TestDataProvider::get1CardsArray(),
            TestDataProvider::get2CardsArray(),
        );
        foreach ($prependCardsArray as $prependCards) {
            foreach ($prependCards as $card) {
                $this->assertFalse($cardCollection->has($card));
            }
            $cardCollection->prependMany($prependCards);
            $count += count($prependCards);
            $this->assertSame($count, $cardCollection->count());
            if (count($prependCards) > 0) {
                $this->assertSame(end($prependCards), $cardCollection->getFirst());
            }
            foreach ($prependCards as $card) {
                $this->assertTrue($cardCollection->has($card));
            }
        }
    }
}
