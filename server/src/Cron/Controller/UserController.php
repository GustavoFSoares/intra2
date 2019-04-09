<?php
namespace Cron\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use HospitalApi\BasicApplicationAbstract;
use HospitalApi\Controller\ActiveDirectoryController;
use HospitalApi\Entity\User;
use Cocur\Slugify\Slugify;
/**
 * <b>ActiveDirectoryController</b>
 */
class UserController extends BasicApplicationAbstract
{

    public $model;
    public function __construct() {
        $this->model = new \Cron\Model\UserModel();
    }

    public function update() {
        $AD = new ActiveDirectoryController();
        $alph = ['a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l',
        'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '.'];
        
        \Cron\Model\RotineModel::startRotine('USER-AD', 'active-directory', 'cron/user/active-directory');
        echo \Helper\LoggerHelper::writeFile("Verificando...\n");
        foreach ($alph as $letter) {
            foreach ($alph as $secondLetter) {
                foreach ($alph as $thirdLetter) {
                    foreach ($alph as $fourthLetter) {
                        
                        $users = $AD->getUsers($letter.$secondLetter.$thirdLetter.$fourthLetter);
                        if($users['count']){
                            
                            foreach ($users as $user) {
                                if(is_array($user)) {

                                    $row = [
                                        'name' => isset($user['displayname'][0]) ? $user['displayname'][0] : '' ,
                                        'group' => $AD->getGroupArray(isset($user['department']) ? $user['department'][0] : '' ),
                                        'occupation' => isset($user['description'][0]) ? $user['description'][0] : '',
                                        'code' => isset($user['physicaldeliveryofficename'][0]) ? $user['physicaldeliveryofficename'][0] : 'sem-matricula',
                                    ];

                                    $id = $user['samaccountname']['0'];
                                    $active = $AD->isActive($id);

                                    $User = $this->model->findByNameOrCode($row['name'], $row['code'], $id);
                                    if($active) {
                                        /* Ativo e COM usuário cadastrado */
                                        if($User) {
                                            
                                            $group = $this->getRepositoryGroupById($row['group']['name']);
                                            if(!$group){
                                                echo \Helper\LoggerHelper::writeFile("$id --> {$row['group']['name']} <-- Ativo - Grupo nao encontrado\n");
                                            } else {

                                                $User
                                                    ->setActiveDirectory(true);
                                                $this->model->doUpdate($User);
                                                echo \Helper\LoggerHelper::writeFile("$id --> {$row['group']['name']} <-- Ativo - Atualizado\n");
                                            }

                                        /* Ativo e SEM usuário  cadastrado */
                                        } else {
                                            $row = [
                                                'id' => $user['samaccountname'][0],
                                                'name' => isset($user['displayname'][0]) ? $user['displayname'][0] : '' ,
                                                'group' => $AD->getGroupArray(isset($user['department']) ? $user['department'][0] : '' ),
                                                'occupation' => isset($user['description'][0]) ? $user['description'][0] : '',
                                                'code' => isset($user['physicaldeliveryofficename'][0]) ? $user['physicaldeliveryofficename'][0] : 'sem-matricula',
                                                'group' => $AD->getGroupArray(isset($user['department']) ? $user['department'][0] : '' ),
                                            ];
                                            $group = $this->getRepositoryGroupById($row['group']['name']);

                                            if(!$group){
                                                echo \Helper\LoggerHelper::writeFile("$id --> {$row['group']['name']} <-- Ativo - Grupo nao encontrado\n");
                                            } else {

                                                $User = new User();
                                                $User
                                                    ->setId($row['id'])
                                                    ->setCode($row['code'])
                                                    ->setName($row['name'])
                                                    ->setGroup($group)
                                                    ->setOccupation($row['occupation'])
                                                    ->setActiveDirectory(true);

                                                $this->model->doInsert($User);
                                                echo \Helper\LoggerHelper::writeFile("$id --> {$row['group']['name']} <-- Ativo - Adicionado\n");
                                            }

                                        }
                                    } else {

                                        /* Desativado e COM usuário cadastrado */
                                        if($User) {
                                            $User
                                                // ->setC_removed(true)
                                                ->setActiveDirectory(true);
                                            $this->model->doUpdate($User);
                                            echo \Helper\LoggerHelper::writeFile("$id --> {$row['group']['name']} <-- Desativado - Usuário Inativado\n");
                                        
                                        /* Desativado e SEM usuário cadastrado */
                                        } else {
                                            echo \Helper\LoggerHelper::writeFile("$id --> {$row['group']['name']} <-- Desativado - Ignorado\n");
                                        }
                                    }


                                }
                                
                            }

                        }
                        

                    }
                }
            }
        }
        
        \Cron\Model\RotineModel::endRotine();
    }

    public function adpIntegration() {
        $path = PATH.'/../files/adp/';
        
        $dir = scandir($path);
        $file = end($dir);

        \Cron\Model\RotineModel::startRotine('USER-ADP', $file, 'cron/user/adp');

        $this->integrateWithAdpFileAction($file, $path);
        $this->insertDuplicatedUsersAction();

        \Cron\Model\RotineModel::endRotine();
    }

    public function integrateWithAdpFileAction($file, $path) {
        $excel = new \Helper\ExcelHelper( pathinfo($file, PATHINFO_EXTENSION) );
        $excel->loadFile($path.$file);
        
        $GroupModel = new \Cron\Model\GroupModel();
        
        echo \Helper\LoggerHelper::writeFile("Arquivo analizado: $file\n");
        echo \Helper\LoggerHelper::writeFile("Verificando...\n");
        foreach ($excel->getRows() as $key => $data) {
            if( empty($data[0]) ) {
                continue;
            }
            if($key == 0) {
                foreach ($data as $key => $label) {
                    $label = \Helper\SlugHelper::get($label);
                    $index[$label] = $key;
                }
                
                continue;
            }
            
            $data[ $index['nome'] ]        = \Helper\SlugHelper::removeSpaces($data[ $index['nome'] ]);
            $data[ $index['funcao-nome'] ] = \Helper\SlugHelper::removeSpaces($data[ $index['funcao-nome'] ]);
            
            $User = $this->model->findByNameOrCode($data[$index['nome']], $data[$index['matricula']]);
            if($User) {
                
                $User
                    ->setCode( $data[$index['matricula']] )
                    ->setName( $data[$index['nome']] )
                    ->setOccupation( $data[$index['funcao-nome']] )
                    ->setByAdp( true );
                
                $groupId = $GroupModel->getGroupByGroupMappingFiles( $data[$index['centro-de-resultado-nome']] );
                if(!$groupId) {
                    echo \Helper\LoggerHelper::writeFile("$key - Usuário ({$data[ $index['nome'] ]}) - sem grupo cadastrado\n");
                    continue;
                }
                
                $group = $this->model->em->getRepository('HospitalApi\Entity\Group')->findOneByGroupId($groupId);
                
                if($group) {
                    $group = $this->model->em->getRepository('HospitalApi\Entity\Group')->find($group->getId());
                    $User->setGroup($group);

                    $this->model->doUpdate($User);
                    echo \Helper\LoggerHelper::writeFile("$key - ({$User->getCode()}) {$User->getId()} --> {$group->getName()} <-- Atualizado\n");
                } else {
                    
                    $this->saveIfGroupNotFound($data[$index['centro-de-resultado-nome']], $groupId, $User);
                    continue;
                    
                }

            } else {
                try {
   
                    $User = new User();
                    $User
                        ->setId( $this->model->makeId( $data[$index['nome']] ) )
                        ->setCode( $data[$index['matricula']] )
                        ->setName( $data[$index['nome']] )
                        ->setOccupation( $data[$index['funcao-nome']] )
                        ->setByAdp( true );
                    
                    $groupId = $GroupModel->getGroupByGroupMappingFiles( $data[$index['centro-de-resultado-nome']] );
                    
                    $group = $this->model->em->getRepository('HospitalApi\Entity\Group')->findOneByGroupId($groupId);
                    
                    if($group) {
                        $group = $this->model->em->getRepository('HospitalApi\Entity\Group')->find($group->getId());
                        $User->setGroup($group);

                        $this->model->doInsert($User);
                        echo \Helper\LoggerHelper::writeFile("$key - ({$User->getCode()}) {$User->getId()} --> {$group->getName()} <-- Inserido\n");
                    } else {
                        echo \Helper\LoggerHelper::writeFile("$key - ({$User->getCode()}) {$User->getId()} --> Grupo não encontrado <--\n");
                        $this->saveIfGroupNotFound($data[$index['centro-de-resultado-nome']], $groupId, $User);
                        continue;
                    }

                
                } catch (UniqueConstraintViolationException $e) {
                    $this->model->restartEntityPath();
                    $this->model->insertWithAnotherId($User);
                }
                
            }
        }
        
    }

    public function insertDuplicatedUsersAction() {
        \Cron\Model\DuplicatedUsersModel::cleanTable();
        $groupModel = new \Cron\Model\GroupModel();
        $rotine = \Cron\Model\RotineModel::getInstance()->entity;

        $i = 0;

        echo \Helper\LoggerHelper::writeFile("\n\n---Analistando usuários duplicados---\n");
        foreach ($groupModel->findGroupsId() as $key => $groupId) {
            
            foreach ($this->model->getProblablyDuplicatedUsers($groupId) as $match) {
                
                $user1 = [ 'id' => $match['UserId1'], 'name' => $match['UserName1'], 'code' => $match['UserCode1'], ];
                $user2 = [ 'id' => $match['UserId2'], 'name' => $match['UserName2'], 'code' => $match['UserCode2'], ];

                $user1['name'] = explode('-', \Helper\SlugHelper::get($user1['name']) );
                $user2['name'] = explode('-', \Helper\SlugHelper::get($user2['name']) );

                $matching = 0;
                if($this->model->isAvailableCode($user1['code'], $user2['code']))  {
                    if($user1['code'] == $user2['code']) {

                        $this->getModel()->mergeUsers($user1['id'], $user2['id']);
                        echo \Helper\LoggerHelper::writeFile("{$user1['id']} <-> {$user2['id']} - Unidos\n");

                    } else {
                        continue;
                    }
                } else {
                    $matching = $this->model->getMatcingsByName($user1['name'], $user2['name']);
                }

                if($matching >= 2) {
                    echo \Helper\LoggerHelper::writeFile("{$user1['id']} <-> {$user2['id']} - Compativel\n");

                    \Cron\Model\DuplicatedUsersModel::addUsers($user1['id'], $user2['id'], $rotine);
                }
            }

        }
        
    }

    public function getModel() {
        return new \HospitalApi\Model\UserModel();
    }

    public function getRepositoryGroupById($id) {
        $groupRepository = $this->model->em->getRepository('HospitalApi\Entity\Group');
        $group = $groupRepository->findOneByGroupId(\Helper\SlugHelper::get($id));
        
        return $group;
    }

    public function saveIfGroupNotFound($adpGroup, $groupId, $User) {
        $groupId = $groupId ? $groupId : "undefined";
        $adpgroup = \Helper\SlugHelper::get($adpGroup);

        $notFoundGroup = "{$adpgroup}: \"{$groupId}\"\n";

        file_put_contents(PATH."/Cron/MappingGroup.yml", "    $notFoundGroup", FILE_APPEND);
        echo \Helper\LoggerHelper::writeFile("{$User->getId()} --> $groupId <-- Grupo não encontrado\n");
    }

}