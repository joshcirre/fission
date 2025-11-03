<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    public function mount()
    {
        info('this component has been loaded');
    }
};

?>

<div>
    <div>
        <flux:button data-pan="playground-button">Hi everyone</flux:button>
    </div>
</div>
