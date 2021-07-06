<?php


namespace Bot\Session;


use Bot\Response;
use Bot\Storage;
use DateTime;
use DateTimeZone;

class N554185363964 extends Session
{
    const SERVER_URL = 'http://localhost:21465/api/bot';
    const SERVER_KEY = '$2b$10$PaxaikJuEuAIJNfqOwQh3.YIVz.uZIdI6qCT.BxeYBWFtFobSjPTO';
    /**
     * Mensagem para enviar no cron
     * @var string[]
     */
    private $messages = [
        'Vai uma água ai?💦',
        'Se manter hidratado no inverno também é  importante, perdemos líquido sem perceber.',
        '🍶 Beba água!',
        'Keep calm and beba água'
    ];

    /**
     * Fluxo da conversa
     * @todo criar interface step
     * @return object
     */
    public function getFlow(){
        if(@$this->attrs['registred'] !== true){
            return $this->getNewUsersFlow();
        } else {
            return $this->getRegistredUsersFlow();
        }
    }

    /**
     * Fluxo da conversa para novos users
     * @return object
     */
    public function getNewUsersFlow(){
        return (object)[
            'validation' => false,
            'send' => 'Exemplo de menu! Ex: 1\n1 - Sim\n2 - Não',
            'func' => function ($step){
                Response::sendText('Oi!\nEu irei de dar alguns exemplos de como me usar.', $this);
            },
            'replace' => false,
            'skip' => false,
            'next' => [
                1 => (object) [
                    'validation' => false,
                    'send' => 'Como deseja ser chamado? Ex: Bea',
                    'func' => function ($step){
                        Response::sendText('Você selecionou 1!', $this);
                    },
                    'replace' => false,
                    'skip' => function ($step){
                        //@todo Skip caso já tenhamos o nome da pessoa
                    },
                    'next' => [
                        0 => (object) [
                            'validation' => function($step){
                                if (strlen ($this->message) > 60) return false;
                                else return true;
                            },
                            'send' => 'Qual sua idade? Ex: 26',
                            'func' => function ($step){
                                // Valida NOME
                                if(strpos($this->message," ") !== false){
                                    $name = explode(' ', $this->message);
                                    if (count($name) < 2) $this->attrs['name'] = ucfirst($name[0]);
                                    else $this->attrs['name'] = ucfirst($name[0]) . ' ' . ucfirst($name[1]);
                                } else {
                                    $this->attrs['name'] = ucfirst($this->message) ;
                                }

                                $this->persist();
                            },
                            'replace' => false,
                            'skip' => false,
                            'next' => [
                                0 => (object) [
                                    // Valida a IDADE
                                    'validation' => function($step){
                                        if (is_numeric($this->message) && $this->message < 100) return true;
                                        else return false;
                                    },
                                    'send' => 'Qual seu peso? Ex: 84',
                                    'func' => function ($step){
                                        Response::sendText('Chegamos ao final do cadastro', $this);
                                        Response::sendText($this->attrs['name'] . ', também adicionamos você a fila do cron!', $this);

                                        $dateNow = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
                                        $this->attrs['last_shot'] = $dateNow->format('d-m-Y H:i:s');
                                        $this->attrs['state'] = "";
                                        $this->attrs['cron'] = true;
                                        $this->attrs['registred'] = true;
                                        $this->attrs['peso'] = $this->message;
                                        $this->persist();
                                    },
                                    'replace' => false,
                                    'skip' => false,
                                    'next' => []
                                ]
                            ]
                        ]
                    ]
                ],
                2 => (object) [
                    'send' => 'Ok!\nVocê selecionou 2. Envie outra mensagem para começar de novo. 😉',
                    'func' => function($step){
                        $this->storage->delete();
                    },
                    'replace' => false,
                    'skip' => false,
                    'next' => []
                ]
            ]
        ];
    }

    /**
     * Fluxo da conversa users registrados
     * @return object
     */
    private function getRegistredUsersFlow() {
        return (object)[
            'send' => 'Selecione uma das opções:\n' .
                '1 - Editar nome.\n' .
                '2 - Parar de receber notificações.',
            'func' => function ($step){},
            'replace' => function ($step){},
            'skip' => function ($step){},
            'validation' => false,
            'next' => [
                1 => (object) [
                    'send' => 'Como deseja ser chamado? Ex: Bea',
                    'func' => function($step){},
                    'replace' => false,
                    'skip' => false,
                    'validation' => false,
                    'next' => [
                        0 => (object) [
                            'validation' => function($step){
                                if (strlen ($this->message) > 60) return false;
                                else return true;
                            },
                            'send' => false,
                            'func' => function ($step){
                                // Valida NOME
                                if(strpos($this->message," ") !== false){
                                    $name = explode(' ', $this->message);
                                    if (count($name) < 2) $this->attrs['name'] = ucfirst($name[0]);
                                    else $this->attrs['name'] = ucfirst($name[0]) . ' ' . ucfirst($name[1]);
                                } else {
                                    $this->attrs['name'] = ucfirst($this->message) ;
                                }
                                $this->attrs['state'] = '';
                                $this->persist();
                                Response::sendText("Ok, vou te chamar de {$this->attrs['name']}!", $this);
                                },
                            'replace' => false,
                            'skip' => false,
                            'next' => []
                        ]
                    ]
                ],
                2 => (object) [
                    'send' => false,
                    'func' => function($step){
                        $this->storage->delete();
                        Response::sendText('Ok 😢, qualquer coisa, chama.', $this);
                    },
                    'validation' => false,
                    'replace' => false,
                    'skip' => false,
                    'next' => []
                ]
            ]
        ];
    }

    /**
     * Aqui acontece a logica do cron, essa func será chamada várias vezes por minuto,
     * calcule o intervalo de horas e o que deseja fazer por aqui.
     * @return bool
     */
    public function processaCron():bool{
        $enviar = false;
        $dateNow = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));

        if(@$this->attrs['last_shot']){
            $lastDate = new DateTime($this->attrs['last_shot'], new DateTimeZone('America/Sao_Paulo'));
            if($this->checkShotTime($dateNow,$lastDate)) $enviar = true;
        }

        if($enviar){
            $messageToSend = $this->messages[array_rand($this->messages)];
            Response::sendText($messageToSend, $this);
            $this->attrs['last_shot'] = $dateNow->format('d-m-Y H:i:s');
            $storage = Storage::load();
            $storage->setAttr($this->attrs);
        }

        return $enviar;
    }

    /**
     * Checa se está na hora de enviar uma mensagem
     * @param DateTime $currentTime
     * @param DateTime $lastShotTime
     * @return bool
     */
    private function checkShotTime(DateTime $currentTime, DateTime $lastShotTime) : bool
    {
        $startTime = new DateTime($this->attrs['schedule_start'], new DateTimeZone('America/Sao_Paulo'));
        $endTime = new DateTime($this->attrs['schedule_end'], new DateTimeZone('America/Sao_Paulo'));
        $delay = 30;

        if (!$delay || $delay == 0) return false; //@todo fix
        $lastShotTime->modify("+$delay minutes");
        return $currentTime > $lastShotTime && $currentTime >= $startTime && $currentTime <= $endTime;

    }
}
