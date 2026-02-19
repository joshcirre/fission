<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component {
    //
};

?>

<div>
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Welcome to your new dashboard</flux:heading>
            <flux:subheading>
                Let's get started in the
                <flux:link href="/playground">playground.</flux:link>
            </flux:subheading>
        </div>
    </flux:card>
</div>
