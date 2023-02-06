<?php

declare(strict_types=1);

namespace DMK\T3rest\Middleware;

use DMK\T3rest\Request\BodyParserInterface;
use DMK\T3rest\Request\JsonBodyParser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Tx_T3rest_Middleware_Abstract.
 *
 * @author     Michael Wagner
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractMiddleware implements MiddlewareInterface
{
    abstract protected function processRestRequest(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // if hook is not enabled or uri is not an api call, proceed with next handler!
        if (\Tx_T3rest_Utility_Config::isRestHookEnabled()) {
            $GLOBALS['TYPO3_REQUEST'] = $GLOBALS['TYPO3_REQUEST'] ?? $request;
            if ($this->isApiCall($request)) {
                return $this->processRestRequest($request, $handler);
            }
        }

        return $handler->handle($request);
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
        $apiSegment = ltrim(\Tx_T3rest_Utility_Config::getRestApiUriPathForSiteLanguage(), '/');

        return 0 === strpos($requestUri, $apiSegment);
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
        $result = [];
        try {
            $result = $this->getBodyParser()->parseBody($request);
        } catch (\InvalidArgumentException $argumentException) {
            \Sys25\RnBase\Utility\Logger::warn(
                sprintf(
                    '%s: could not parse body as JSON: %s',
                    __CLASS__,
                    $argumentException->getMessage()
                ),
                't3rest',
                [
                    'exception' => $argumentException,
                ]
            );
        }

        return $result;
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
