<?php

use PHPUnit\Framework\TestCase;

// Include the Database class since it might not be autoloaded
require_once __DIR__ . '/../../config/database.php';

class DatabaseTest extends TestCase
{
    /**
     * @testdox Database::getInstance returns the same singleton instance
     */
    public function testSingletonInstance()
    {
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();

        $this->assertInstanceOf(Database::class, $db1);
        $this->assertSame($db1, $db2, 'Database instance should be a singleton');
    }

    /**
     * @testdox Connects to the database and returns a valid PDO object
     */
    public function testConnection()
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $this->assertInstanceOf(PDO::class, $connection);
    }
}
