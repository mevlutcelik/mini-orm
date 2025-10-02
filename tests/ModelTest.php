<?php

namespace MiniOrm\Tests;

use PHPUnit\Framework\TestCase;
use MiniOrm\Database;
use MiniOrm\Models\User;
use MiniOrm\Models\Post;

class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        Database::setConfig([
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'test_db',
            'username' => 'root',
            'password' => 'root'
        ]);

        // Test için basit in-memory sqlite kullanabiliriz
        // Gerçek projede MySQL kullanılacak
    }

    public function testUserCreation()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'age' => 25,
            'status' => 'active'
        ];

        $user = new User($userData);
        
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals(25, $user->age);
        $this->assertEquals('active', $user->status);
    }

    public function testUserToArray()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'age' => 25
        ];

        $user = new User($userData);
        $array = $user->toArray();

        $this->assertIsArray($array);
        $this->assertEquals($userData, $array);
    }

    public function testUserToJson()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ];

        $user = new User($userData);
        $json = $user->toJson();

        $this->assertJson($json);
        $this->assertEquals(json_encode($userData), $json);
    }

    public function testFillableAttributes()
    {
        $user = new User();
        
        // Fillable olan alanlar
        $user->fill([
            'name' => 'Test',
            'email' => 'test@test.com',
            'age' => 30
        ]);

        $this->assertEquals('Test', $user->name);
        $this->assertEquals('test@test.com', $user->email);
        $this->assertEquals(30, $user->age);
    }

    public function testTableName()
    {
        $user = new User();
        $this->assertEquals('users', $user->getTable());

        $post = new Post();
        $this->assertEquals('posts', $post->getTable());
    }

    public function testPrimaryKey()
    {
        $user = new User();
        $this->assertEquals('id', $user->getKeyName());
    }

    public function testAttributeGettersSetters()
    {
        $user = new User();
        
        $user->setAttribute('name', 'John Doe');
        $this->assertEquals('John Doe', $user->getAttribute('name'));
        
        // Magic methods test
        $user->email = 'john@example.com';
        $this->assertEquals('john@example.com', $user->email);
    }
}