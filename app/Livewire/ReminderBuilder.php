<?php

namespace App\Livewire;

use App\Models\ReminderSequence;
use App\Models\ReminderStep;
use Livewire\Component;

class ReminderBuilder extends Component
{
    public ?int $sequenceId = null;
    public string $name = '';
    public bool $isDefault = false;
    public bool $isActive = true;

    public array $steps = [];

    protected function rules(): array
    {
        return [
            'name'                      => 'required|string|max:255',
            'steps'                     => 'required|array|min:1',
            'steps.*.label'             => 'required|string|max:255',
            'steps.*.offset_days'       => 'required|integer',
            'steps.*.offset_from'       => 'required|in:due_date,issue_date',
            'steps.*.tone'              => 'required|in:friendly,professional,firm,final,demand',
            'steps.*.channel'           => 'required|in:email,sms,whatsapp,auto',
            'steps.*.subject_template'  => 'nullable|string',
            'steps.*.body_template'     => 'nullable|string',
        ];
    }

    public function mount(?ReminderSequence $sequence = null): void
    {
        if ($sequence && $sequence->exists) {
            $this->sequenceId = $sequence->id;
            $this->name       = $sequence->name;
            $this->isDefault  = $sequence->is_default;
            $this->isActive   = $sequence->is_active;

            $this->steps = $sequence->steps->map(fn($step) => [
                'label'            => $step->label,
                'offset_days'      => $step->days_offset,
                'offset_from'      => $step->offset_from,
                'tone'             => $step->tone,
                'channel'          => $step->channel,
                'subject_template' => $step->subject_template ?? '',
                'body_template'    => $step->body_template ?? '',
                'ai_generate'      => $step->ai_generate,
                'is_active'        => $step->is_active,
            ])->toArray();
        } else {
            $this->steps = [$this->defaultStep(1)];
        }
    }

    private function defaultStep(int $num): array
    {
        $presets = [
            1 => ['label' => '3 Days Before Due',  'offset_days' => -3,  'tone' => 'friendly'],
            2 => ['label' => 'On Due Date',         'offset_days' => 0,   'tone' => 'professional'],
            3 => ['label' => '3 Days After Due',    'offset_days' => 3,   'tone' => 'firm'],
            4 => ['label' => '7 Days After Due',    'offset_days' => 7,   'tone' => 'firm'],
            5 => ['label' => '14 Days After Due',   'offset_days' => 14,  'tone' => 'final'],
            6 => ['label' => 'Final Notice (30d)',  'offset_days' => 30,  'tone' => 'demand'],
        ];

        $p = $presets[$num] ?? ['label' => 'Step ' . $num, 'offset_days' => $num * 7, 'tone' => 'professional'];

        return [
            'label'            => $p['label'],
            'offset_days'      => $p['offset_days'],
            'offset_from'      => 'due_date',
            'tone'             => $p['tone'],
            'channel'          => 'auto',
            'subject_template' => '',
            'body_template'    => '',
            'ai_generate'      => true,
            'is_active'        => true,
        ];
    }

    public function addStep(): void
    {
        $this->steps[] = $this->defaultStep(count($this->steps) + 1);
    }

    public function removeStep(int $index): void
    {
        if (count($this->steps) > 1) {
            array_splice($this->steps, $index, 1);
        }
    }

    public function moveUp(int $index): void
    {
        if ($index > 0) {
            [$this->steps[$index - 1], $this->steps[$index]] =
                [$this->steps[$index], $this->steps[$index - 1]];
        }
    }

    public function moveDown(int $index): void
    {
        if ($index < count($this->steps) - 1) {
            [$this->steps[$index], $this->steps[$index + 1]] =
                [$this->steps[$index + 1], $this->steps[$index]];
        }
    }

    public function save(): void
    {
        $this->validate();

        $userId = auth()->id();

        if ($this->isDefault) {
            ReminderSequence::where('user_id', $userId)
                ->update(['is_default' => false]);
        }

        if ($this->sequenceId) {
            $sequence = ReminderSequence::where('user_id', $userId)
                ->findOrFail($this->sequenceId);
            $sequence->update([
                'name'       => $this->name,
                'is_default' => $this->isDefault,
                'is_active'  => $this->isActive,
            ]);
        } else {
            $sequence = ReminderSequence::create([
                'user_id'    => $userId,
                'name'       => $this->name,
                'is_default' => $this->isDefault,
                'is_active'  => $this->isActive,
            ]);
        }

        // Delete old steps, insert new
        $sequence->steps()->delete();

        foreach ($this->steps as $i => $s) {
            ReminderStep::create([
                'reminder_sequence_id' => $sequence->id,
                'step_number'          => $i + 1,
                'days_offset'          => $s['offset_days'],
                'offset_from'          => $s['offset_from'],
                'label'                => $s['label'],
                'tone'                 => $s['tone'],
                'channel'              => $s['channel'],
                'subject_template'     => $s['subject_template'] ?: null,
                'body_template'        => $s['body_template'] ?: null,
                'ai_generate'          => $s['ai_generate'],
                'is_active'            => $s['is_active'],
            ]);
        }

        session()->flash('success', $this->sequenceId ? 'Sequence updated.' : 'Sequence created.');
        $this->redirect(route('reminders.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.reminder-builder');
    }
}
