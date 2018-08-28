<?php

declare(strict_types=1);

namespace Itineris\DisallowPwnedPasswords\HaveIBeenPwned;

class Password
{
    /**
     * The password in plain text.
     *
     * @var string
     */
    protected $cleartext;

    /**
     * The password in uppercase SHA-1 hash.
     *
     * @var string
     */
    protected $hash;

    /**
     * Password constructor.
     *
     * @param string $cleartext The password in plain text.
     */
    public function __construct(string $cleartext)
    {
        $this->cleartext = $cleartext;
    }

    /**
     * Calculate the first 5 characters of the SHA-1 hash.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return substr(
            $this->getHash(),
            0,
            5
        );
    }

    /**
     * Calculate the suffix (from 6th character onwards) of the SHA-1 hash.
     *
     * @return string
     */
    public function getSuffix(): string
    {
        return substr(
            $this->getHash(),
            5
        );
    }

    /**
     * Calculate the sha1 hash of a password, then uppercase it.
     *
     * @return string
     */
    protected function getHash(): string
    {
        if (empty($this->hash)) {
            $this->hash = utf8_encode(
                strtoupper(
                    sha1($this->cleartext)
                )
            );
        }

        return $this->hash;
    }
}
