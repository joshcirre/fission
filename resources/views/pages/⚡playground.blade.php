<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    public function mount(): void
    {
        info('this component has been loaded');
    }
};

?>

<div>
    <div>
        <flux:button variant="primary">Hi everyone</flux:button>
    </div>
</div>
