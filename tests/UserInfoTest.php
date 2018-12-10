<?php

namespace webignition\Url\Tests;

use webignition\Url\UserInfo;

class UserInfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider toStringDataProvider
     *
     * @param string $user
     * @param string|null $password
     * @param string $expectedString
     */
    public function testToString(string $user, ?string $password, string $expectedString)
    {
        $userInfo = new UserInfo($user, $password);

        $this->assertSame($expectedString, (string) $userInfo);
    }

    public function toStringDataProvider(): array
    {
        return [
            'user empty, password null' => [
                'user' => '',
                'password' => null,
                'expectedString' => '',
            ],
            'user empty, password empty' => [
                'user' => '',
                'password' => '',
                'expectedString' => '',
            ],
            'user only' => [
                'user' => 'user',
                'password' => null,
                'expectedString' => 'user',
            ],
            'user and password' => [
                'user' => 'user',
                'password' => 'password',
                'expectedString' => 'user:password',
            ],
        ];
    }

    /**
     * @dataProvider fromStringDataProvider
     *
     * @param string $userInfoString
     * @param string $expectedUser
     * @param string|null $expectedPassword
     *
     */
    public function testFromString(string $userInfoString, string $expectedUser, ?string $expectedPassword)
    {
        $userInfo = UserInfo::fromString($userInfoString);

        $this->assertSame($expectedUser, $userInfo->getUser());
        $this->assertSame($expectedPassword, $userInfo->getPassword());
    }

    public function fromStringDataProvider(): array
    {
        return [
            'empty' => [
                'userInfoString' => '',
                'expectedUser' => '',
                'expectedPassword' => null,
            ],
            'user only' => [
                'userInfoString' => 'user',
                'expectedUser' => 'user',
                'expectedPassword' => null,
            ],
            'user and empty password' => [
                'userInfoString' => 'user:',
                'expectedUser' => 'user',
                'expectedPassword' => null,
            ],
            'user and password' => [
                'userInfoString' => 'user:password',
                'expectedUser' => 'user',
                'expectedPassword' => 'password',
            ],
        ];
    }
}
