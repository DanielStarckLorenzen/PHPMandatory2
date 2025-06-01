<?php

namespace Chinook\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Chinook\Models\Artist;
use Chinook\Db\Database;

class ArtistTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../config/config.php';
    }

    public function testArtistClassExists(): void
    {
        // Arrange
        $this->assertTrue(class_exists(Artist::class), 'Artist class should exist');

        // Act
        $reflection = new \ReflectionClass(Artist::class);

        // Assert
        $this->assertFalse($reflection->isAbstract(), 'Artist class should not be abstract');
        $this->assertTrue($reflection->hasMethod('getAll'), 'Artist should have getAll method');
        $this->assertTrue($reflection->hasMethod('getById'), 'Artist should have getById method');
        $this->assertTrue($reflection->hasMethod('getAlbums'), 'Artist should have getAlbums method');
        $this->assertTrue($reflection->hasMethod('create'), 'Artist should have create method');
        $this->assertTrue($reflection->hasMethod('delete'), 'Artist should have delete method');
        
        $getAllMethod = $reflection->getMethod('getAll');
        $this->assertTrue($getAllMethod->isPublic(), 'getAll method should be public');
    }

    public function testArtistGetAllWithoutSearch(): void
    {
        // Arrange
        $mockDatabase = $this->createMock(Database::class);
        $expectedResult = [
            ['ArtistId' => 1, 'Name' => 'AC/DC'],
            ['ArtistId' => 2, 'Name' => 'Accept']
        ];
        
        $mockDatabase->expects($this->once())
                    ->method('fetchAll')
                    ->with(
                        $this->equalTo("SELECT ArtistId, Name FROM Artist ORDER BY Name"),
                        $this->equalTo([])
                    )
                    ->willReturn($expectedResult);
        
        $artist = new Artist($mockDatabase);

        // Act
        $result = $artist->getAll();
        
        // Assert
        $this->assertIsArray($result, 'getAll should return an array');
        $this->assertCount(2, $result, 'Should return 2 artists');
        $this->assertEquals('AC/DC', $result[0]['Name'], 'First artist should be AC/DC');
        $this->assertEquals('Accept', $result[1]['Name'], 'Second artist should be Accept');
    }

    public function testArtistGetById(): void
    {
        // Arrange
        $mockDatabase = $this->createMock(Database::class);
        $expectedResult = ['ArtistId' => 1, 'Name' => 'AC/DC'];

        $mockDatabase->expects($this->once())
                    ->method('fetchOne')
                    ->with(
                        $this->equalTo("SELECT ArtistId, Name FROM Artist WHERE ArtistId = ?"),
                        $this->equalTo([1])
                    )
                    ->willReturn($expectedResult);

        $artist = new Artist($mockDatabase);

        // Act
        $result = $artist->getById(1);

        // Assert
        $this->assertEquals($expectedResult, $result, 'getById should return the correct artist');
    }

    public function testCreateArtist(): void
    {
        // Arrange
        $mockDatabase = $this->createMock(Database::class);
        
        // Mock the fetchOne call (to get max ID)
        $mockDatabase->expects($this->once())
                    ->method('fetchOne')
                    ->with($this->equalTo("SELECT MAX(ArtistId) as maxId FROM Artist"))
                    ->willReturn(['maxId' => 5]);
        
        // Mock the query call (to insert new artist)
        $mockDatabase->expects($this->once())
                    ->method('query')
                    ->with(
                        $this->equalTo("INSERT INTO Artist (ArtistId, Name) VALUES (?, ?)"),
                        $this->equalTo([6, 'AC/DC'])
                    );

        $artist = new Artist($mockDatabase);

        // Act
        $result = $artist->create('AC/DC');

        // Assert
        $this->assertEquals(6, $result, 'create should return the new artist ID');
    }
}