<?php

namespace App\Service;

class ApiService
{

    public function getAllDepartments(): array
    {
        // on récupère l'url
        $url = 'https://geo.api.gouv.fr/departements';
        
        // on initialise une session cURL
        $ch = curl_init($url);
        // on indique que l'on veut récupérer le résultat sous forme de chaîne de caractères
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        // on gère les erreurs cURL
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            dump('Erreur cURL:', $error);
            curl_close($ch);
            throw new \Exception('Erreur lors de l\'appel à l\'API : ' . $error);
        }

        // on ferme la session cURL et on décode la réponse JSON
        curl_close($ch);
        $data = json_decode($response, true);

        return $data ?? [];
    }

    // public function getModelsByMake(string $make): array
    // {
    //     // on récupère l'url
    //     $url = $this->baseUrl . 'GetModelsForMake/' . urlencode($make) . '?format=json';

    //     // on initialise une session cURL
    //     $ch = curl_init($url);
    //     // on indique que l'on veut récupérer le résultat sous forme de chaîne de caractères
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     $response = curl_exec($ch);

    //     // on gère les erreurs cURL
    //     if (curl_errno($ch)) {
    //         curl_close($ch);
    //         throw new \Exception('Erreur lors de l’appel à l’API NHTSA : ' . curl_error($ch));
    //     }

    //     // on ferme la session cURL et on décode la réponse JSON
    //     curl_close($ch);
    //     $data = json_decode($response, true);

    //     return $data['Results'] ?? [];
    // }
}