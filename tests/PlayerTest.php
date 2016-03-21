<?php
declare(strict_types = 1);

namespace Bnowak\CardGame\Tests;

use Bnowak\CardGame\CardCollection;
use Bnowak\CardGame\Exception\PlayerException;
use Bnowak\CardGame\Player;

/**
 * PlayerTest
 *
 * @author Bartłomiej Nowak <barteknowak90@gmail.com>
 */
class PlayerTest extends TestCase
{
    public function testConstructSuccess()
    {
        $player = new Player('player name');
        $this->assertInstanceOf(Player::class, $player);
        $this->assertSame('player name', $player->getName());
        $this->assertSame(0, $player->getCards()->count());
    }
    
    public function testGetCards()
    {
        $player = new Player('player name');
        $this->assertInstanceOf(CardCollection::class, $player->getCards());
    }
    
    public function testToString()
    {
        $player = new Player('player name');
        $this->assertSame('player name', (string) $player);
    }
    
    public function testCheckPlayerHasNotCard()
    {
        $player = new Player('player name');
        $card = TestDataProvider::getCard2();
        
        $player->checkPlayerHasNotCard($card);
        $this->addToAssertionCount(1);
        
        $player->getCards()->append($card);
        $expectedException = PlayerException::playerHasAlreadyCard($player, $card);
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        $player->checkPlayerHasNotCard($card);
    }
    
    public function testCheckPlayerHasCard()
    {
        $player = new Player('player name');
        $card = TestDataProvider::getCard2();
        $player->getCards()->append($card);
        
        $player->checkPlayerHasCard($card);
        $this->addToAssertionCount(1);
        
        $player->getCards()->clear();
        $expectedException = PlayerException::playerDoesNotHaveCard($player, $card);
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        $player->checkPlayerHasCard($card);
    }
    
    public function testCheckIfAreDiffrentPlayers()
    {
        $player1 = new Player('player 1');
        $player2 = new Player('player 2');
        
        Player::checkIfAreDiffrentPlayers($player1, $player2);
        $this->addToAssertionCount(1);
        
        $expectedException = PlayerException::theseSamePlayers($player1);
        $this->expectException(get_class($expectedException));
        $this->expectExceptionMessage($expectedException->getMessage());
        Player::checkIfAreDiffrentPlayers($player1, $player1);
    }
}
