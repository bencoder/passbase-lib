<?php
namespace PassBase\Encoder;

/**
 * Encoder/Decode interface for decoding or encoding data
 */
interface Encoder
{
    /**
     * Encodes the data using the specified key
     *
     * @param string $key
     * @param string $data The cleartext data
     *
     * @return string The encrypted data
     */
    public function encode($key, $data);

    /**
     * Decodes the data using the specified key
     *
     * @param string $key The key
     * @param string $data The encrypted data
     *
     * @param string The decrypted data
     */
    public function decode($key, $data);
}
