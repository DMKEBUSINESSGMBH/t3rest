<?php

namespace DMK\T3rest\Tests\Unit\Middleware;

use DMK\T3rest\Middleware\AuthResolver;
use GuzzleHttp\Psr7\Stream;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * Class AuthResolverTest.
 *
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class AuthResolverTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['t3rest'] = [
            'disableCookie' => '0',
            'restApiController' => 'Tx_T3rest_Controller_Json',
            'restApiRouter' => 'Tx_T3rest_Router_Respect',
            'restApiUriPath' => 't3rest',
            'restAuthUserStoragePid' => '4',
            'restEnableHook' => '1',
        ];
    }

    /**
     * @test
     */
    public function testProcess()
    {
        $authMiddleware = new AuthResolver();
        $fp = fopen('php://memory', 'w+');
        $body = new Stream($fp);
        $body->write('{"user": "foo", "pass": "pass:word", "logintype": "login"}');
        $body->rewind();

        $request = new ServerRequest('/t3rest/login', 'POST', $body);
        $requestHandler = new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new JsonResponse(
                    [
                        'body' => $request->getParsedBody(),
                    ]
                );
            }
        };
        $response = $authMiddleware->process($request, $requestHandler);
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertSame('{"body":{"pid":"4@74dbee593db1fe3bd77ba6cc190c0cefe4a078bf"}}', $response->getBody()->getContents());
        $this->assertSame('foo', $_POST['user']);
        $this->assertSame('pass:word', $_POST['pass']);
        $this->assertSame('login', $_POST['logintype']);

        fclose($fp);
    }

    /**
     * @test
     */
    public function testNoProcessIfUriDoesNotMatch()
    {
        $request = new ServerRequest('/not/a/rest/api/endpoint', 'GET');
        $requestHandler = new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new NullResponse();
            }
        };
        $authMiddleware = $this->getMockBuilder(AuthResolver::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParsedBody'])
            ->getMock();
        $authMiddleware
            ->expects($this->never())
            ->method('getParsedBody');

        $response = $authMiddleware->process($request, $requestHandler);

        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertSame('', $response->getBody()->getContents());
    }

    /**
     * @test
     */
    public function testProcessIfRedirect()
    {
        $authMiddleware = new AuthResolver();
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'basic '.base64_encode('foo:pass:word');

        $request = new ServerRequest('/t3rest/login', 'POST');
        $requestHandler = new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new JsonResponse(
                    [
                        'body' => $request->getParsedBody(),
                    ]
                );
            }
        };
        $response = $authMiddleware->process($request, $requestHandler);
        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertSame('{"body":{"pid":"4@74dbee593db1fe3bd77ba6cc190c0cefe4a078bf"}}', $response->getBody()->getContents());
        $this->assertSame('foo', $_POST['user']);
        $this->assertSame('pass:word', $_POST['pass']);
        $this->assertSame('login', $_POST['logintype']);
    }
}
