import { Navigate, useRoutes } from 'react-router-dom';
// auth
import AuthGuard from '../auth/AuthGuard';
import GuestGuard from '../auth/GuestGuard';
// layouts
import CompactLayout from '../layouts/compact';
import ServiceLayout from '../layouts/service';
import GuestLayout from '../layouts/guest';
// config
import { PATH_AFTER_LOGIN } from '../config-global';
//
import {
  Page404,
  PageOne,
  PageTwo,
  PageSix,
  PageFour,
  PageFive,
  LoginPage,
  PageThree,
  PaymentHistory,
} from './elements';

// ----------------------------------------------------------------------

export default function Router() {
  return useRoutes([
    {
      path: '/',
      children: [
        { element: <Navigate to={PATH_AFTER_LOGIN} replace />, index: true },
        {
          path: 'login',
          element: (
            <GuestGuard>
              <LoginPage />
            </GuestGuard>
          ),
        },
      ],
    },
    {
      path: '/test',
      element: (
        <GuestGuard>
          <GuestLayout />
        </GuestGuard>
      ),
      children: [
        {
          path: 'one',
          element: <PageFour />,
        },
      ],
    },
    {
      path: '/dashboard',
      element: (
        <AuthGuard>
          <ServiceLayout />
        </AuthGuard>
      ),
      children: [
        { element: <Navigate to={PATH_AFTER_LOGIN} replace />, index: true },
        { path: 'one', element: <PageOne /> },
        { path: 'two', element: <PageTwo /> },
        { path: 'three', element: <PageThree /> },
        {
          path: 'user',
          children: [
            { element: <Navigate to="/dashboard/user/four" replace />, index: true },
            { path: 'four', element: <PageFour /> },
            { path: 'five', element: <PageFive /> },
            { path: 'six', element: <PageSix /> },
          ],
        },
      ],
    },
    {
      path: '/payment',
      element: (
        <AuthGuard>
          <ServiceLayout />
        </AuthGuard>
      ),
      children: [{ path: 'history', element: <PaymentHistory /> }],
    },
    {
      element: <CompactLayout />,
      children: [{ path: '404', element: <Page404 /> }],
    },
    { path: '*', element: <Navigate to="/404" replace /> },
  ]);
}
