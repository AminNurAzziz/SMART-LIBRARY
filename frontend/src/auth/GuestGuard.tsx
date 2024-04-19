import { ReactNode } from 'react';
import { Navigate } from 'react-router-dom';
// components
import LoadingScreen from '../components/loading-screen';
//
// import { useAuthContext } from './useAuthContext';
import { useAuthStore } from '../providers/auth.provider';

// ----------------------------------------------------------------------

type GuestGuardProps = {
  readonly children: ReactNode;
};

export default function GuestGuard({ children }: GuestGuardProps) {
  const { user } = useAuthStore();

  if (user?.isAuthenticated) {
    return <Navigate to="/dashboard" />;
  }
  //
  // if (!isInitialized) {
  //   return <LoadingScreen />;
  // }

  return <> {children} </>;
}
