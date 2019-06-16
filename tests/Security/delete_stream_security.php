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

namespace ProophTest\EventStoreClient\Security;

use function Amp\call;
use function Amp\Promise\wait;
use Prooph\EventStore\Common\SystemRoles;
use Prooph\EventStore\Exception\AccessDenied;
use Prooph\EventStore\StreamMetadata;
use Throwable;

class delete_stream_security extends AuthenticationTestCase
{
    /**
     * @test
     * @throws Throwable
     */
    public function delete_of_all_is_never_allowed(): void
    {
        wait(call(function () {
            yield $this->expectExceptionFromCallback(AccessDenied::class, function () {
                return $this->deleteStream('$all', null, null);
            });
            yield $this->expectExceptionFromCallback(AccessDenied::class, function () {
                return $this->deleteStream('$all', 'user1', 'pa$$1');
            });
            yield $this->expectExceptionFromCallback(AccessDenied::class, function () {
                return $this->deleteStream('$all', 'adm', 'admpa$$');
            });
        }));
    }

    /**
     * @test
     * @throws Throwable
     * @doesNotPerformAssertions
     */
    public function deleting_normal_no_acl_stream_with_no_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()->build());

            yield $this->deleteStream($streamId, null, null);
        }));
    }

    /**
     * @test
     * @throws Throwable
     * @doesNotPerformAssertions
     */
    public function deleting_normal_no_acl_stream_with_existing_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()->build());

            yield $this->deleteStream($streamId, 'user1', 'pa$$1');
        }));
    }

    /**
     * @test
     * @throws Throwable
     * @doesNotPerformAssertions
     */
    public function deleting_normal_no_acl_stream_with_admin_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()->build());

            yield $this->deleteStream($streamId, 'adm', 'admpa$$');
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_user_stream_with_no_user_is_not_allowed(): void
    {
        wait(call(function () {
            yield $this->expectExceptionFromCallback(AccessDenied::class, function () {
                $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                    ->setDeleteRoles('user1')
                    ->build()
                );

                return $this->deleteStream($streamId, null, null);
            });
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_user_stream_with_not_authorized_user_is_not_allowed(): void
    {
        wait(call(function () {
            yield $this->expectExceptionFromCallback(AccessDenied::class, function () {
                $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                    ->setDeleteRoles('user1')
                    ->build()
                );

                return $this->deleteStream($streamId, 'user2', 'pa$$2');
            });
        }));
    }

    /**
     * @test
     * @throws Throwable
     * @doesNotPerformAssertions
     */
    public function deleting_normal_user_stream_with_authorized_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                ->setDeleteRoles('user1')
                ->build()
            );

            yield $this->deleteStream($streamId, 'user1', 'pa$$1');
        }));
    }

    /**
     * @test
     * @throws Throwable
     * @doesNotPerformAssertions
     */
    public function deleting_normal_user_stream_with_admin_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                ->setDeleteRoles('user1')
                ->build()
            );

            yield $this->deleteStream($streamId, 'adm', 'admpa$$');
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_admin_stream_with_no_user_is_not_allowed(): void
    {
        wait(call(function () {
            yield $this->expectExceptionFromCallback(AccessDenied::class, function () {
                $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                    ->setDeleteRoles(SystemRoles::ADMINS)
                    ->build()
                );

                return $this->deleteStream($streamId, null, null);
            });
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_admin_stream_with_existing_user_is_not_allowed(): void
    {
        wait(call(function () {
            yield $this->expectExceptionFromCallback(AccessDenied::class, function () {
                $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                    ->setDeleteRoles(SystemRoles::ADMINS)
                    ->build()
                );

                return $this->deleteStream($streamId, 'user1', 'pa$$1');
            });
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_admin_stream_with_admin_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                ->setDeleteRoles(SystemRoles::ADMINS)
                ->build()
            );

            yield $this->deleteStream($streamId, 'adm', 'admpa$$');
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_all_stream_with_no_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                ->setDeleteRoles(SystemRoles::ALL)
                ->build()
            );

            yield $this->deleteStream($streamId, null, null);
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_all_stream_with_existing_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                ->setDeleteRoles(SystemRoles::ALL)
                ->build()
            );

            yield $this->deleteStream($streamId, 'user1', 'pa$$1');
        }));
    }

    /**
     * @test
     * @throws Throwable
     */
    public function deleting_normal_all_stream_with_admin_user_is_allowed(): void
    {
        wait(call(function () {
            $streamId = yield $this->createStreamWithMeta(StreamMetadata::create()
                ->setDeleteRoles(SystemRoles::ALL)
                ->build()
            );

            yield $this->deleteStream($streamId, 'adm', 'admpa$$');
        }));
    }

    // $-stream

    // @todo write tests
}
