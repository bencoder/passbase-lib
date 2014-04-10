<?php
namespace PassBase\Entity\Serializer;

use PassBase\Entity\DecryptedPassword;

/**
 * Class to serialize/deserialize a DecryptedPassword object
 * If the format changes this class needs to handle backwards compatibility
 */
interface DecryptedPasswordSerializer
{
    /**
     * @param DecryptedPassword $password
     *
     * @return string the serialized data
     */
    public function serializePassword(DecryptedPassword $password);

    /**
     * @param string
     *
     * @return DecryptedPassword
     */
    public function deserializePassword($data);
}
