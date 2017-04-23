<?php

namespace Application\Security\Utils;

use Phalcon\Di;

/**
 * Created by PhpStorm.
 * User: juliuskoronci
 * Date: 17/04/2017
 * Time: 18:01
 */
class Firewall
{
    /** @var Di */
    private $di;

    /**
     * Firewall constructor.
     * @param Di $di
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function shouldSecure(string $path): bool
    {
        /** @var array $firewallEntries */
        $firewallEntries = $this->di->get('firewall');

        foreach ($firewallEntries as $entry) {
            $isMatch = $this->matchPathWithEntry($path, $entry);
            if ($isMatch) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $path
     * @param string $entry
     * @return bool
     */
    private function matchPathWithEntry(string $path, string $entry): bool
    {
        return preg_match('{' . $entry . '}', $path);
    }
}