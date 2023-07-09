<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class ZipCodesService
{
    public function getAllCodesPostauxRegion(): array
    {
        $client = HttpClient::create();
        $url = 'https://geo.api.gouv.fr/communes?codeRegion=11&fields=codesPostaux';
        $response = $client->request('GET', $url);
        $dataRaw = $response->getContent();
        $data = json_decode($dataRaw, true);
        $modifiedData = array_map(function ($item) {
            $codesPostaux = $item['codesPostaux'];
            $nom = $item['nom'];
            // Создание новых объектов для каждого индекса
            $result = [];
            if (count($codesPostaux) > 1) {
                foreach ($codesPostaux as $codePostal) {
                    $result[] = [$codePostal . ' | ' . $nom => $codePostal . ' | ' . $nom];
                }
            } else {
                $result[] = [$codesPostaux[0] . ' | ' . $nom => $codesPostaux[0] . ' | ' . $nom];
            }
            return $result;
        }, $data);

        return array_reduce(array_merge(...$modifiedData), function ($carry, $item) {
            return $carry + $item;
        }, ['' => '']);
    }

    public function getAllCodesPostaux(): array
    {
        $client = HttpClient::create();
        $url = 'https://geo.api.gouv.fr/communes?&fields=codesPostaux';
        $response = $client->request('GET', $url);
        $dataRaw = $response->getContent();
        $data = json_decode($response->getContent(), true);
        $modifiedData = array_map(function ($item) {
            $codesPostaux = $item['codesPostaux'];
            $nom = $item['nom'];
            // Создание новых объектов для каждого индекса
            $result = [];
            foreach ($codesPostaux as $codePostal) {
                $result[] = [$codePostal . ' | ' . $nom => $codePostal . ' | ' . $nom];
            }
            return $result;
        }, $data);

        return array_reduce(array_merge(...$modifiedData), function ($carry, $item) {
            return $carry + $item;
        }, ['' => '']);
    }
}
