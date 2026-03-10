import './bootstrap';
import { init as instruckt } from 'instruckt';

instruckt({ endpoint: '/instruckt', adapters: ['livewire', 'blade'] });
