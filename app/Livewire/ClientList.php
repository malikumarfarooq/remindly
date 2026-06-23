<?php

namespace App\Livewire;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $riskFilter = 'all'; // all | low | medium | high
    public array $selected = [];
    public bool $selectAll = false;

    protected $queryString = ['search', 'riskFilter'];

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function setFilter(string $filter): void
    {
        $this->riskFilter = $filter;
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getQuery()
                ->get()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteClient(int $id): void
    {
        $client = Client::where('user_id', auth()->id())->findOrFail($id);
        $client->delete();

        session()->flash('success', 'Client deleted.');
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        Client::where('user_id', auth()->id())
            ->whereIn('id', $this->selected)
            ->delete();

        session()->flash('success', count($this->selected) . ' client(s) deleted.');
        $this->selected = [];
        $this->selectAll = false;
    }

    private function getQuery()
    {
        $query = Client::where('user_id', auth()->id());

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('company', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->riskFilter !== 'all') {
            $query->where('risk_level', $this->riskFilter);
        }

        return $query->latest();
    }

    public function render()
    {
        $clients = $this->getQuery()->paginate(10);

        $counts = [
            'all'    => Client::where('user_id', auth()->id())->count(),
            'low'    => Client::where('user_id', auth()->id())->where('risk_level', 'low')->count(),
            'medium' => Client::where('user_id', auth()->id())->where('risk_level', 'medium')->count(),
            'high'   => Client::where('user_id', auth()->id())->where('risk_level', 'high')->count(),
        ];

        return view('livewire.client-list', compact('clients', 'counts'));
    }
}
