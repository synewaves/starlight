<?php
/*
 * This file is part of the Starlight framework.
 *
 * (c) Matthew Vince <matthew.vince@phaseshiftllc.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Starlight\Component\Http\Exception;


/**
 * IP Spoofing exception
 * @see \Starlight\Component\Http\Request::getRemoteIp()
 */
class IpSpoofingException extends \Exception
{
}