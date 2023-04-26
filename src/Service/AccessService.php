<?php
namespace App\Service;

class AccessService
{
    public function getAllowedIPs(): array
    {
        return ['127.0.0.1'];
    }
}