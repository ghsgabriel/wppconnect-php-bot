<?php


namespace Bot;


class Request
{
    /**
     * request data received from Wppconnect server webhook
     * @var object
     */
    private $data;

    static private $instance = false;

    /**
     * Request constructor.
     */
    private function __construct()
    {
        $this->data = @json_decode(@file_get_contents('php://input')) ?: (object)[];
    }

    /**
     * Get instance
     * @return Request
     */
    public static function load(): Request
    {
        if (!self::$instance){
            self::$instance = new Request();
        }

        return self::$instance;
    }

    /**
     * Change param
     * @param $key
     * @param $val
     */
    public function setParam($key, $val)
    {
        $this->data->{$key} = $val;
    }

    /**
     * Save all request data in file
     */
    public function logRequest()
    {
        $myFile = __DIR__ . "/../requestslog.txt";
        $fh = fopen($myFile, 'a') or die("can't open file");
        fwrite($fh, "\n\n---------------------------------------------------------------\n");
        fwrite($fh, print_r($this->data, 1));
        fclose($fh);
    }

    /**
     * Return string content of message
     * @return string
     */
    public function getMessage(): string
    {
        return @$this->data->content;
    }

    /**
     * Return string event of message
     * @return string
     */
    public function getEvent(): string
    {
        return @$this->data->event;
    }

    /**
     * Return bot number
     * @return string
     */
    public function getTo(): string
    {
        return @filter_var(@$this->data->to, FILTER_SANITIZE_NUMBER_INT);;
    }

    /**
     * Return contact number
     * @return string
     */
    public function getFrom(): string
    {
        return @filter_var(@$this->data->from, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Return if message is media
     * @return bool
     */
    public function getIsMedia(): bool
    {
        return @$this->data->isMedia;
    }
}