<?php


namespace Bot\Session;


use Bot\Request;
use Bot\Storage;

class Session
{
    public $to;
    public $from;
    public $history;
    public $attrs = [];
    public $request;
    public $storage;
    public $message;


    /**
     * Session constructor.
     */
    private function __construct()
    {
        $this->request = Request::load();
        $this->storage = Storage::load();
        $this->to = $this->request->getTo();
        $this->from = $this->request->getFrom();
        $this->message = $this->request->getMessage();

        $this->attrs = $this->storage->getAttr();
        $state = $this->attrs['state'] ?: '';
        if($this->message !== false){
            $state = $state . (strlen($state) > 0 ? "," : "") . str_replace(',', '', $this->message);
            $this->history = explode(',', $state);
            $this->attrs['state'] = $state;
            $this->persist();
        }
    }

    /**
     * Get Instance
     * @return false|mixed
     */
    public static function load(){
        $request = Request::load();

        $className = '\Bot\Session\\N' . $request->getTo();
        if(class_exists($className)){
            return new $className();
        }
        return false;
    }

    /**
     * Retorna o fluxo do bot
     * @return object
     */
    public function getFlow(){
        return (object)[
            'send' => 'Please configure flow',
            'func' => function($step){},
            'replace' => false,
            'skip' => false,
            'next' => []
        ];
    }

    /**
     * Retorna o estado o cliente
     * @return false|string[]
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Essa etapa será chamada quando o bot chegar ao fim, pode sobrescrever ela na classe do bot
     * @return object
     */
    public function getLastStep(){
        return (object)[
            'send' => false,
            'func' => function ($n){
                $this->attrs['state'] = "";
                $this->persist();
                return $n;
            },
            'next' => []
        ];
    }

    /**
     * Aqui acontece a logica do cron, essa func será chamada várias vezes por minuto,
     * calcule o intervalo de horas e o que deseja fazer por aqui.
     * @return bool
     */
    public function processaCron(): bool
    {
        return false;
    }

    /**
     * Salva o estado do cliente
     */
    public function persist(){
        $this->storage->setAttr($this->attrs);
    }

}