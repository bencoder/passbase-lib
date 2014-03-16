<?php
namespace PassBase\Entity\Serializer\Json;

class DecryptedPasswordSerializer implements \PassBase\Entity\Serializer\DecryptedPasswordSerializer
{
    /**
     * {@inherit}
     */
    public function serializePassword(\PassBase\Entity\DecryptedPassword $password)
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
        return new \PassBase\Entity\DecryptedPassword(
            $data["url"],
            $data["username"],
            $data["password"],
            $data["notes"],
            new \DateTime($data["createdAt"])
        );
    }
}
