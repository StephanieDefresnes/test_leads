<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Service\RequestApi;
use App\Service\ImportEditor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private RequestApi $requestApi,
        private ImportEditor $editor,
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameters,
    ){}

    /**
     * Import from API
     */
    #[Route('/ajaxImport')]
    public function ajaxImport(): JsonResponse
    {
        $api_response = $this->requestApi->loginApi();

        if ( is_array($api_response) && array_key_exists('code', $api_response) ) {
            return $this->json([
                'success' => false,
                'msg' => $api_response['message'],
            ]);
        }
        try {
            $response = $this->requestApi->callApi( 'GET', 'api/listContacts', $api_response['token'] );
            return $this->json( $this->editor->insertContact( json_decode($response->getContent()) ) );

        } catch (\Exception $e) {
            return $this->json([ 'success' => false, 'msg' => $e->getMessage() ]);
        }
    }

    /**
     * Campaign simulation
     */
    #[Route('/ajaxCampaign')]
    public function ajaxCampaign(): JsonResponse
    {
        {
            $statuts = ['NRP/OQP', 'REFUS DE REPONDRE', 'BARRAGE SECRETAIRE', 'RAPPEL', 'PROJET AVEC RAPPEL COMMERCIAL'];
            $echeances = ['Moins de 3 mois', 'Entre 3 et 6 mois'];
    
            $contacts = $this->em->getRepository(Contact::class)->getCampainContacts();
            if(empty($contacts)) return $this->json( ['success' => false, 'msg' => 'Aucun contact à traiter.'] );
    
            $campain_result = [];
            $updated = 0;
            foreach ($contacts as $contact){
                $statut         = $statuts[rand(0, count($statuts) -1)];
                $echeanceProjet = 'PROJET AVEC RAPPEL COMMERCIAL' === $statut ? $echeances[rand(0, count($echeances) -1)] : null;
    
                $alph = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; $num = '0123456789';
                $code = substr(str_shuffle($alph), 0, 2).substr(str_shuffle($num), 0, 3);
                $codeConcession = 'PROJET AVEC RAPPEL COMMERCIAL' === $statut ? $code : null;
    
                $contact->setCampagne('AUDI_Q3_MARS_2022');
                $contact->setEcheanceProjet($echeanceProjet);
                $contact->setCodeConcession($codeConcession);
                $contact->setStatut($statut);
                if ('PROJET AVEC RAPPEL COMMERCIAL' === $statut) $contact->setModele('Q4');
                $this->em->persist($contact);
            }
    
            try {
                $this->em->flush();
                return $this->json( ['success' => true, 'msg' => 'Les contacts de la campagne ont été mis à jour.'] );
            } catch(Exception $e) {
                return $this->json( ['success' => false, "Une erreur s'est produite lors de la mise à jour des contacts."] );
            }
        }
    }

    /**
     * Sends lead to API
     */
    #[Route('/ajaxSendLeads')]
    public function ajaxSendLeads(): JsonResponse
    {
        $contacts = $this->em->getRepository(Contact::class)->getLeads();
        if(empty($contacts)) return $this->json( [['success' => false, 'msg' => 'Auncun nouveau lead à transmettre au client.']] );

        $api_response = $this->requestApi->loginApi();

        if ( array_key_exists('code', $api_response) ) {
            return $this->json([
                'success' => false,
                'msg' => $api_response['message'],
            ]);
        }
        
        $result = [];
        $count = 0;

        foreach ($contacts as $c){
            $status = false;
            do {
                try {
                    $lead_response = $this->requestApi->callApi("POST", 'api/lead', $api_response['token'], $c, true);
                    $response = json_decode($lead_response->getContent(), true);
                    
                    // Save lead already sent
                    if ( is_array($response) && array_key_exists('status', $response) && 201 === $response['status']) {
                        $status = true;
                        $count++;

                        $contact = $this->em->getRepository(Contact::class)->findOneBy(['idContact' => $c['idLead']]);
                        $contact->setTransmitted(true);
                        $this->em->persist($contact);
                        
                        try {
                            $this->em->flush();
                            $result[] = ['success' => true, 'msg' => $response['message'] . ': #'. $contact->getIdContact().'.'];
                        } catch(Exception $e) {
                            $result[] = ['success' => false, 'msg' => $response['message'] . ", mais une erreur s'est produite lors de la mise à jour du contact #". $contact->getIdContact().'.'];
                        }
                        if ($count === count($contacts)) return $this->json($result);
                    }
                } catch(Exception $e) {
                    sleep(1);
                    continue;
                }
                break;
            } while (!$status);
        }
    }

}