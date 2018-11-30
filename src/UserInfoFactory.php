<?php

namespace webignition\Url;

class UserInfoFactory
{
    public static function create(string $user, string $password): string
    {
        $userInfo = '';

        if (!empty($user)) {
            $userInfo .= $user;
        }

        if (!empty($password)) {
            $userInfo .= ':' . $password;
        }

        return $userInfo;
    }
}
