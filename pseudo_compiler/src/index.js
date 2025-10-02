import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';

// 导入Font Awesome
import '@fortawesome/fontawesome-free/css/all.min.css';

// 导入Prism.js样式
import 'prismjs/themes/prism.css';
import 'prismjs';
import 'prismjs/components/prism-javascript';

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);