import React from 'react';

interface AccessDeniedMessageProps {
  message?: string;
}

export function AccessDeniedMessage({ message = 'Access denied. You are not authorized to view employee audit history.' }: AccessDeniedMessageProps): JSX.Element {
  return (
    <section className="audit-message audit-message--error" role="alert">
      <h2>Access Denied</h2>
      <p>{message}</p>
    </section>
  );
}
