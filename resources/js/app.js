import './bootstrap';
if (import.meta.env.DEV) {
    import('instruckt').then(({ init }) => init({ endpoint: '/instruckt', adapters: ['livewire', 'blade'] }));
}
