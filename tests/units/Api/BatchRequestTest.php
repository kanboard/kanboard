<?php

namespace KanboardTests\units\Api;

use JsonRPC\Exception\AccessDeniedException;
use JsonRPC\Exception\AuthenticationFailureException;
use JsonRPC\Server;
use PHPUnit\Framework\TestCase;

class BatchRequestTest extends TestCase
{
    public function testForbiddenProcedureOnlyFailsItsOwnRequest()
    {
        $server = new Server('[
            {"jsonrpc": "2.0", "method": "getVersion", "id": 1},
            {"jsonrpc": "2.0", "method": "getProjectById", "id": 2},
            {"jsonrpc": "2.0", "method": "getVersion", "id": 3}
        ]');

        $server->register('getVersion', function () {
            return '1.2.3';
        });

        $server->register('getProjectById', function () {
            throw new AccessDeniedException('Forbidden');
        });

        $this->assertEquals(
            json_decode('[
                {"jsonrpc": "2.0", "result": "1.2.3", "id": 1},
                {"jsonrpc": "2.0", "error": {"code": 403, "message": "Forbidden"}, "id": 2},
                {"jsonrpc": "2.0", "result": "1.2.3", "id": 3}
            ]', true),
            json_decode($server->execute(), true)
        );
    }

    public function testWrongCredentialsStillFailTheWholeBatch()
    {
        $server = new Server('[
            {"jsonrpc": "2.0", "method": "getVersion", "id": 1},
            {"jsonrpc": "2.0", "method": "getProjectById", "id": 2}
        ]');

        $server->register('getVersion', function () {
            return '1.2.3';
        });

        $server->register('getProjectById', function () {
            throw new AuthenticationFailureException('Wrong credentials');
        });

        $this->assertEquals(
            json_decode('{"jsonrpc": "2.0", "error": {"code": 401, "message": "Unauthorized"}, "id": null}', true),
            json_decode($server->execute(), true)
        );
    }
}
