<?php

namespace App\Services;

use SoapClient;

class ViesService
{
    /**
     * Validate NIF/VAT number using VIES SOAP service.
     *
     * @param string $nif
     * @param string $countryCode
     * @return array<string, mixed>|null
     */
    public function validateNif(string $nif, string $countryCode = 'PT'): ?array
    {
        try {
            $soapUrl = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);
            
            $client = new SoapClient($soapUrl, [
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'exceptions' => true
            ]);
            
            $result = $client->checkVat([
                'countryCode' => $countryCode,
                'vatNumber' => $nif
            ]);
            
            if ($result->valid) {
                return [
                    'valid' => true,
                    'name' => $result->name ?? '',
                    'address' => $result->address ?? '',
                    'countryCode' => $result->countryCode ?? $countryCode,
                    'vatNumber' => $result->vatNumber ?? $nif,
                    'requestDate' => $result->requestDate ?? null,
                ];
            }
            
            return ['valid' => false];
        } catch (\SoapFault $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }
}

