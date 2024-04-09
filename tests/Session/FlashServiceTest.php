<?php
namespace Tests\Session;

use Humbrain\Framework\sessions\FlashService;
use Humbrain\Framework\sessions\PhpSession;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{

    /**
     * @var PhpSession
     */
    private $session;

    /**
     * @var FlashService
     */
    private $flashService;

    public function setUp(): void
    {
        $this->session = new PhpSession();
        $this->flashService = new FlashService($this->session);
    }

    public function testDeleteFlashAfterGettingIt()
    {
        $this->flashService->success('Bravo');
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
    }
}
