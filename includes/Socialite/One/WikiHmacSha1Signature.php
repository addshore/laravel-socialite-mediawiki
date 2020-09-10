<?php
/**
 * MIT License
 *
 * Copyright (c) 2020 Taavi Väänänen
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 */

namespace Taavi\LaravelSocialiteMediawiki\Socialite\One;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use League\OAuth1\Client\Signature\HmacSha1Signature;

class WikiHmacSha1Signature extends HmacSha1Signature
{
    /**
     * {@inheritDoc}
     */
    // Overriding this is a hack, and I don't like it. It's needed to include port in OAuth signature uri :/
    protected function baseString(UriInterface $url, $method = 'POST', array $parameters = array())
    {
        $baseString = rawurlencode($method).'&';

        $schemeHostPath = Uri::fromParts(array(
            'scheme' => $url->getScheme(),
            'host' => $url->getHost(),
            'path' => $url->getPath(),
            'port' => $url->getPort(),
        ));

        $baseString .= rawurlencode($schemeHostPath). '&';

        $data = array();
        parse_str($url->getQuery(), $query);
        $data = array_merge($query, $parameters);

        // normalize data key/values
        array_walk_recursive($data, function (&$key, &$value) {
            $key   = rawurlencode(rawurldecode($key));
            $value = rawurlencode(rawurldecode($value));
        });
        ksort($data);

        $baseString .= $this->queryStringFromData($data);

        return $baseString;
    }
}
