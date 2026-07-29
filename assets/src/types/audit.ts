export interface AuditHistoryItem {
  auditEventId: number;
  employeeId: string;
  employeeName: string | null;
  actionType: 'Create' | 'Update' | 'Delete' | string;
  changedBy: string;
  timestamp: string;
  summary: string | null;
}

export interface AuditChangeDetail {
  fieldName: string;
  fieldLabel: string;
  previousValue: string | null;
  newValue: string | null;
}

export interface AuditEventDetail extends AuditHistoryItem {
  changes: AuditChangeDetail[];
}

export interface ApiErrorResponse {
  error?: {
    code: string;
    message: string;
  };
}
