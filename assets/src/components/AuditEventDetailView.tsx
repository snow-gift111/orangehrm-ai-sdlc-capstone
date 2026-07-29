import React from 'react';
import type { AuditEventDetail } from '../types/audit';

interface AuditEventDetailViewProps {
  detail: AuditEventDetail;
  onBack: () => void;
}

function formatTimestamp(value: string): string {
  return new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'medium'
  }).format(new Date(value));
}

export function AuditEventDetailView({ detail, onBack }: AuditEventDetailViewProps): JSX.Element {
  return (
    <section className="audit-card" aria-labelledby="audit-detail-title">
      <div className="audit-card__header">
        <div>
          <p className="audit-eyebrow">Audit Event Detail</p>
          <h2 id="audit-detail-title">{detail.actionType} Event #{detail.auditEventId}</h2>
        </div>
        <button type="button" className="audit-button audit-button--secondary" onClick={onBack}>
          Back to Audit History
        </button>
      </div>

      <dl className="audit-detail-grid">
        <div>
          <dt>Employee ID</dt>
          <dd>{detail.employeeId}</dd>
        </div>
        <div>
          <dt>Employee Name</dt>
          <dd>{detail.employeeName ?? 'Not available'}</dd>
        </div>
        <div>
          <dt>Action Type</dt>
          <dd>{detail.actionType}</dd>
        </div>
        <div>
          <dt>Changed By</dt>
          <dd>{detail.changedBy}</dd>
        </div>
        <div>
          <dt>Timestamp</dt>
          <dd>{formatTimestamp(detail.timestamp)}</dd>
        </div>
        <div className="audit-detail-grid__wide">
          <dt>Summary</dt>
          <dd>{detail.summary ?? 'No summary available.'}</dd>
        </div>
      </dl>

      <h3>Captured Values</h3>
      {detail.changes.length === 0 ? (
        <p className="audit-muted">No field-level values were captured for this event.</p>
      ) : (
        <table className="audit-table">
          <thead>
            <tr>
              <th scope="col">Field</th>
              <th scope="col">Previous Value</th>
              <th scope="col">New Value</th>
            </tr>
          </thead>
          <tbody>
            {detail.changes.map((change) => (
              <tr key={`${change.fieldName}-${change.fieldLabel}`}>
                <td>{change.fieldLabel}</td>
                <td>{change.previousValue ?? '—'}</td>
                <td>{change.newValue ?? '—'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </section>
  );
}
