<?php
namespace PassBase\Entity\Serializer;

/**
 * Class to serialize/deserialize a DecryptedPassword object
 * If the format changes this class needs to handle backwards compatibility
 */
interface DecryptedPasswordSerializer
{
    /**
     * @param \PassBase\Entity\DecryptedPassword $password
     *
     * @return string the serialized data
     */
    public function serializePassword(\PassBase\Entity\DecryptedPassword $password);

    /**
     * @param string
     *
     * @return \PassBase\Entity\DecryptedPassword
     */
    public function deserializePassword($data);
}
