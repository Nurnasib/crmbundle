<?php


namespace Terminalbd\CrmBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Entity\DmsFile;

class DmsFileRepository extends EntityRepository
{

    public function insertAttachmentFile($sourceEntity, $data, $files, $bundle, $module)
    {

        $em = $this->_em;


        if (isset($files)) {
            $errors = array();
            foreach ($files['tmp_name'] as $key => $tmp_name) {

                if($tmp_name){

                    $fileName = $sourceEntity->getId() . '-' . time() . "-" . $files['name'][$key];
                    $fileType = $files['type'][$key];
                    $fileSize = $files['size'][$key];

                    if ($fileSize > DmsFile::FILESIZE) {
                        $errors[] = 'File size must be less than 2 MB';
                    }
                    if (empty($errors) == true and in_array($fileType, DmsFile::FILETYPE)) {
                        if (is_dir(DmsFile::DESTINATION.''.strtolower($module)) == false) {
                            mkdir(DmsFile::DESTINATION, 0777);        // Create directory if it does not exist
                            mkdir(DmsFile::DESTINATION.''.strtolower($module), 0777);        // Create directory if it does not exist
                        }
                        $image = new Image($files['tmp_name'][$key]);
                        $image->destination = DmsFile::DESTINATION.''.strtolower($module).'/'. $fileName;
                        $image->constraint = DmsFile::RESIZEBY;
                        $image->size = DmsFile::RESIZETO;
                        $image->quality = DmsFile::QUALITY;
                        $image->render();
                        $entity = new DmsFile();
                        $entity->setSourceId($sourceEntity->getId());
                        $entity->setBundle($bundle);
                        $entity->setModule($module);
                        if(isset($data['labelName'])&&$data['labelName'][$key]){
                            $entity->setName($data['labelName'][$key]);
                        }
                        $entity->setFileName($fileName);
                        $entity->setPath(DmsFile::DESTINATION.''.strtolower($module).'/');
                        $em->persist($entity);
                        $em->flush();
                    } elseif (empty($errors) == true and $fileType = "application/pdf") {
                        $file_tmp = $files['tmp_name'][$key];
                        if (is_dir(DmsFile::DESTINATION.''.strtolower($module)) == false) {
                            mkdir(DmsFile::DESTINATION, 0777);        // Create directory if it does not exist
                            mkdir(DmsFile::DESTINATION.''.strtolower($module), 0777);        // Create directory if it does not exist
                        }
                        if (is_dir(DmsFile::DESTINATION . $fileName) == false) {
                            move_uploaded_file($file_tmp, DmsFile::DESTINATION.''.strtolower($module).'/' . $fileName);
                        }
                        $entity = new DmsFile();
                        $entity->setSourceId($sourceEntity->getId());
                        $entity->setBundle($bundle);
                        $entity->setModule($module);
                        if(isset($data['labelName'])&&$data['labelName'][$key]){
                            $entity->setName($data['labelName'][$key]);
                        }
                        $entity->setFileName($fileName);
                        $entity->setPath(DmsFile::DESTINATION.''.strtolower($module).'/');
                        $em->persist($entity);
                        $em->flush();
                    } else {
                        return $errors;
                    }
                }

            }
        }
    }


    public function getDmsAttchmentFile($entity, $bundle, $module)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.created as created','e.name as name', 'e.fileName as fileName', 'e.path as filePath');
        $qb->where('e.sourceId = :sourceId')->setParameter('sourceId',"{$entity->getId()}");
        $qb->andWhere('e.bundle = :bundle')->setParameter('bundle',$bundle);
        $qb->andWhere('e.module = :module')->setParameter('module',$module);
        $result = $qb->getQuery()->getArrayResult();
        if($result){
            return $result;
        }
        return [];
    }

}