import type { AuditEventDetail, AuditHistoryItem, ApiErrorResponse } from '../types/audit';

interface ListResponse<T> extends ApiErrorResponse {
  data: T[];
  message?: string | null;
}

interface ItemResponse<T> extends ApiErrorResponse {
  data: T;
}

const defaultHeaders: HeadersInit = {
  Accept: 'application/json'
};

async function parseResponse<T>(response: Response): Promise<T> {
  const payload = (await response.json().catch(() => ({}))) as ApiErrorResponse;

  if (!response.ok) {
    throw new Error(payload.error?.message ?? `Request failed with status ${response.status}`);
  }

  return payload as T;
}

export async function fetchEmployeeAuditHistory(employeeIdentifier: string): Promise<ListResponse<AuditHistoryItem>> {
  const response = await fetch(`/api/v1/pim/employees/${encodeURIComponent(employeeIdentifier)}/audit-events`, {
    method: 'GET',
    headers: defaultHeaders,
    credentials: 'same-origin'
  });

  return parseResponse<ListResponse<AuditHistoryItem>>(response);
}

export async function fetchAuditEventDetail(auditEventId: number): Promise<AuditEventDetail> {
  const response = await fetch(`/api/v1/pim/audit-events/${auditEventId}`, {
    method: 'GET',
    headers: defaultHeaders,
    credentials: 'same-origin'
  });

  const parsed = await parseResponse<ItemResponse<AuditEventDetail>>(response);
  return parsed.data;
}
