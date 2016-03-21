<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Tests;

use Bnowak\CardGame\Card;
use Bnowak\CardGame\CardCollection;
use Bnowak\CardGame\Exception\GameTableException;
use Bnowak\CardGame\GameTable;
use Bnowak\CardGame\Player;
use stdClass;

/**
 * GameTableTest
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class GameTableTest extends TestCase
{
    /**
     * @dataProvider constructSuccessProvider
     * @param Player[] $players
     */
    public function testConstructSuccess(array $players)
    {
        $gameTable = new GameTable($players);
        $this->assertInstanceOf(GameTable::class, $gameTable);
    }
    
    public static function constructSuccessProvider()
    {
        return array(
            'empty array param' => array(array()),
            'players array param' => array(TestDataProvider::getPlayersArray()),
        );
    }
    
    /**
     * @expectedException TypeError
     */
    public function testConstructFail()
    {
        new GameTable(array(new stdClass()));
    }
    
    public function testUnsetPlayer()
    {
        $players = TestDataProvider::getPlayersArray();
        $firstPlayer = reset($players);
        $gameTable = new GameTable($players);
        
        $this->assertInstanceOf(CardCollection::class, $gameTable->getPlayerCards($firstPlayer));
        
        $gameTable->unsetPlayer($firstPlayer);
        $expectedException = GameTableException::noPlayer($firstPlayer);
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        $this->assertInstanceOf(CardCollection::class, $gameTable->getPlayerCards($firstPlayer));
    }
    
    public function testGetPlayerCards()
    {
        $players = TestDataProvider::getPlayersArray();
        $playerNotInGame = TestDataProvider::getPlayer1();
        $firstPlayer = reset($players);
        $gameTable = new GameTable($players);
        
        $this->assertInstanceOf(CardCollection::class, $gameTable->getPlayerCards($firstPlayer));
        
        $expectedException = GameTableException::noPlayer($playerNotInGame);
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        $this->assertInstanceOf(CardCollection::class, $gameTable->getPlayerCards($playerNotInGame));
    }
    
    public function testGetCardsPile()
    {
        $gameTable = new GameTable(TestDataProvider::getPlayersArray());
        $this->assertInstanceOf(CardCollection::class, $gameTable->getCardsPile());
    }
    
    /**
     * @dataProvider countsProvider
     * @param Player[] $players
     * @param Card[] $firstPlayerCards
     * @param Card[] $cardsInPile
     */
    public function testCountSituatedCardPlayer(array $players, array $firstPlayerCards, array $cardsInPile)
    {
        $gameTable = $this->prepareGameTable($players, $firstPlayerCards, $cardsInPile);
        $this->assertSame(count($firstPlayerCards), $gameTable->countSituatedCardPlayer(reset($players)));
    }
    
    /**
     * @dataProvider countsProvider
     * @param Player[] $players
     * @param Card[] $firstPlayerCards
     * @param Card[] $cardsInPile
     */
    public function testCountCardsSituatedByAllPlayers(array $players, array $firstPlayerCards, array $cardsInPile)
    {
        $gameTable = $this->prepareGameTable($players, $firstPlayerCards, $cardsInPile);
        $this->assertSame(count($firstPlayerCards), $gameTable->countCardsSituatedByAllPlayers());
    }
    
    /**
     * @dataProvider countsProvider
     * @param Player[] $players
     * @param Card[] $firstPlayerCards
     * @param Card[] $cardsInPile
     */
    public function testCountCardsSituatedInPile(array $players, array $firstPlayerCards, array $cardsInPile)
    {
        $gameTable = $this->prepareGameTable($players, $firstPlayerCards, $cardsInPile);
        $this->assertSame(count($cardsInPile), $gameTable->countCardsSituatedInPile());
    }
    
    /**
     * @dataProvider countsProvider
     * @param Player[] $players
     * @param Card[] $firstPlayerCards
     * @param Card[] $cardsInPile
     */
    public function testCountAllCardsSituated(array $players, array $firstPlayerCards, array $cardsInPile)
    {
        $gameTable = $this->prepareGameTable($players, $firstPlayerCards, $cardsInPile);
        $this->assertSame(count($firstPlayerCards) + count($cardsInPile), $gameTable->countAllCardsSituated());
    }
    
    public static function countsProvider() : array
    {
        return array(
            array(
                TestDataProvider::getPlayersArray(),
                array(),
                array(),
            ),
            array(
                TestDataProvider::getPlayersArray(),
                TestDataProvider::get2CardsArray(),
                TestDataProvider::get1CardsArray(),
            ),
        );
    }
    
    public function testHasPlayerCardsSituated()
    {
        $players = TestDataProvider::getPlayersArray();
        $firstPlayer = reset($players);
        $gameTable = new GameTable($players);
        
        $this->assertFalse($gameTable->hasPlayerCardsSituated($firstPlayer));
        
        $gameTable->getPlayerCards($firstPlayer)->append(TestDataProvider::getCard2());
        $this->assertTrue($gameTable->hasPlayerCardsSituated($firstPlayer));
    }
    
    public function testHasEveryPlayerCardsSituated()
    {
        $player1 = TestDataProvider::getPlayer1();
        $player2 = TestDataProvider::getPlayer2();
        $gameTable = new GameTable(array($player1, $player2));
        
        $this->assertFalse($gameTable->hasEveryPlayerCardsSituated());
        
        $gameTable->getPlayerCards($player1)->append(TestDataProvider::getCard2());
        $this->assertFalse($gameTable->hasEveryPlayerCardsSituated());
        
        $gameTable->getPlayerCards($player2)->append(TestDataProvider::getCard3());
        $this->assertTrue($gameTable->hasEveryPlayerCardsSituated());
    }
    
    public function testHasEveryPlayerSameCardsSituatedCount()
    {
        $player1 = TestDataProvider::getPlayer1();
        $player2 = TestDataProvider::getPlayer2();
        $gameTable = new GameTable(array($player1, $player2));
        
        $this->assertTrue($gameTable->hasEveryPlayerSameCardsSituatedCount());
        
        $gameTable->getPlayerCards($player1)->append(TestDataProvider::getCard2());
        $this->assertFalse($gameTable->hasEveryPlayerSameCardsSituatedCount());
        
        $gameTable->getPlayerCards($player2)->append(TestDataProvider::getCard3());
        $this->assertTrue($gameTable->hasEveryPlayerSameCardsSituatedCount());
    }
    
    public function testIsCardSituatedByPlayer()
    {
        $player = TestDataProvider::getPlayer1();
        $card = TestDataProvider::getCard2();
        $gameTable = new GameTable(array($player));
        
        $this->assertFalse($gameTable->isCardSituatedByPlayer($card, $player));
        
        $gameTable->getPlayerCards($player)->append($card);
        $this->assertTrue($gameTable->isCardSituatedByPlayer($card, $player));
    }
    
    public function testIsCardSituatedByAnyPlayer()
    {
        $player = TestDataProvider::getPlayer1();
        $card = TestDataProvider::getCard2();
        $gameTable = new GameTable(array($player));
        
        $this->assertFalse($gameTable->isCardSituatedByAnyPlayer($card));
        
        $gameTable->getPlayerCards($player)->append($card);
        $this->assertTrue($gameTable->isCardSituatedByAnyPlayer($card));
    }
    
    public function testIsCardSituatedInPile()
    {
        $card = TestDataProvider::getCard2();
        $gameTable = new GameTable(array());
        
        $this->assertFalse($gameTable->isCardSituatedInPile($card));
        
        $gameTable->getCardsPile()->append($card);
        $this->assertTrue($gameTable->isCardSituatedInPile($card));
    }
    
    public function testGetPlayerWhoSituatedCard()
    {
        $player = TestDataProvider::getPlayer1();
        $card2 = TestDataProvider::getCard2();
        $card3 = TestDataProvider::getCard3();
        $gameTable = new GameTable(array($player));
        $gameTable->getPlayerCards($player)->append($card2);
        
        $this->assertInstanceOf(Player::class, $gameTable->getPlayerWhoSituatedCard($card2));
        
        $expectedException = GameTableException::noPlayerHasPlacedCard($card3);
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        $gameTable->getPlayerWhoSituatedCard($card3);
    }
    
    public function testCheckIsSituatedCard()
    {
        $player = TestDataProvider::getPlayer1();
        $card2 = TestDataProvider::getCard2();
        $card3 = TestDataProvider::getCard3();
        $gameTable = new GameTable(array($player));
        $gameTable->getPlayerCards($player)->append($card2);
        
        $gameTable->checkIsSituatedCard($card2);
        $this->addToAssertionCount(1);
        
        $expectedException = GameTableException::noPlayerHasPlacedCard($card3);
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        $gameTable->checkIsSituatedCard($card3);
    }
    
    public function testCheckEveryPlayersHasSituatedCards()
    {
        $player1 = TestDataProvider::getPlayer1();
        $player2 = TestDataProvider::getPlayer2();
        $card2 = TestDataProvider::getCard2();
        $card3 = TestDataProvider::getCard3();
        $gameTable = new GameTable(array($player1, $player2));
        $gameTable->getPlayerCards($player1)->append($card2);
        $gameTable->getPlayerCards($player2)->append($card3);
        
        $gameTable->checkEveryPlayersHasSituatedCards();
        $this->addToAssertionCount(1);
        
        $gameTable->getPlayerCards($player1)->clear();
        $expectedException = GameTableException::notAllPlayersPlacedCards();
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        $gameTable->checkEveryPlayersHasSituatedCards();
    }
    
    /**
     * @param Player[] $players
     * @param Card[] $firstPlayerCards
     * @param Card[] $cardsInPile
     * @return GameTable
     */
    private function prepareGameTable(array $players, array $firstPlayerCards, array $cardsInPile) : GameTable
    {
        $gameTable = new GameTable($players);
        $gameTable->getPlayerCards(reset($players))->appendMany($firstPlayerCards);
        $gameTable->getCardsPile()->appendMany($cardsInPile);
                
        return $gameTable;
    }
}
