<?php
namespace App\Shell;

use Cake\Console\Shell;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use App\Controller\WebSocketController;
use Ratchet\WebSocket\WsServer;
use Cake\Controller\ComponentRegistry;

/**
 * WebSocket shell command.
 */
class WebSocketShell extends Shell
{

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
		$server = IoServer::factory(
			new HttpServer(
				new WsServer(
					new WebSocketController()
				)
			),
			2020
		);
		$this->out('Server running on port '.$server->socket->getPort());
		$server->run();

    }
}
