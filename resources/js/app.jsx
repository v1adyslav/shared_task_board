import './bootstrap';
import Alpine from 'alpinejs';
import { createRoot } from 'react-dom/client';
import TaskBoardApp from './task-board/TaskBoardApp';

window.Alpine = Alpine;
Alpine.start();

const rootElement = document.getElementById('task-board-app');

if (rootElement && window.taskBoardConfig?.board) {
    createRoot(rootElement).render(<TaskBoardApp config={window.taskBoardConfig} />);
}
