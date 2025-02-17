<?php
require 'vendor/autoload.php';
require_once('db_connect.php');
$conn = connect();

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketServer implements MessageComponentInterface {
  protected $clients;
  protected $clientMap;
  protected $type;

  public function __construct() {
    $this->clients = new \SplObjectStorage;
    $this->clientMap = [];
    $this->type = [];
  }

  public function onOpen(ConnectionInterface $conn) {
    $queryParams = $conn->httpRequest->getUri()->getQuery();
    parse_str($queryParams, $params);

    $clientID = isset($params['id']) ? $params['id'] : null;
    $type = isset($params['user_type']) ? $params['user_type'] : null;

    if ($clientID) {
      $this->clients->attach($conn);
      $this->clientMap[$clientID] = $conn;
      $this->type[$clientID] = $type;
      
      echo "New connection: ID={$clientID}, Resource ID={$conn->resourceId}, Type = {$type}\n";
    } else {
      $conn->close();
    }
  }

  public function onMessage(ConnectionInterface $from, $msg) {
    $data = json_decode($msg, true);
    $type = $data['user_type'];
    foreach($this->clientMap as $id => $client) {
      if($client != $from && $this->type[$id] != $type) {
        $client->send($msg);
      }
    }
  }

  public function onClose(ConnectionInterface $conn) {
    $this->clients->detach($conn);
    echo "Connection {$conn->resourceId} has disconnected\n";
  }

  public function onError(ConnectionInterface $conn, \Exception $e) {
    echo "An error has occurred: {$e->getMessage()}\n";
    $conn->close();
  }
}

use Ratchet\App;

$server = new App('localhost', 8080);
$server->route('/chat', new WebSocketServer, ['*']);
$server->run();
