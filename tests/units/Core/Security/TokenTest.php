<?php

require_once __DIR__.'/../../Base.php';

use Kanboard\Core\Security\Token;

class TokenTest extends Base
{
    public function testGenerateToken()
    {
        $t1 = Token::getToken();
        $t2 = Token::getToken();

        $this->assertNotEmpty($t1);
        $this->assertNotEmpty($t2);

        $this->assertNotEquals($t1, $t2);
    }

    public function testCSRFTokens()
    {
        $token = new Token($this->container);
        $t1 = $token->getCSRFToken();

        $this->assertNotEmpty($t1);
        $this->assertTrue($token->validateCSRFToken($t1));
        $this->assertFalse($token->validateCSRFToken($t1));
    }
}
