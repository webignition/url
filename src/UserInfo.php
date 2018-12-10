<?php

namespace webignition\Url;

class UserInfo
{
    const USER_PASS_DELIMITER = ':';

    private $user = '';
    private $password = null;

    public function __construct(string $user, ?string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public static function fromString(string $userInfo): UserInfo
    {
        $parts = explode(self::USER_PASS_DELIMITER, $userInfo, 2);
        $partCount = count($parts);

        $user = '';
        $password = null;

        if ($partCount) {
            $user = $parts[0];

            if ($partCount > 1) {
                $password = $parts[1];
                $password = empty($password) ? null : $password;
            }
        }

        return new static($user, $password);
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function __toString(): string
    {
        $userInfo = '';

        if (!empty($this->user)) {
            $userInfo .= $this->user;
        }

        if (!empty($this->password)) {
            $userInfo .=  self::USER_PASS_DELIMITER . $this->password;
        }

        return $userInfo;
    }
}
