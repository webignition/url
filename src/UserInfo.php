<?php

namespace webignition\Url;

class UserInfo
{
    private $user = '';
    private $password = null;

    public function __construct(string $user, ?string $password)
    {
        $this->user = $user;
        $this->password = $password;
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
            $userInfo .= ':' . $this->password;
        }

        return $userInfo;
    }
}
