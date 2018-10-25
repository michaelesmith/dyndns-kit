<?php
declare(strict_types = 1);

namespace DynDNSKit\Validator;

class IPValidator
{
    /**
     * @param $ip
     * @return bool/string
     */
    public static function ip($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
    }

    /**
     * @param $ip
     * @return bool/string
     */
    public static function ipv4($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * @param $ip
     * @return bool/string
     */
    public static function ipv6($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }
}
