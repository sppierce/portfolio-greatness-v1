<?php

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Gears
 * @author              Steven Palmer
 * @author url          https://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /files/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Gears is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

/**
 *  extension helper.
 */
class CwGearsIptools {

    /**
     * Get users IP address
     * 
     * @return string
     */
    public static function getUserIP() {
        $ip = self::_real_getUserIP();

        if ((strstr($ip, ',') !== false) || (strstr($ip, ' ') !== false)) {
            $ip = str_replace(' ', ',', $ip);
            $ip = str_replace(',,', ',', $ip);
            $ips = explode(',', $ip);
            $ip = '';
            while (empty($ip) && !empty($ips)) {
                $ip = array_pop($ips);
                $ip = trim($ip);
            }
        } else {
            $ip = trim($ip);
        }

        return $ip;
    }

    /**
     * Gets the visitor's IP address
     *
     * @return string
     */
    private static function _real_getUserIP() {
        // Normally the $_SERVER superglobal is set
        if (isset($_SERVER)) {

            $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');

            foreach ($ip_keys as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (explode(',', $_SERVER[$key]) as $ip) {
                        // trim for safety measures
                        $ip = trim($ip);
                        // attempt to validate IP
                        if (self::validate_ip($ip)) {
                            return $ip;
                        }
                    }
                }
            }

            return self::validate_ip($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        }

        // This part is executed on PHP running as CGI, or on SAPIs which do
        // not set the $_SERVER superglobal
        // If getenv() is disabled, you're screwed
        if (function_exists('getenv')) {

            $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');

            foreach ($ip_keys as $key) {
                if (getenv($key)) {
                    foreach (explode(',', getenv($key)) as $ip) {
                        // trim for safety measures
                        $ip = trim($ip);
                        // attempt to validate IP
                        if (self::validate_ip($ip)) {
                            return $ip;
                        }
                    }
                }
            }

            return self::validate_ip(getenv('REMOTE_ADDR')) ? getenv('REMOTE_ADDR') : '';
        }

        // Catch-all case for broken servers, apparently
        return '';
    }

    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     */
    protected static function validate_ip($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
            return false;
        }
        return true;
    }

    /**
     * Is this an IPv6 IP address?
     *
     * @param string $ip An IPv4 or IPv6 address
     *
     * @return boolean  True if it's IPv6
     */
    public static function isIPv6($ip) {
        if (strstr($ip, ':')) {
            return true;
        }

        return false;
    }

    /**
     * Converts inet_pton output to bits string
     *
     * @param string $inet The in_addr representation of an IPv4 or IPv6 address
     *
     * @return string
     */
    protected static function inet_to_bits($inet) {
        if (strlen($inet) == 4) {
            $unpacked = unpack('A4', $inet);
        } else {
            $unpacked = unpack('A16', $inet);
        }
        $unpacked = str_split($unpacked[1]);
        $binaryip = '';

        foreach ($unpacked as $char) {
            $binaryip .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        return $binaryip;
    }

    /**
     * Checks if an IP is contained in a list of IPs or IP expressions
     *
     * @param string $ip      The IPv4/IPv6 address to check
     * @param array  $ipTable The list of IP expressions
     *
     * @return null|boolean  True if it's in the list, null if the filtering can't proceed
     */
    public static function ipinList($ip, $ipTable = array()) {
        // No point proceeding with an empty IP list
        if (empty($ipTable)) {
            return null;
        }

        // If no IP address is found, return null (unsupported)
        if ($ip == '0.0.0.0') {
            return null;
        }

        // If no IP is given, return null (unsupported)
        if (empty($ip)) {
            return null;
        }

        // Sanity check
        if (!function_exists('inet_pton')) {
            return null;
        }

        // Get the IP's in_adds representation
        $myIP = @inet_pton($ip);

        // If the IP is in an unrecognisable format, quite
        if ($myIP === false) {
            return null;
        }

        $ipv6 = self::isIPv6($ip);

        foreach ($ipTable as $ipExpression) {
            $ipExpression = trim($ipExpression);

            // Inclusive IP range, i.e. 123.123.123.123-124.125.126.127
            if (strstr($ipExpression, '-')) {
                list($from, $to) = explode('-', $ipExpression, 2);

                if ($ipv6 && (!self::isIPv6($from) || !self::isIPv6($to))) {
                    // Do not apply IPv4 filtering on an IPv6 address
                    continue;
                } elseif (!$ipv6 && (self::isIPv6($from) || self::isIPv6($to))) {
                    // Do not apply IPv6 filtering on an IPv4 address
                    continue;
                }

                $from = @inet_pton(trim($from));
                $to = @inet_pton(trim($to));

                // Sanity check
                if (($from === false) || ($to === false)) {
                    continue;
                }

                // Swap from/to if they're in the wrong order
                if ($from > $to) {
                    list($from, $to) = array($to, $from);
                }

                if (($myIP >= $from) && ($myIP <= $to)) {
                    return true;
                }
            }
            // Netmask or CIDR provided
            elseif (strstr($ipExpression, '/')) {
                $binaryip = self::inet_to_bits($myIP);

                list($net, $maskbits) = explode('/', $ipExpression, 2);
                if ($ipv6 && !self::isIPv6($net)) {
                    // Do not apply IPv4 filtering on an IPv6 address
                    continue;
                } elseif (!$ipv6 && self::isIPv6($net)) {
                    // Do not apply IPv6 filtering on an IPv4 address
                    continue;
                } elseif (!$ipv6 && strstr($maskbits, '.')) {
                    // Convert IPv4 netmask to CIDR
                    $long = ip2long($maskbits);
                    $base = ip2long('255.255.255.255');
                    $maskbits = 32 - log(($long ^ $base) + 1, 2);
                }

                // Convert network IP to in_addr representation
                $net = @inet_pton($net);

                // Sanity check
                if ($net === false) {
                    continue;
                }

                // Get the network's binary representation
                $binarynet = self::inet_to_bits($net);

                // Check the corresponding bits of the IP and the network
                $ip_net_bits = substr($binaryip, 0, $maskbits);
                $net_bits = substr($binarynet, 0, $maskbits);

                if ($ip_net_bits == $net_bits) {
                    return true;
                }
            } else {
                // IPv6: Only single IPs are supported
                if ($ipv6) {
                    $ipExpression = trim($ipExpression);

                    if (!self::isIPv6($ipExpression)) {
                        continue;
                    }

                    $ipCheck = @inet_pton($ipExpression);
                    if ($ipCheck === false) {
                        continue;
                    }

                    if ($ipCheck == $myIP) {
                        return true;
                    }
                } else {
                    // Standard IPv4 address, i.e. 123.123.123.123 or partial IP address, i.e. 123.[123.][123.][123]
                    $dots = 0;
                    if (substr($ipExpression, -1) == '.') {
                        // Partial IP address. Convert to CIDR and re-match
                        foreach (count_chars($ipExpression, 1) as $i => $val) {
                            if ($i == 46) {
                                $dots = $val;
                            }
                        }
                        switch ($dots) {
                            case 1:
                                $netmask = '255.0.0.0';
                                $ipExpression .= '0.0.0';
                                break;

                            case 2:
                                $netmask = '255.255.0.0';
                                $ipExpression .= '0.0';
                                break;

                            case 3:
                                $netmask = '255.255.255.0';
                                $ipExpression .= '0';
                                break;

                            default:
                                $dots = 0;
                        }

                        if ($dots) {
                            $binaryip = self::inet_to_bits($myIP);

                            // Convert netmask to CIDR
                            $long = ip2long($netmask);
                            $base = ip2long('255.255.255.255');
                            $maskbits = 32 - log(($long ^ $base) + 1, 2);

                            $net = @inet_pton($ipExpression);

                            // Sanity check
                            if ($net === false) {
                                continue;
                            }

                            // Get the network's binary representation
                            $binarynet = self::inet_to_bits($net);

                            // Check the corresponding bits of the IP and the network
                            $ip_net_bits = substr($binaryip, 0, $maskbits);
                            $net_bits = substr($binarynet, 0, $maskbits);

                            if ($ip_net_bits == $net_bits) {
                                return true;
                            }
                        }
                    }
                    if (!$dots) {
                        $ip = @inet_pton(trim($ipExpression));
                        if ($ip == $myIP) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Checks if an IP is contained in a list of IPs or IP expressions
     *
     * @param string $ip      The IPv4/IPv6 address to check
     * @param array  $ipTable The list of IP expressions
     *
     * @return null|boolean  True if it's in the list, null if the filtering can't proceed
     */
    public static function titleinList($ip, $ipTable = array()) {
        // No point proceeding with an empty IP list
        if (empty($ipTable)) {
            return null;
        }

        // If no IP address is found, return null (unsupported)
        if ($ip == '0.0.0.0') {
            return null;
        }

        // If no IP is given, return null (unsupported)
        if (empty($ip)) {
            return null;
        }

        // Sanity check
        if (!function_exists('inet_pton')) {
            return null;
        }

        // Get the IP's in_adds representation
        $myIP = @inet_pton($ip);

        // If the IP is in an unrecognisable format, quite
        if ($myIP === false) {
            return null;
        }

        $ipv6 = self::isIPv6($ip);

        foreach ($ipTable as $i => $ipExpression) {

            $ipExpression2 = trim($ipExpression['0']);
            $foo = '';

            // Inclusive IP range, i.e. 123.123.123.123-124.125.126.127
            if (strstr($ipExpression2, '-')) {
                list($from, $to) = explode('-', $ipExpression2, 2);

                if ($ipv6 && (!self::isIPv6($from) || !self::isIPv6($to))) {
                    // Do not apply IPv4 filtering on an IPv6 address
                    continue;
                } elseif (!$ipv6 && (self::isIPv6($from) || self::isIPv6($to))) {
                    // Do not apply IPv6 filtering on an IPv4 address
                    continue;
                }

                $from = @inet_pton(trim($from));
                $to = @inet_pton(trim($to));

                // Sanity check
                if (($from === false) || ($to === false)) {
                    continue;
                }

                // Swap from/to if they're in the wrong order
                if ($from > $to) {
                    list($from, $to) = array($to, $from);
                }

                if (($myIP >= $from) && ($myIP <= $to)) {
                    $foo = $ipExpression['1'];
                    return $foo;
                }
            }
            // Netmask or CIDR provided
            elseif (strstr($ipExpression2, '/')) {
                $binaryip = self::inet_to_bits($myIP);

                list($net, $maskbits) = explode('/', $ipExpression2, 2);
                if ($ipv6 && !self::isIPv6($net)) {
                    // Do not apply IPv4 filtering on an IPv6 address
                    continue;
                } elseif (!$ipv6 && self::isIPv6($net)) {
                    // Do not apply IPv6 filtering on an IPv4 address
                    continue;
                } elseif (!$ipv6 && strstr($maskbits, '.')) {
                    // Convert IPv4 netmask to CIDR
                    $long = ip2long($maskbits);
                    $base = ip2long('255.255.255.255');
                    $maskbits = 32 - log(($long ^ $base) + 1, 2);
                }

                // Convert network IP to in_addr representation
                $net = @inet_pton($net);

                // Sanity check
                if ($net === false) {
                    continue;
                }

                // Get the network's binary representation
                $binarynet = self::inet_to_bits($net);

                // Check the corresponding bits of the IP and the network
                $ip_net_bits = substr($binaryip, 0, $maskbits);
                $net_bits = substr($binarynet, 0, $maskbits);

                if ($ip_net_bits == $net_bits) {
                    $foo = $ipExpression['1'];
                    return $foo;
                }
            } else {
                // IPv6: Only single IPs are supported
                if ($ipv6) {
                    $ipExpression2 = trim($ipExpression2);

                    if (!self::isIPv6($ipExpression2)) {
                        continue;
                    }

                    $ipCheck = @inet_pton($ipExpression2);
                    if ($ipCheck === false) {
                        continue;
                    }

                    if ($ipCheck == $myIP) {
                        $foo = $ipExpression['1'];
                        return $foo;
                    }
                } else {
                    // Standard IPv4 address, i.e. 123.123.123.123 or partial IP address, i.e. 123.[123.][123.][123]
                    $dots = 0;
                    if (substr($ipExpression2, -1) == '.') {
                        // Partial IP address. Convert to CIDR and re-match
                        foreach (count_chars($ipExpression2, 1) as $i => $val) {
                            if ($i == 46) {
                                $dots = $val;
                            }
                        }
                        switch ($dots) {
                            case 1:
                                $netmask = '255.0.0.0';
                                $ipExpression2 .= '0.0.0';
                                break;

                            case 2:
                                $netmask = '255.255.0.0';
                                $ipExpression2 .= '0.0';
                                break;

                            case 3:
                                $netmask = '255.255.255.0';
                                $ipExpression2 .= '0';
                                break;

                            default:
                                $dots = 0;
                        }

                        if ($dots) {
                            $binaryip = self::inet_to_bits($myIP);

                            // Convert netmask to CIDR
                            $long = ip2long($netmask);
                            $base = ip2long('255.255.255.255');
                            $maskbits = 32 - log(($long ^ $base) + 1, 2);

                            $net = @inet_pton($ipExpression2);

                            // Sanity check
                            if ($net === false) {
                                continue;
                            }

                            // Get the network's binary representation
                            $binarynet = self::inet_to_bits($net);

                            // Check the corresponding bits of the IP and the network
                            $ip_net_bits = substr($binaryip, 0, $maskbits);
                            $net_bits = substr($binarynet, 0, $maskbits);

                            if ($ip_net_bits == $net_bits) {
                                $foo = $ipExpression['1'];
                                return $foo;
                            }
                        }
                    }
                    if (!$dots) {
                        $ip = @inet_pton(trim($ipExpression2));
                        if ($ip == $myIP) {
                            $foo = $ipExpression['1'];
                            return $foo;
                        }
                    }
                }
            }
        }

        return $foo;
    }

}
