<?php

namespace App\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\Websocket\MessageHandler;
use Ratchet\App;
use Ratchet\Server\EchoServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsocketServerCommand extends Command
{
    protected static $defaultName = "run:websocket-server";

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $port = 3001;
        // $output->writeln("Starting server on port " . $port);
        // $server = IoServer::factory(
        //     new HttpServer(
        //         new WsServer(
        //             new MessageHandler()
        //         )
        //     ),
        //     $port,
        //     "0.0.0.0/chat"
        // );
        // $server->run();
        $app = new App("localhost", 8080, '0.0.0.0');
        $app->route('/chat', new MessageHandler, array('*'));
        $app->run();
        return 0;
    }
}
