import '../css/app.css';
import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// --- React setup ---

// Load React
import loadReactApp from './react-app.jsx';
loadReactApp();