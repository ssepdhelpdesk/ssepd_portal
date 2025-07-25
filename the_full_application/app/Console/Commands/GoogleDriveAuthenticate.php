<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client;

class GoogleDriveAuthenticate extends Command
{
    protected $signature = 'gdrive:auth';
    protected $description = 'Authenticate Google Drive and get access token';

    public function handle()
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $client->setScopes(['https://www.googleapis.com/auth/drive']);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        $this->info("Open the following URL in your browser:\n\n$authUrl\n");
        
        $authCode = $this->ask('Paste the authorization code here');
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        file_put_contents(storage_path('app/gdrive-token.json'), json_encode($accessToken));
        $this->info('✅ Access token saved to storage/app/gdrive-token.json');
    }
}