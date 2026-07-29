import React from 'react';

interface NoRecordsMessageProps {
  message?: string;
}

export function NoRecordsMessage({ message = 'No Audit Records Found' }: NoRecordsMessageProps): JSX.Element {
  return (
    <section className="audit-message audit-message--empty" role="status">
      <p>{message}</p>
    </section>
  );
}
