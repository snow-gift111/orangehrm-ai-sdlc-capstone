import React, { useEffect, useState } from 'react';
import { fetchAuditEventDetail, fetchEmployeeAuditHistory } from '../api/auditApi';
import type { AuditEventDetail, AuditHistoryItem } from '../types/audit';
import { AccessDeniedMessage } from './AccessDeniedMessage';
import { AuditEventDetailView } from './AuditEventDetailView';
import { NoRecordsMessage } from './NoRecordsMessage';

interface EmployeeAuditHistoryViewProps {
  employeeIdentifier: string;
}

function formatTimestamp(value: string): string {
  return new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
    timeStyle: 'medium'
  }).format(new Date(value));
}

export function EmployeeAuditHistoryView({ employeeIdentifier }: EmployeeAuditHistoryViewProps): JSX.Element {
  const [items, setItems] = useState<AuditHistoryItem[]>([]);
  const [selectedDetail, setSelectedDetail] = useState<AuditEventDetail | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [detailLoading, setDetailLoading] = useState<boolean>(false);
  const [message, setMessage] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let active = true;

    setLoading(true);
    setError(null);
    setMessage(null);
    setSelectedDetail(null);

    fetchEmployeeAuditHistory(employeeIdentifier)
      .then((response) => {
        if (!active) {
          return;
        }
        setItems(response.data);
        setMessage(response.message ?? null);
      })
      .catch((err: unknown) => {
        if (!active) {
          return;
        }
        setError(err instanceof Error ? err.message : 'Unable to load employee audit history.');
      })
      .finally(() => {
        if (active) {
          setLoading(false);
        }
      });

    return () => {
      active = false;
    };
  }, [employeeIdentifier]);

  async function handleSelectEvent(auditEventId: number): Promise<void> {
    setDetailLoading(true);
    setError(null);

    try {
      const detail = await fetchAuditEventDetail(auditEventId);
      setSelectedDetail(detail);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to load audit event detail.');
    } finally {
      setDetailLoading(false);
    }
  }

  if (error !== null && error.toLowerCase().includes('access denied')) {
    return <AccessDeniedMessage message={error} />;
  }

  if (selectedDetail !== null) {
    return <AuditEventDetailView detail={selectedDetail} onBack={() => setSelectedDetail(null)} />;
  }

  return (
    <section className="audit-card" aria-labelledby="audit-history-title">
      <div className="audit-card__header">
        <div>
          <p className="audit-eyebrow">PIM</p>
          <h1 id="audit-history-title">Employee Audit History</h1>
          <p className="audit-muted">Employee Identifier: {employeeIdentifier}</p>
        </div>
      </div>

      {loading ? <p role="status">Loading audit history…</p> : null}
      {detailLoading ? <p role="status">Loading audit event detail…</p> : null}
      {error !== null ? <p className="audit-message audit-message--error" role="alert">{error}</p> : null}

      {!loading && items.length === 0 ? <NoRecordsMessage message={message ?? undefined} /> : null}

      {!loading && items.length > 0 ? (
        <table className="audit-table">
          <thead>
            <tr>
              <th scope="col">Action Type</th>
              <th scope="col">Changed By</th>
              <th scope="col">Timestamp</th>
              <th scope="col">Summary</th>
              <th scope="col">Details</th>
            </tr>
          </thead>
          <tbody>
            {items.map((item) => (
              <tr key={item.auditEventId}>
                <td><span className="audit-badge">{item.actionType}</span></td>
                <td>{item.changedBy}</td>
                <td>{formatTimestamp(item.timestamp)}</td>
                <td>{item.summary ?? 'No summary available.'}</td>
                <td>
                  <button
                    type="button"
                    className="audit-button"
                    onClick={() => void handleSelectEvent(item.auditEventId)}
                  >
                    View Details
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      ) : null}
    </section>
  );
}
