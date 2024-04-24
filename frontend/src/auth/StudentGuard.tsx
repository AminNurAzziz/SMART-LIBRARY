import { useStudentStore } from '@/providers/auth.provider';
import { ReactNode, useState } from 'react';
import { isEmpty } from 'lodash';
import { Navigate, useLocation } from 'react-router';

type StudentGuardProps = {
  children: ReactNode;
};

export default function StudentGuard({ children }: Readonly<StudentGuardProps>) {
  const { user } = useStudentStore();

  const { pathname } = useLocation();

  const [requestedLocation, setRequestedLocation] = useState<string | null>(null);

  if (isEmpty(user?.profile)) {
    if (pathname !== requestedLocation) {
      setRequestedLocation(pathname);
    }
    return <Navigate to="/scan" />;
  }

  if (requestedLocation && pathname !== requestedLocation) {
    setRequestedLocation(null);
    return <Navigate to={requestedLocation} />;
  }

  return <> {children} </>;
}
