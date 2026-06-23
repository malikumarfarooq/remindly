<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;

class ClientForm extends Component
{
    public ?int $clientId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $whatsapp = '';
    public string $company = '';
    public string $contactPerson = '';
    public string $currency = 'USD';
    public string $preferredChannel = 'auto';

    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $country = '';
    public string $postalCode = '';
    public string $taxNumber = '';
    public string $website = '';
    public string $notes = '';

    public bool $isActive = true;

    protected function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'whatsapp'         => 'nullable|string|max:20',
            'company'          => 'nullable|string|max:255',
            'contactPerson'    => 'nullable|string|max:255',
            'currency'         => 'required|string|size:3',
            'preferredChannel' => 'required|in:email,sms,whatsapp,auto',
            'address'          => 'nullable|string',
            'city'             => 'nullable|string|max:255',
            'state'            => 'nullable|string|max:255',
            'country'          => 'nullable|string|max:2',
            'postalCode'       => 'nullable|string|max:255',
            'taxNumber'        => 'nullable|string|max:255',
            'website'          => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ];
    }

    public function mount(?Client $client = null): void
    {
        if ($client && $client->exists) {
            $this->clientId        = $client->id;
            $this->name            = $client->name;
            $this->email           = $client->email ?? '';
            $this->phone           = $client->phone ?? '';
            $this->whatsapp        = $client->whatsapp ?? '';
            $this->company         = $client->company ?? '';
            $this->contactPerson   = $client->contact_person ?? '';
            $this->currency        = $client->currency;
            $this->preferredChannel = $client->preferred_channel;
            $this->address         = $client->address ?? '';
            $this->city            = $client->city ?? '';
            $this->state           = $client->state ?? '';
            $this->country         = $client->country ?? '';
            $this->postalCode      = $client->postal_code ?? '';
            $this->taxNumber       = $client->tax_number ?? '';
            $this->website         = $client->website ?? '';
            $this->notes           = $client->notes ?? '';
            $this->isActive        = $client->is_active;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'user_id'           => auth()->id(),
            'name'              => $this->name,
            'email'             => $this->email ?: null,
            'phone'             => $this->phone ?: null,
            'whatsapp'          => $this->whatsapp ?: null,
            'company'           => $this->company ?: null,
            'contact_person'    => $this->contactPerson ?: null,
            'currency'          => $this->currency,
            'preferred_channel' => $this->preferredChannel,
            'address'           => $this->address ?: null,
            'city'              => $this->city ?: null,
            'state'             => $this->state ?: null,
            'country'           => $this->country ?: null,
            'postal_code'       => $this->postalCode ?: null,
            'tax_number'        => $this->taxNumber ?: null,
            'website'           => $this->website ?: null,
            'notes'             => $this->notes ?: null,
            'is_active'         => $this->isActive,
        ];

        if ($this->clientId) {
            $client = Client::where('user_id', auth()->id())->findOrFail($this->clientId);
            $client->update($data);
            session()->flash('success', 'Client updated.');
        } else {
            $data['member_since'] = now();
            Client::create($data);
            session()->flash('success', 'Client created.');
        }

        $this->redirect(route('clients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.client-form');
    }
}
