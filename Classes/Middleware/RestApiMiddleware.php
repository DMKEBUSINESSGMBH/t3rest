<?php

declare(strict_types=1);

namespace DMK\T3rest\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RestApiMiddleware.
 *
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class RestApiMiddleware extends AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function processRestRequest(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $GLOBALS['TYPO3_REQUEST'] = $request;
        $this->getController()->execute();

        return $handler->handle($request);
    }

    /**
     * Returns an instance of a api controller.
     *
     * @return \Tx_T3rest_Controller_InterfaceController
     *
     * @throws \Exception
     */
    public function getController()
    {
        return \Tx_T3rest_Utility_Factory::getRestApiController();
    }
}
