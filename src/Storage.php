<?php


namespace Bot;

use MongoDB\Client;

class Storage
{
    static private $instance = false;
    private $to;
    private $from;
    private $client;
    private $db;

    private function __construct(){
        $request = Request::load();
        $this->to = $request->getTo();
        $this->from = $request->getFrom();
        $this->client = new Client($_ENV['MONGODB_STRING']);
        $this->db = $this->client->phpbot;
    }
    public function __destruct()
    {
        self::$instance = false;
    }

    /**
     * Get instance
     * @return Storage
     */
    public static function load() {
        if(!self::$instance) self::$instance = new Storage();
        return self::$instance;
    }

    /**
     * Get attrs by bot and user from mongodb
     * @return array
     */
    public function getAttr(): array {
        return (array) $this->db->users->findOne( [ 'bot' => $this->to, 'user' => $this->from ] );
    }

    /**
     * Set attrs by bot and user in mongodb
     */
    public function setAttr($attrs) {
        $attrs['bot'] = $this->to;
        $attrs['user'] = $this->from;
        $this->db->users->findOneAndUpdate(
            ['bot' => $this->to, 'user' => $this->from],
            ['$set' => $attrs],
            ['upsert' => true]
        );
    }

    /**
     * Delete attrs by bot and user in mongodb
     */
    public function delete() {
        $attrs['bot'] = $this->to;
        $attrs['user'] = $this->from;
        $this->db->users->deleteMany(
            ['bot' => $this->to, 'user' => $this->from]
        );
    }

    /**
     * Get all numbers to run in cron
     * @return array
     */
    public function getCronNumbers(): array
    {
        $result = $this->db->users->find( [ 'cron' => true ]);
        $results = [];
        foreach ($result as $entry) {
            $results[] = $entry;
        }
        return $results;
    }
}