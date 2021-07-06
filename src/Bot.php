<?php


namespace Bot;


use Bot\Session\Session;

class Bot
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Request
     */
    private $request;

    /**
     * Bot constructor.
     */
    private function __construct(){
        $this->session = Session::load();
        $this->storage = Storage::load();
        $this->request = Request::load();
        if(!$this->session) die();
    }

    /**
     * Get instance
     * @return Bot
     */
    static public function load(): Bot
    {
        return new Bot();
    }

    /**
     * run step logic
     * @return void
     */
    public function run(){
        if($this->request->getMessage() === 'reset'){
            $this->storage->delete();
            Response::sendText('Resetado', $this->session);
            die();
        }

        $step = $this->getStep();
        if (!@$step->retentativa){
            if(is_callable(@$step->func)) $step->func->__invoke($step);
            if(is_callable(@$step->replace)) $step->replace->__invoke($step);
            if(@$step->send !== false) Response::sendText($step->send, $this->session);
        } else {
            if(is_callable(@$step->replace)) $step->replace->__invoke($step);
            if(@$step->send !== false)  Response::sendText("Não entendi. " . $step->send, $this->session);
        }
    }

    /**
     * Get step by state
     * @todo create a interface Step
     */
    public function getStep()
    {
        $history = $this->session->getHistory();
        $step = $this->session->getFlow();

        if(count($history) > 1) {
            foreach ($history as $k => $s) {
                if($k === 0) continue;
                if(@$step->next[is_string($s) ? strtolower($s) : $s]){
                    //Caso exista a opção no menu
                    if($history[count($history) - 1] === $s && is_callable(@$step->next[$s]->validation) && !$step->next[$s]->validation->__invoke($step)){
                        array_pop($history);
                        $this->session->attrs['state'] = implode(',',$history);
                        $this->session->persist();
                        $step->retentativa = 1;
                        continue;
                    } else {
                        $step = $step->next[$s];
                    }
                } else if (@count($step->next) === 1) {
                    //Caso só exista 1 proxima etapa
                    if($history[count($history) - 1] === $s && is_callable(@$step->next[0]->validation) && !$step->next[0]->validation->__invoke($step)){
                        array_pop($history);
                        $this->session->attrs['state'] = implode(',',$history);
                        $this->session->persist();
                        $step->retentativa = 1;
                        continue;
                    } else {
                        $step = $step->next[0];
                    }
                } else if (@count($step->next) > 1) {
                    array_pop($history);
                    $this->session->attrs['state'] = implode(',',$history);
                    $this->session->persist();
                    $step->retentativa = 1;
                    continue;
                } else {
                    $step =  $this->session->getLastStep();
                }
            }
        }

        return $step;
    }
}