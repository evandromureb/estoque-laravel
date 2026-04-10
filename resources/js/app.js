import './bootstrap';

import Alpine from 'alpinejs';
import { registerCrudAlpine } from './crud/register';

registerCrudAlpine(Alpine);

window.Alpine = Alpine;

Alpine.start();
