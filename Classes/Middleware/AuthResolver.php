<?php

declare(strict_types=1);

namespace DMK\T3rest\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Class Tx_T3rest_Middleware_AuthController.
 *
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class AuthResolver extends AbstractMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function processRestRequest(
        ServerRequestInterface $request,
        \Psr\Http\Server\RequestHandlerInterface $handler
    ): \Psr\Http\Message\ResponseInterface {
        // auth nach redirect herstellen
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =
                explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)), 2);
        }

        // there are user and pwd, so we have to reauth by this data
        if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
            $_POST['user'] = $_SERVER['PHP_AUTH_USER'];
            $_POST['pass'] = $_SERVER['PHP_AUTH_PW'];
            $_POST['logintype'] = 'login';
        } else {
            $requestBody = $this->getParsedBody($request);

            // TODO: If T3 would use the request object in fe user auth,
            //      we would not need to change POST here.
            $_POST['user'] = $requestBody['user'] ?? '';
            $_POST['pass'] = $requestBody['pass'] ?? '';
            $_POST['logintype'] = $requestBody['logintype'] ?? '';
        }

        return $handler->handle($this->addFeUserPid($request));
    }

    /**
     * Check if API URI is first occurrence in request URI path.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    protected function isApiCall(ServerRequestInterface $request)
    {
        $requestUri = ltrim($request->getUri()->getPath(), '/');
        $apiSegment = ltrim(\Tx_T3rest_Utility_Config::getRestApiUriPath(), '/');

        return 0 === strpos($requestUri, $apiSegment);
    }

    /**
     * Add the storage pid for fe users configured in the extension configuration.
     *
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    protected function addFeUserPid(ServerRequestInterface $request)
    {
        return $request->withParsedBody(
            array_merge($request->getParsedBody() ?: [], [
                'pid' => \Tx_T3rest_Utility_Config::getSignedAuthUserStoragePid(),
            ])
        );
    }
}
