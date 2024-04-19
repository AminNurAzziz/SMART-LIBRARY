// import { createContext, useEffect, useCallback, useMemo } from 'react';
// // utils
// import { getAuthToken, isValidToken } from './utils';
// import { JWTContextType } from './types';
// import { useGetProfile } from '../api/services/auth-api';
// import { useAuthStore } from '../providers/auth.provider';
//
// // ----------------------------------------------------------------------
//
// export const AuthContext = createContext<JWTContextType | null>(null);
//
// // ----------------------------------------------------------------------
//
// type AuthProviderProps = {
//   children: React.ReactNode;
// };
//
// export function AuthProvider({ children }: AuthProviderProps) {
//   const { refetch: fetchProfile } = useGetProfile();
//   const { setInitialized, isInitialized } = useAuthStore();
//   const initialize = useCallback(async () => {
//     try {
//       const accessToken = getAuthToken();
//
//       if (accessToken && isValidToken(accessToken)) {
//         await fetchProfile();
//         setInitialized(true);
//       }
//     } catch (error) {
//       console.error(error);
//       setInitialized(false);
//     }
//   }, []);
//
//   useEffect(() => {
//     initialize();
//   }, [initialize]);
//
//   const memoizedValue = useMemo(
//     () => ({
//       isInitialized: isInitialized,
//     }),
//     [isInitialized],
//   );
//
//   return <AuthContext.Provider value={memoizedValue}>{children}</AuthContext.Provider>;
// }
