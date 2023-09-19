<?php declare(strict_types=1);

namespace seregazhuk\PhpWatcher\Tests\Feature;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Loop;
use seregazhuk\PhpWatcher\Config\WatchList;
use seregazhuk\PhpWatcher\Filesystem\ResourceWatcherBased\ChangesListener;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\Filesystem;
use seregazhuk\PhpWatcher\Tests\Feature\Helper\WithFilesystem;
use function React\Async\delay;

final class ChangesListenerTest extends TestCase
{
    use WithFilesystem;

    /** @test */
    public function it_emits_change_event_on_changes(): void
    {
        $loop = Loop::get();
        $listener = new ChangesListener($loop);
        $listener->start(new WatchList([Filesystem::fixturesDir()]));

        $loop->addTimer(1, [Filesystem::class, 'createHelloWorldPHPFile']);
        $eventWasEmitted = false;
        $listener->on('change', static function () use (&$eventWasEmitted) {
            $eventWasEmitted = true;
        });
        delay(4); // to be sure changes have been detected

        $this->assertTrue($eventWasEmitted, '"change" event should be emitted');
    }
}
