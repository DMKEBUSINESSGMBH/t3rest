<?php
declare(strict_types = 1);

namespace DMK\T3rest\Middleware;

use DMK\T3rest\Request\BodyParserInterface;
use DMK\T3rest\Request\JsonBodyParser;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Tx_T3rest_Utility_Config;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tx_T3rest_Middleware_AuthController
 *
 * @package    TYPO3
 * @subpackage DMK\T3rest
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class AuthResolver implements MiddlewareInterface
{

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(
        \Psr\Http\Message\ServerRequestInterface $request,
        \Psr\Http\Server\RequestHandlerInterface $handler
    ): \Psr\Http\Message\ResponseInterface {

        // the hook is not enabled, skip!
        if (!Tx_T3rest_Utility_Config::isRestHookEnabled()) {
            return $handler->handle($request);
        }
        $requestBody = $this->getParsedBody($request);

        //TODO: If T3 would use the request object in fe user auth,
        //      we would not need to change POST here
        $_POST['user'] = $requestBody['user'];
        $_POST['pass'] = $requestBody['pass'];
        $_POST['logintype'] = $requestBody['logintype'];

        return $handler->handle($this->addFeUserPid($request));
    }

    /**
     * Return the parsed body.
     *
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    protected function getParsedBody(ServerRequestInterface $request)
    {
        return $this->getBodyParser()->parseBody($request);
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
                'pid' => Tx_T3rest_Utility_Config::getAuthUserStoragePid()
            ])
        );
    }

    /**
     * We expect JSON here so we use a JSON-BodyParser.
     *
     * @return BodyParserInterface
     */
    protected function getBodyParser(): BodyParserInterface
    {
        return GeneralUtility::makeInstance(JsonBodyParser::class);
    }
}
