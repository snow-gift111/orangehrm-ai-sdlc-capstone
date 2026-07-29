import React from 'react';
import { createRoot } from 'react-dom/client';
import { EmployeeAuditHistoryView } from './components/EmployeeAuditHistoryView';
import './styles.css';

const rootElement = document.getElementById('root');

if (rootElement === null) {
  throw new Error('Application root element was not found.');
}

const params = new URLSearchParams(window.location.search);
const employeeIdentifier = params.get('employeeIdentifier') ?? params.get('employeeId') ?? '0001';

createRoot(rootElement).render(
  <React.StrictMode>
    <main className="audit-app">
      <EmployeeAuditHistoryView employeeIdentifier={employeeIdentifier} />
    </main>
  </React.StrictMode>
);
