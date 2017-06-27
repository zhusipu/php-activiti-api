<?php

namespace Activiti\Tests\Client\Service;

use Activiti\Client\Model\User\User;
use Activiti\Client\Model\User\UserCreate;
use Activiti\Client\Model\User\UserInfo;
use Activiti\Client\Model\User\UserInfoList;
use Activiti\Client\Model\User\UserList;
use Activiti\Client\Model\User\UserQuery;
use Activiti\Client\Model\User\UserUpdate;
use Activiti\Client\Service\UserService;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

class UserServiceTest extends AbstractServiceTest
{
    public function testGetUser()
    {
        $expected = [
            'id' => 'testuser',
            'firstName' => 'Fred',
            'lastName' => 'McDonald',
            'url' => 'http://localhost:8182/identity/users/testuser',
            'email' => 'no-reply@activiti.org',
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expected))
        ]);

        $result = $this
            ->createUserService($client)
            ->getUser('testuser');

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('GET', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser', (string)$this->getLastRequest()->getUri());
        $this->assertEquals(new User($expected), $result);
    }

    public function testGetUserList()
    {
        $expectedUri = 'identity/users';

        $expectedResult = [
            'data' => [
                [
                    'id' => 'testgroup',
                    'url' => 'http://localhost:8182/identity/groups/testgroup',
                    'name' => 'Test group',
                    'type' => 'Test type',
                ],
            ],
            'total' => 3,
            'start' => 0,
            'sort' => 'id',
            'order' => 'asc',
            'size' => 3,
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expectedResult))
        ]);

        $result = $this
            ->createUserService($client)
            ->getUsersList(new UserQuery([
                // TODO: Query parameters
            ]));

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('GET', $this->getLastRequest()->getMethod());
        $this->assertEquals($expectedUri, (string)$this->getLastRequest()->getUri());
        $this->assertEquals(new UserList($expectedResult), $result);
    }

    public function testCreateUser()
    {
        $expected = [
            'id' => 'testuser',
            'firstName' => 'Fred',
            'lastName' => 'McDonald',
            'url' => 'http://localhost:8182/identity/users/testuser',
            'email' => 'no-reply@activiti.org',
        ];

        $payload = [
            'id' => 'testuser',
            'firstName' => 'Fred',
            'lastName' => 'McDonald',
            'email' => 'no-reply@activiti.org',
            'password' => '123456'
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expected))
        ]);

        $result = $this
            ->createUserService($client)
            ->createUser(new UserCreate($payload));

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('POST', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users', (string)$this->getLastRequest()->getUri());
        $this->assertEquals(json_encode($payload), $this->getLastRequest()->getBody()->getContents());
        $this->assertEquals(new User($expected), $result);
    }

    public function testUpdateUser()
    {
        $expected = [
            'id' => 'testuser',
            'firstName' => 'Fred',
            'lastName' => 'McDonald',
            'url' => 'http://localhost:8182/identity/users/testuser',
            'email' => 'no-reply@activiti.org',
        ];

        $payload = [
            'firstName' => 'Fred',
            'lastName' => 'McDonald',
            'email' => 'no-reply@activiti.org',
            'password' => '123456'
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expected))
        ]);

        $result = $this
            ->createUserService($client)
            ->updateUser('testuser', new UserUpdate($payload));

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('PUT', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser', (string)$this->getLastRequest()->getUri());
        $this->assertEquals(json_encode($payload), $this->getLastRequest()->getBody()->getContents());
        $this->assertEquals(new User($expected), $result);
    }

    public function testDeleteUser()
    {
        $client = $this->createClient([new Response(204)]);

        $result = $this
            ->createUserService($client)
            ->deleteUser('testuser');

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('DELETE', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser', (string)$this->getLastRequest()->getUri());
        $this->assertNull($result);
    }

    public function testGetUserPicture()
    {
        $userId = 'kermit';

        $expected = "(Some binary data)";

        $client = $this->createClient([
            new Response(200, [], $expected)
        ]);

        $actual = $this
            ->createUserService($client)
            ->getUserPicture($userId);

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('GET', $this->getLastRequest()->getMethod());
        $this->assertEquals("identity/users/$userId/picture", (string)$this->getLastRequest()->getUri());
        $this->assertEquals($expected, $actual);
    }

    public function testSetUserPicture()
    {
        $userId = 'kermit';
        $picture = "(Some binary data)";

        $client = $this->createClient([
            new Response(204, [], $picture)
        ]);

        $actual = $this
            ->createUserService($client)
            ->setUserPicture($userId, $picture);

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('PUT', $this->getLastRequest()->getMethod());
        $this->assertEquals("identity/users/$userId/picture", (string)$this->getLastRequest()->getUri());
        $this->assertNull($actual);
    }

    public function testGetUserInfo()
    {
        $expected = [
            'key' => 'key1',
            'value' => 'Value 1',
            'url' => 'http://localhost:8182/identity/users/testuser/info/key1',
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expected))
        ]);

        $result = $this
            ->createUserService($client)
            ->getUserInfo('testuser', 'key1');

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('GET', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser/info/key1', (string)$this->getLastRequest()->getUri());
        $this->assertEquals(new UserInfo($expected), $result);
    }

    public function testGetUserInfoList()
    {
        $expected = [
            [
                'key' => 'key1',
                'url' => 'http://localhost:8182/identity/users/testuser/info/key1',
            ],
            [
                'key' => 'key2',
                'url' => 'http://localhost:8182/identity/users/testuser/info/key2',
            ]
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expected))
        ]);

        $result = $this
            ->createUserService($client)
            ->getUserInfoList('testuser');

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('GET', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser/info', (string)$this->getLastRequest()->getUri());
        $this->assertEquals(new UserInfoList($expected), $result);
    }

    public function testCreateUserInfo()
    {
        $expected = [
            'key' => 'key1',
            'value' => 'The updated value',
            'url' => 'http://localhost:8182/identity/users/testuser/info/key1',
        ];

        $payload = [
            'key' => 'key1',
            'value' => 'The updated value',
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expected))
        ]);

        $result = $this
            ->createUserService($client)
            ->createUserInfo('testuser', 'key1', 'The updated value');

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('POST', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser/info', (string)$this->getLastRequest()->getUri());
        $this->assertEquals(json_encode($payload), $this->getLastRequest()->getBody()->getContents());
        $this->assertEquals(new UserInfo($expected), $result);
    }

    public function testUpdateUserInfo()
    {
        $expected = [
            'key' => 'key1',
            'value' => 'The updated value',
            'url' => 'http://localhost:8182/identity/users/testuser/info/key1',
        ];

        $payload = [
            'value' => 'The updated value',
        ];

        $client = $this->createClient([
            new Response(200, [], json_encode($expected))
        ]);

        $result = $this
            ->createUserService($client)
            ->updateUserInfo('testuser', 'key1', 'The updated value');

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('PUT', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser/info/key1', (string)$this->getLastRequest()->getUri());
        $this->assertEquals(json_encode($payload), $this->getLastRequest()->getBody()->getContents());
        $this->assertEquals(new UserInfo($expected), $result);
    }

    public function testDeleteUserInfo()
    {
        $client = $this->createClient([new Response(204)]);

        $result = $this
            ->createUserService($client)
            ->deleteUserInfo('testuser', 'key1');

        $this->assertCount(1, $this->getHistory());
        $this->assertEquals('DELETE', $this->getLastRequest()->getMethod());
        $this->assertEquals('identity/users/testuser/info/key1', (string)$this->getLastRequest()->getUri());
        $this->assertNull($result);
    }

    private function createUserService(ClientInterface $client)
    {
        return new UserService($client);
    }
}