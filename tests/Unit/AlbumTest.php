<?php

namespace Chinook\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Chinook\Models\Album;
use Chinook\Db\Database;

class AlbumTest extends TestCase
{
    protected function setUp(): void
    {
        // Include config to define database constants
        require_once __DIR__ . '/../../config/config.php';
    }

    public function testAlbumClassExists(): void
    {
        // Arrange
        $this->assertTrue(class_exists(Album::class), 'Album class should exist');

        // Act
        $reflection = new \ReflectionClass(Album::class);

        // Assert
        $this->assertFalse($reflection->isAbstract(), 'Album class should not be abstract');
        $this->assertTrue($reflection->hasMethod('getAll'), 'Album should have getAll method');
        $this->assertTrue($reflection->hasMethod('getById'), 'Album should have getById method');
        $this->assertTrue($reflection->hasMethod('create'), 'Album should have create method');
        $this->assertTrue($reflection->hasMethod('delete'), 'Album should have delete method');

        $getAllMethod = $reflection->getMethod('getAll');
        $this->assertTrue($getAllMethod->isPublic(), 'getAll method should be public');
    }

    public function testGetAlbumById(): void
    {
        // Arrange
        $mockDatabase = $this->createMock(Database::class);
        $expectedResult = [
            'AlbumId' => 1, 
            'Title' => 'For Those About to Rock We Salute You', 
            'ArtistId' => 1,
            'ArtistName' => 'AC/DC'
        ];
        
        $mockDatabase->expects($this->once())
                    ->method('fetchOne')
                    ->with(
                        $this->equalTo("SELECT a.AlbumId, a.Title, a.ArtistId, ar.Name as ArtistName 
                FROM Album a
                JOIN Artist ar ON a.ArtistId = ar.ArtistId
                WHERE a.AlbumId = ?"),
                        $this->equalTo([1])
                    )
                    ->willReturn($expectedResult);

        $album = new Album($mockDatabase);

        // Act
        $result = $album->getById(1);

        // Assert
        $this->assertEquals($expectedResult, $result, 'getById should return the correct album');
    }

    public function testCreateAlbum(): void
    {
        // Arrange
        $mockDatabase = $this->createMock(Database::class);

        // Mock the fetchOne call (to get max ID)
        $mockDatabase->expects($this->once())
                    ->method('fetchOne')
                    ->with($this->equalTo("SELECT MAX(AlbumId) as maxId FROM Album"))
                    ->willReturn(['maxId' => 10]);

        // Mock the query call (to insert new album)
        $mockDatabase->expects($this->once())
                    ->method('query')
                    ->with(
                        $this->equalTo("INSERT INTO Album (AlbumId, Title, ArtistId) VALUES (?, ?, ?)"),
                        $this->equalTo([11, 'For Those About to Rock We Salute You', 1])
                    );

        $album = new Album($mockDatabase);

        // Act
        $result = $album->create('For Those About to Rock We Salute You', 1);

        // Assert
        $this->assertEquals(11, $result, 'create should return the new album ID');
    }
}