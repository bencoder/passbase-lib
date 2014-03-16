<?php
namespace PassBase\Entity;

class DecryptedPassword
{
    /**
     * @var string
     */
    private $url, $username, $password, $notes;

    /**
     * @var \DateTime
     */
    private $createdAt;

    public function __construct(
        $url,
        $username,
        $password,
        $notes,
        \DateTime $createdAt
    ) {
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

}
