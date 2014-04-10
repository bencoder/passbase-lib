<?php
namespace PassBase\Entity\Serializer\Json;

use PassBase\Entity\DecryptedPassword;
use PassBase\Entity\Serializer\DecryptedPasswordSerializer as DecryptedPasswordSerializerInterface;

class DecryptedPasswordSerializer implements DecryptedPasswordSerializerInterface
{
    /**
     * {@inherit}
     */
    public function serializePassword(DecryptedPassword $password)
    {
        $data = array(
            "url" => $password->getUrl(),
            "username" => $password->getUsername(),
            "password" => $password->getPassword(),
            "notes" => $password->getNotes(),
            "createdAt" => $password->getCreatedAt()->format("r"),
        );
        return json_encode($data);
    }

    /**
     * {@inherit}
     */
    public function deserializePassword($data)
    {
        $data = json_decode($data,true);
        return new DecryptedPassword(
            $data["url"],
            $data["username"],
            $data["password"],
            $data["notes"],
            new \DateTime($data["createdAt"])
        );
    }
}
