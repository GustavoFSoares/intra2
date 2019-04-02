<?php
namespace HospitalApi\Controller;

use HospitalApi\Model\OmbudsmanModel;
use HospitalApi\Model\OmbudsmanMessagesModel;
use HospitalApi\Model\OmbudsmanMessagesNotificationModel;
use HospitalApi\Template\Document\DocumentFactory;
use HospitalApi\Template\OmbudsmanNoticicationEmailTemplate;

/**
 * <b>OmbudsmanController</b>
 */
class OmbudsmanController extends ControllerAbstractLongEntity
{
    public function __construct() {
        parent::__construct(new OmbudsmanModel());
    }

    public function printDocumentAction($req, $res, $args) {
        $params = $req->getParams();
        $params['origin'] = $this->getEntityManager()->getRepository('HospitalApi\Entity\OmbudsmanOrigin')->find($params['origin']);

        new DocumentFactory('Ouvidorias', $params['page'], $params);
        
        exit;
    }

    public function getWaitingAction($req, $res, $args) {
        $params = $req->getParams();
        $data = $this->getModel()->getOmbudsmansWaiting($params);

        return $res->withJson($data);
    }

    public function setManagerResponseAction($req, $res, $args) {
        $values = $req->getParsedBody();
        $values['status'] = 'manager-received';

        $this->getModel()->setResponse($values);
        
        return $res->withJson(true);
    }

    public function setOmbudsmanResponseAction($req, $res, $args) {
        $values = $req->getParsedBody();
        $values['status'] = 'finished';

        $this->getModel()->setResponse($values);
        
        return $res->withJson(true);
    }

    public function addManagerAction($req, $res, $args) {
        $id = $req->getAttribute('id');
        $type = $req->getAttribute('type');

        $user = $req->getParsedBody();
        $response = $this->getModel()->addManager($id, $user, $type);

        return $res->withJson($response['status'], $response['code']);
    }
    
    public function removeManagerAction($req, $res, $args) {
        $id = $req->getAttribute('id');
        $type = $req->getAttribute('type');

        $user = $req->getParsedBody();
        $response = $this->getModel()->removeManager($id, $user, $type);

        return $res->withJson($response['status'], $response['code']);
    }

    public function insertChatAction($req, $res, $args) {
        $values = (object)$req->getParsedBody();

        $model = $this->getModel();
        $messagesModel = new OmbudsmanMessagesModel();
        $messageEntity = $messagesModel->mount($values);
        
        $result = $messagesModel->doInsert($messageEntity);
        
        try {
            $ombudsman = $messageEntity->getOmbudsman();
            $user = $messageEntity->getUser();
            
            \Helper\LoggerHelper::initLogFile('ombudsman-messages', "#".$ombudsman->getId(), 'ombudsman');

            $transmissionList = $model->getTransmissionList($ombudsman->getId(), $user->getId());
            
            $model->updateTransmissionList($ombudsman->getId(), $user, 'add');
            OmbudsmanMessagesNotificationModel::checkLikeReadNotificationForUser($ombudsman, $user);

            foreach ($transmissionList as $userReceiver) {
                OmbudsmanMessagesNotificationModel::plusOne($ombudsman, $userReceiver);

                $emailTemplate = new OmbudsmanNoticicationEmailTemplate($ombudsman, $user, $userReceiver);
                EmailController::sendEmailAction($emailTemplate);
            }
            $this->makeLog($messageEntity, $user);

        } catch(Exception $e) {
            $model = new \HospitalApi\Model\StatusMessageModel();
            $result = $model->getStatus('notification_not_send')->toArray();
            return $res->withJson($result, 401);
        }
        
        return $res->withJson($result);
    }

    public function makeLog($messageEntity, $user) {
        $logMessage = $messageEntity->getTime()->format('Y/m/d H:i:s')." ";
        $logMessage .= $user->getName()."(".$user->getGroup()->getName()."): ".$messageEntity->getMessage();
        \Helper\LoggerHelper::writeFile($logMessage);        
    }

    public function getChatsByOmbudsmanAction($req, $res, $args) {
        $values = $req->getParams();
        
        $id = $req->getParam('id');
        $this->storeUser($values);

        $messagesModel = new OmbudsmanMessagesModel();
        $data = $messagesModel->findMessagesByOmbudsman($id);
        return $res->withJson($data);
    }

    public function reprintAction($req, $res, $args) {
        $params = $req->getParams();
        $this->storeUser($params);
        new DocumentFactory('Ouvidorias', 1, $params);

        exit;
    }

    public function closeChatAction($req, $res, $args) {
        $model = $this->getModel();
        
        $values = $req->getParsedBody();
        $return = $this->getModel()->closeOmbudsman($values);
        if(!$return) {
            $res->withCode(500);
        }
        $messagesModel = new OmbudsmanMessagesModel();
        $messagesModel->deleteChats( $model->entity->getId() );

        return $res->withJson($return);
    }

    public function finishAction($req, $res, $args) {
        $values = $req->getParsedBody();
        $return = $this->getModel()->finishOmbudsman($values);

        return $res->withJson($return);
    }
    
}