<?php

namespace App\Service;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

class ImportEditor
{
    public function __construct(private EntityManagerInterface $em){}

    /**
     * De-deplicate contact depending on idContact value
     */
    private function uniqueContact($array)
    {
        $data = array_values(
            array_combine(
                array_column($array, 'idContact'), 
                $array
            )
        );
        return json_decode(json_encode($data), true);
    }

    /**
     * Return formated number phone and valid contact
     */
    private function formatTel($phone)
    {
        $tel = str_replace([' ', '-', '/', '.'], '', $phone);
        $numbers = str_split($tel);

        if ( '+33' === $numbers[0].$numbers[1].$numbers[2] ) {
            $tel = str_replace('+33', ('0' !== $numbers[3] ? '0' :''), $tel);
        }
        if ( '0033' === $numbers[0].$numbers[1].$numbers[2].$numbers[3] ) {
            $tel = str_replace('0033', ('0' !== $numbers[4] ? '0' :''), $tel);
        }
        if ( 10 != strlen($tel) || !is_numeric($tel)
                || '0' !== str_split($tel)[0] || '8' === str_split($tel)[1] )
            return ['tel' => $phone, 'valid' => false];

        return ['tel' => $tel, 'valid' => true];
    }

    /**
     * Deduplicate & check database data
     */
    private function checkImport($data)
    {
        // Check contacts already imported
        $contacts = self::uniqueContact($data);
        
        //- Sort array by idContact key
        usort($contacts, function ($a, $b) { return $a['idContact'] > $b['idContact']; });

        $currents = $this->em->getRepository(Contact::class)->getExistingContacts();
        if (12 === count($currents)) return ['success' => false, 'msg' => '12 contacts ont déja été importés.'];

        //- Merge contacts
        if ( !empty($currents) ) {
            foreach ( (array)$currents as $newContact ){
                array_unshift($contacts, $newContact);
            }
            //- Deduplicate contacts;
            $contacts = self::uniqueContact($contacts);
            
            //- Sort array by idContact key
            usort($contacts, function ($a, $b) {return $a['idContact'] > $b['idContact'];});
    
            //- Remove existing contacts
            foreach( $contacts as $k => $contact ) {
                foreach( $currents as $curr ) {
                    if ($curr['idContact'] === $contact['idContact']) unset($contacts[$k]);
                }
            } 
            if (empty($contacts)) return ['success' => false, 'msg' => 'Tous les contacts ont déja été importés.'];    
        }         
        return $contacts;
    }

    /**
     * Insert new contacts
     */
    public function insertContact($data)
    {       
        if (empty($data)) return ['success' => false, 'msg' => 'Aucun contact à importer.'];
        
        $result = self::checkImport($data);

        if ( array_key_exists('success', $result) ) return $result;

        // Foreach until 12 with max 12 in database
        $length = count($result) > 12 ? 12 : count($result);
        $imports = array_slice($result, 0, $length) ;
        $imported = 0;

        try {
            foreach($imports as $import) {
                $status = null;
                $contact = new Contact();
                $imported++;
                
                // Set setters
                foreach($import as $key => $val) {

                    switch($key) {
                        case 'codePostal':
                            $property = 'cp';
                            $value = $val;
                            break;
                        case 'telephone':
                            $property = 'tel';
                            $newTel = self::formatTel($val);
                            $value = $newTel['tel'];
                            $status = ! $newTel['valid'] ? 'FAUX NUMERO' : null;
                            break;
                        default:
                            $property = $key;
                            $value = $val;
                    }
                    
                    $contact->{'set'.ucfirst($property)}($value);
                }
                $contact->setStatut($status);
                $this->em->getRepository(Contact::class)->save($contact, true);

                if ($imported === count($imports))
                    return [ 'success' => true, 'msg' => 'Nombre de contacts importés: '. $imported .'/'. count($result) .'.' ];    
            }
        } catch ( \Doctrine\DBAL\DBALException $e ) {
            return [ 'success' => false, 'msg' => $e->getMessage() ];
        }
    }
}