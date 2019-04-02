<?php 
namespace HospitalApi\Model;

use HospitalApi\Entity\OmbudsmanMessagesNotification;
use HospitalApi\Entity\Ombudsman;
use HospitalApi\Entity\User;

/**
 * <b>OmbudsmanMessagesNotificationModel</b>
 */
class OmbudsmanMessagesNotificationModel extends ModelAbstract
{

    public function __construct() {
        $this->entity = new OmbudsmanMessagesNotification();
        parent::__construct();
    }

    public static function plusOne(Ombudsman $ombudsman, User $user) {
        $model = new OmbudsmanMessagesNotificationModel();
        
        if($notification = $model->existRegister($ombudsman, $user)) {
            $notification->plusOne();

            $model->doUpdate($notification);
        } else {
            
            $model->entity->setOmbudsman( $model->em->getRepository('HospitalApi\Entity\Ombudsman')->find($ombudsman->getId()) );
            $model->entity->setUser( $model->em->getRepository('HospitalApi\Entity\User')->find($user->getId()) );

            $model->doInsert($model->entity);
        }
    }

    public function existRegister(Ombudsman $ombudsman, User $user) {
        return $this->getRepository()->findOneBy(['ombudsman' => $ombudsman->getId(), 'user' => $user->getId()]);
    }

    public static function checkLikeReadNotificationForUser(Ombudsman $ombudsman, User $user) {
        $model = new OmbudsmanMessagesNotificationModel();
        $notification = $model->existRegister($ombudsman, $user);
        
        $return = "";
        if($notification) {
            $return = $model->doDelete($notification);
        }
        return $return;
    }

    public function deleteNotifications($idOmbudsman) {
        $query = $this->em->createQueryBuilder();
        $query
            ->delete($this->getEntityPath(), 'nir')
            ->where('nir.ombudsman = :ombudsmanId')
            ->setParameter('ombudsmanId', $idOmbudsman);
        $query->getQuery()->execute();
    }

}
