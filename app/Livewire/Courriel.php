<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Mary\Traits\Toast;

class Courriel extends Component
{
    use Toast;

    public $state = [];
    public $updateSMTP_info_modal = false;

    public function mount()
    {
        // Populate the form fields from the .env file
        $this->state = [
            'mail_mailer' => env('MAIL_MAILER'),
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT'),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mail_encryption' => env('MAIL_ENCRYPTION'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS'),
            'mail_from_name' => env('MAIL_FROM_NAME'),
        ];
    }

    public function save()
    {
        $this->validate([
            'state.mail_mailer' => 'required|string',
            'state.mail_host' => 'required|string',
            'state.mail_port' => 'required|integer',
            'state.mail_username' => 'required|string',
            'state.mail_password' => 'required|string',
            'state.mail_encryption' => 'nullable|string',
            'state.mail_from_address' => 'required|email',
            'state.mail_from_name' => 'required|string',
        ]);

        foreach ($this->state as $key => $value) {
            $envKey = strtoupper("MAIL_" . str_replace('mail_', '', $key));
            $this->setEnv($envKey, $value);
        }

        Artisan::call('config:cache');

        $this->success('Saved successfully');
        $this->updateSMTP_info_modal = false;
    }

    protected function setEnv($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $content)) {
                // Replace existing key
                $content = preg_replace($pattern, $replacement, $content);
            } else {
                // Append new key
                $content .= PHP_EOL . $replacement;
            }

            file_put_contents($path, $content);
        }
    }

    public function render()
    {
        return view('livewire.courriel');
    }
}
