<?php

/**
 * OAuthSignatureMethodHMACSHA1
 *
 * PHP version 5
 *
 * @category Deployment
 * @package  Application
 * @author   Urmas Lipso <urmas@mageflow.com>
 * @license  http://mageflow.com/license/mageflow.txt
 *
 */

namespace Mageflow\Connect\Model\Oauth\Signature;

use Mageflow\Connect\Model\AbstractModel;
use Mageflow\Connect\Model\Oauth\Consumer;
use Mageflow\Connect\Model\Oauth\Token;
use Mageflow\Connect\Model\Oauth\Request;
use Mageflow\Connect\Model\Oauth\Util;

/**
 * HMACSHA1 class
 *
 * @category Deployment
 * @package  Application
 * @author   Urmas Lipso <urmas@mageflow.com>
 * @license  http://mageflow.com/license/mageflow.txt
 *
 */
class HMACSHA1 extends AbstractModel implements SignatureBuilder
{

    protected $name = "HMAC-SHA1";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Needs to return the name of the Signature Method (ie HMAC-SHA1)
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Build up the signature
     * NOTE: The output of this function MUST NOT be urlencoded.
     * the encoding is handled in OAuthRequest when the final
     * request is serialized
     *
     * @param \Mageflow\Connect\Model\Oauth\Request|\Mageflow\Connect\Model\Oauth\Signature\OAuthRequest   $request
     * @param \Mageflow\Connect\Model\Oauth\Consumer|\Mageflow\Connect\Model\Oauth\Signature\OAuthConsumer $consumer
     * @param \Mageflow\Connect\Model\Oauth\Signature\OAuthToken|\Mageflow\Connect\Model\Oauth\Token       $token
     *
     * @return string
     */
    public function buildSignature(Request $request, Consumer $consumer,
        Token $token)
    {
        $base_string = $request->getSignatureBaseString();
        $request->setBaseString($base_string);

        $key_parts = array(
            $consumer->getSecret(),
            ($token) ? $token->getSecret() : ''
        );

        $key_parts = Util::urlencodeRFC3986($key_parts);
        $key = implode('&', $key_parts);

        return base64_encode(hash_hmac('sha1', $base_string, $key, true));
    }

    /**
     * Verifies that a given signature is correct
     *
     * @param Request $request
     * @param Consumer $consumer
     * @param Token $token
     * @param string $signature
     * @return bool
     */
    public function checkSignature(Request $request, Consumer $consumer,
        Token $token, $signature)
    {
        $builtSignature = $this->buildSignature($request, $consumer, $token);

        if ( strlen($builtSignature) == 0 || strlen($signature) == 0 )
        {
            return false;
        }

        if ( strlen($builtSignature) != strlen($signature) )
        {
            return false;
        }

        $result = 0;
        for ( $i = 0; $i < strlen($signature); $i++ )
        {
            $result |= ord($builtSignature{$i}) ^ ord($signature{$i});
        }

        return $result == 0;
    }

}