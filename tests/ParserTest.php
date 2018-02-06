<?php
declare(strict_types=1);
namespace ParagonIE\Paseto\Tests;

use ParagonIE\ConstantTime\Hex;
use ParagonIE\Paseto\Exception\PasetoException;
use ParagonIE\Paseto\Keys\{
    AsymmetricSecretKey,
    SymmetricKey
};
use ParagonIE\Paseto\{
    Parser,
    Purpose
};
use ParagonIE\Paseto\Rules\NotExpired;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest
 * @package ParagonIE\Paseto\Tests
 */
class ParserTest extends TestCase
{
    /**
     * @covers Parser::parse()
     * @throws PasetoException
     */
    public function testAuthToken()
    {
        $key = new SymmetricKey('YELLOW SUBMARINE, BLACK WIZARDRY');
        // $nonce = crypto_generichash('Paragon Initiative Enterprises, LLC', '', 24);
        $nonce = Hex::decode('45742c976d684ff84ebdc0de59809a97cda2f64c84fda19b');

        $serialized = 'v2.local.3fNxan9FHjedQRSONRnT7Ce_KhhpB0NrlHwAGsCb54x0FhrjBfeNN4uPHFiO5H0iPCZSjwfEkkfiGeYpE6KAfr1Zm3G-VTe4lcXtgDyKATYULT-zLPfshRqisk4n7EbGufWuqilYvYXMCiYbaA';
        $parser = (new Parser())
            ->setPurpose(new Purpose('local'))
            ->setKey($key);
        $token = $parser->parse($serialized)
            ->setExplicitNonce($nonce);
        $this->assertSame(
            '2039-01-01T00:00:00+00:00',
            $token->getExpiration()->format(\DateTime::ATOM),
            'Mismatched expiration date/time'
        );
        $this->assertSame(
            'this is a signed message',
            $token->get('data'),
            'Custom claim not found'
        );
        $this->assertSame($serialized, (string) $token);

        $this->assertTrue($parser->validate($token));
        $parser->addRule(new NotExpired(new \DateTime('2007-01-01T00:00:00')));
        $this->assertTrue($parser->validate($token));

        $cloned = clone $parser;
        $cloned->addRule(new NotExpired(new \DateTime('2050-01-01T23:59:59')));
        $this->assertFalse($cloned->validate($token));

        try {
            $cloned->parse($serialized);
            $this->fail('Validation logic is being ignored.');
        } catch (PasetoException $ex) {
        }
        $parser->parse($serialized);

        // Switch to asymmetric-key crypto:
        $token->setPurpose(new Purpose('public'))
            ->setExplicitNonce($nonce)
            ->setKey(new AsymmetricSecretKey('YELLOW SUBMARINE, BLACK WIZARDRY'), true);
        $this->assertSame(
            'v2.public.eyJkYXRhIjoidGhpcyBpcyBhIHNpZ25lZCBtZXNzYWdlIiwiZXhwIjoiMjAzOS0wMS0wMVQwMDowMDowMCswMDowMCJ9BAOu3lUQMVHnBcPSkuORw51yiGGQ3QFUMoJO9U0gRAdAOPQEZFsd0YM_GZuBcmrXEOD1Re-Ila8vfPrfM5S6Ag',
            (string) $token,
            'Switching to signing caused a different signature'
        );
    }
}
