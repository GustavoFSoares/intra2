<?php
namespace HospitalApi\Template;

/**
 * <b>OmbudsmanNoticicationEmailTemplate</b>
 */
class OmbudsmanNoticicationEmailTemplate extends EmailTemplateAbstract
{

    public function __construct($ombudsman, $userSender, $userReceiver) {
        $this
            ->setSubject($ombudsman)
            ->setSender(null)
            ->setReceiver($userReceiver->getEmail())
            ->setBody($ombudsman, ['sender'=>$userSender, 'receiver'=>$userReceiver]);
    }

    public function setSender($sender) {
        $sender = $this->getEmailDefault();
        $sender['name'] = 'Ouvidoria';

        $this->sender = $sender;
        return $this;
    }

    public function setSubject($ombudsman) {
        $this->subject = "#{$ombudsman->getId()} Notificação de Ouvidoria - {$ombudsman->getType()}";

        return $this;
    }
    
    public function setBody($ombudsman, $user) {
        $this->body = "
            <h2>#{$ombudsman->getId()} Ouvidoria - {$ombudsman->getType()}</h2>

            Prezado {$user['receiver']->getName()},
            <br><br>
            <b>{$user['sender']->getName()} (<i>{$user['sender']->getGroup()->getName()}</i>)</b> enviou uma nova mensagem no acompanhamento da Ouvidoria.

            <fieldset> 
                <div style='text-align: center'>
                    <a href=http://{$_SERVER['SERVER_NAME']}:8080/usuario/ouvidoria/detalhe/{$ombudsman->getId()}>
                        #{$ombudsman->getId()} Ouvidoria - {$ombudsman->getType()}
                    </a><br>
                    ou acesse<br>
                    <span> <b>Nova Intranet</b> > <b>Área do Usuário</b> > <b>Ouvidoria</b> > <b>Relatos</b> > <b>#\"Identificador da Ouvidoria\"</b> </span>
                </div>

            </fieldset>";
        
        return $this;
    }


}