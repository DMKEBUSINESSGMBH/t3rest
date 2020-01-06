<?php

declare(strict_types=1);

namespace DMK\T3rest\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface BodyParserInterface.
 *
 * @author     Mario Seidel <mario.seidel@dmk-ebusiness.com>
 * @license    http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
interface BodyParserInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function parseBody(ServerRequestInterface $request): array;
}
