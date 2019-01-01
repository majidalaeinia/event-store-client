<?php

/**
 * This file is part of `prooph/event-store-client`.
 * (c) 2018-2019 prooph software GmbH <contact@prooph.de>
 * (c) 2018-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal\Message;

use Prooph\EventStoreClient\Internal\NodeEndPoints;

/** @internal */
class EstablishTcpConnectionMessage implements Message
{
    /** @var NodeEndPoints */
    private $nodeEndPoints;

    public function __construct(NodeEndPoints $nodeEndPoints)
    {
        $this->nodeEndPoints = $nodeEndPoints;
    }

    public function nodeEndPoints(): NodeEndPoints
    {
        return $this->nodeEndPoints;
    }

    public function __toString(): string
    {
        return 'EstablishTcpConnectionMessage';
    }
}
